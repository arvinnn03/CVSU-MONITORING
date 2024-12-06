<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Visitor;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;
use DataTables;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class VisitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Set default timezone to Philippines
        date_default_timezone_set('Asia/Manila');
    }

    public function sub_visitor()
    {
        return view('gate1_visitor');
    }

    // public function advance_sched()
    // {
        
    //     $data = Department::all();
    //     return view('advance_sched', compact('data'));
    // }

    public function index()
    {
        // Fetch visitor statistics
        $totalTodayVisits = Visitor::whereDate('created_at', Carbon::today())->count();
        $totalLast7DaysVisits = Visitor::whereDate('created_at', '>=', Carbon::now()->subDays(7))->count();
       
        // Pass the data to the view
        return view('visitor', compact(
            'totalTodayVisits',
            'totalLast7DaysVisits',
        ));
    }

    public function fetch_all(Request $request)
    {
        if ($request->ajax()) {
            $query = Visitor::leftJoin('users', 'users.id', '=', 'visitors.visitor_enter_by')
                ->select([
                    'visitors.visitor_name',
                    'visitors.visitor_meet_person_name',
                    'visitors.visitor_department',
                    'visitors.visitor_enter_time',
                    'visitors.visitor_out_time',
                    'visitors.visitor_status',
                    'users.name', // This is the user who checked in
                    'visitors.visitor_image',
                    'visitors.id',
                    'visitors.visitor_enter_out_by' // Ensure this line is present
                ])
                ->orderBy('visitors.updated_at', 'desc');

            // Apply filtering based on the user's type
            if (Auth::user()->type == 'User1' || Auth::user()->type == 'User2' || Auth::user()->type == 'User3') {
                $query->where('visitors.visitor_enter_by', '=', Auth::user()->id);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('visitor_enter_out_by', function($row) {
                    return $row->visitor_enter_out_by ? User::find($row->visitor_enter_out_by)->name : 'N/A'; // Fetch the user's name
                })
                ->editColumn('visitor_status', function($row) {
                    return $row->visitor_status == 'In'
                        ? '<span class="badge bg-success">In</span>'
                        : '<span class="badge bg-danger">Out</span>';
                })
                ->escapeColumns('visitor_status')
                ->editColumn('visitor_enter_time', function($row) {
                    return $row->visitor_enter_time ? Carbon::parse($row->visitor_enter_time)->timezone('Asia/Manila')->format('m/d/Y h:i A') : 'N/A';
                })
                ->editColumn('visitor_out_time', function($row) {
                    return $row->visitor_out_time ? Carbon::parse($row->visitor_out_time)->timezone('Asia/Manila')->format('m/d/Y h:i A') : 'N/A';
                })
                ->addColumn('visitor_image', function($row) {
                    $imageUrl = $row->visitor_image ? asset('storage/' . $row->visitor_image) : 'https://via.placeholder.com/100';
                    return '<button class="btn btn-outline-primary btn-sm view-image" data-image="' . $imageUrl . '"><i class="bi bi-eye"></i> View</button>';
                })
                ->addColumn('action', function($row) {
                    $actions = '';
                    // Only include delete button if user is admin
                    if (Auth::user()->type == 'Admin') {
                        $actions .= '<button type="button" class="btn btn-outline-danger btn-sm mx-1 delete" data-id="' . $row->id . '" title="Delete" data-toggle="tooltip" data-placement="top"><i class="fas fa-trash-alt"></i></button>';
                    }
                    $editBtn = '<a href="/visitor/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm mx-1" title="Edit" data-toggle="tooltip" data-placement="top"><i class="fas fa-pencil-alt"></i></a>';
                    $printBtn = '<a href="' . route('visitor.print', $row->id) . '" class="btn btn-outline-secondary btn-sm mx-1" target="_blank" title="Print" data-toggle="tooltip" data-placement="top"><i class="fas fa-print"></i></a>';
                    $viewBtn = '<a href="/visitor/view/' . $row->id . '" class="btn btn-outline-dark btn-sm mx-1" title="View" data-toggle="tooltip" data-placement="top"><i class="fas fa-eye"></i></a>'; // Updated icon class
    
                    if ($row->visitor_status == 'In') {
                        $actions .= $editBtn . $printBtn;
                    } else {
                        $actions .= $viewBtn . $printBtn . $editBtn;
                    }
                    return $actions;
                })
                ->rawColumns(['action', 'visitor_image'])
                ->make(true);
        }
    }
    
    public function add()
    {
        
        $data = Department::all();
        return view('add_visitor', compact('data'));
    }

    public function add_validation(Request $request)
    {

        // Validate the form data
        $request->validate([
            'visitor_name' => 'required',
            'visitor_email' => 'required|email',
            'visitor_mobile_no' => 'required',
            'visitor_meet_person_name' => 'required',
            'visitor_department' => 'required',
            'visitor_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'visitor_reason_to_meet' => 'required',
            'visitor_enter_time' => 'nullable', 'regex:/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2} (AM|PM)$/', // Make it nullable
            'visitor_out_time' => [
                'nullable',
                'regex:/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2} (AM|PM)$/'
            ],
            'visitor_status' => 'nullable',
            'visitor_enter_by' => 'required',
        ]);

        $imagePath = null;
        if ($request->hasFile('visitor_image')) {
            $image = $request->file('visitor_image');
            $imagePath = $image->store('images', 'public'); // Stores the image in 'storage/app/public/images'
        }

        // Convert enter time if provided
        $enterTimeFormatted = null;
        if ($request->input('visitor_enter_time')) {
            $enterTime = Carbon::createFromFormat('m/d/Y h:i A', $request->input('visitor_enter_time'), 'Asia/Manila');
            $enterTimeFormatted = $enterTime ? $enterTime->format('Y-m-d H:i:s') : null;
        }

        // Convert exit time if provided
        $exitTime = $request->input('visitor_out_time') ? Carbon::createFromFormat('m/d/Y h:i A', $request->input('visitor_out_time'), 'Asia/Manila') : null;
        $exitTimeFormatted = $exitTime ? $exitTime->format('Y-m-d H:i:s') : null;

        // Save the visitor entry with converted times
        Visitor::create([
            'visitor_name' => $request->input('visitor_name'),
            'visitor_email' => $request->input('visitor_email'),
            'visitor_mobile_no' => $request->input('visitor_mobile_no'),
            'visitor_meet_person_name' => $request->input('visitor_meet_person_name'),
            'visitor_department' => $request->input('visitor_department'),
            'visitor_image' => $imagePath,
            'visitor_reason_to_meet' => $request->input('visitor_reason_to_meet'),
            'visitor_enter_time' => $enterTimeFormatted, // Use converted enter time
            'visitor_out_time' => $exitTimeFormatted, // Use converted exit time
            'visitor_status' => $request->input('visitor_status'),
            'visitor_enter_by' => $request->input('visitor_enter_by')
        ]);

        return redirect('visitor')->with('success', 'New visitor added');
    }

    public function add_visitor_validation(Request $request)
    {
    // Validate the form data
    $validatedData = $request->validate([
        'visitor_name' => 'required|string|max:255',
        'visitor_email' => 'required|email',
        'visitor_mobile_no' => 'required',
        'visitor_meet_person_name' => 'required|string|max:255',
        'visitor_department' => 'required|string|max:255',
        'visitor_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'visitor_reason_to_meet' => 'required|string|max:255',
        'visitor_enter_time' => 'nullable','regex:/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2} (AM|PM)$/', // Make it nullable
        'visitor_out_time' => [
            'nullable',
            'regex:/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2} (AM|PM)$/'
        ],
        'visitor_status' => 'nullable|string',
        'visitor_enter_by' => 'required|string',
    ]);

    // Generate a unique token
    $uniqueToken = Str::uuid()->toString();

    // Handle image upload
    $imagePath = null;
    if ($request->hasFile('visitor_image')) {
        $image = $request->file('visitor_image');
        $imagePath = $image->store('images', 'public'); // Stores the image in 'storage/app/public/images'
    }

    // Convert enter time if provided
    $enterTimeFormatted = null;
    if ($request->input('visitor_enter_time')) {
        $enterTime = Carbon::createFromFormat('m/d/Y h:i A', $request->input('visitor_enter_time'), 'Asia/Manila');
        $enterTimeFormatted = $enterTime ? $enterTime->format('Y-m-d H:i:s') : null;
    }

    // Convert exit time if provided
    $exitTime = $request->input('visitor_out_time') ? Carbon::createFromFormat('m/d/Y h:i A', $request->input('visitor_out_time'), 'Asia/Manila') : null;
    $exitTimeFormatted = $exitTime ? $exitTime->format('Y-m-d H:i:s') : null;

    // Save the visitor entry with converted times
    $visitor = Visitor::create([
        'visitor_name' => $request->input('visitor_name'),
        'visitor_email' => $request->input('visitor_email'),
        'visitor_mobile_no' => $request->input('visitor_mobile_no'),
        'visitor_meet_person_name' => $request->input('visitor_meet_person_name'),
        'visitor_department' => $request->input('visitor_department'),
        'visitor_image' => $imagePath,
        'visitor_reason_to_meet' => $request->input('visitor_reason_to_meet'),
        'visitor_enter_time' => $enterTimeFormatted, // Use converted enter time
        'visitor_out_time' => $exitTimeFormatted, // Use converted exit time
        'visitor_status' => $request->input('visitor_status'),
        'visitor_enter_by' => $request->input('visitor_enter_by'),
        'unique_token' => $uniqueToken // Save the unique token
    ]);

    // Redirect with success message
    return redirect()->route('visitor.print', $visitor->id)
        ->with('success', 'New visitor added');
}

    public function visitor()
    {
        // Calculate statistics
        $totalTodayVisits = Visitor::whereDate('created_at', Carbon::today())->count();
        $totalLast7DaysVisits = Visitor::whereDate('created_at', '>=', Carbon::now()->subDays(7))->count();
        
        // Pass the data to the dashboard view
        return view('visitor/add', compact(
            'totalTodayVisits',
            'totalLast7DaysVisits',
        ));
    }

    public function delete($id)
    {
        if (Auth::user()->type != 'Admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        try {
            $visitor = Visitor::findOrFail($id);
            $visitor->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $data = Visitor::findOrFail($id);
        return view('edit_visitor', compact('data'));
    }

    public function edit_validation(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'row_id' => 'required|integer|exists:visitors,id',
            'visitor_name' => 'required|string|max:255',
            'visitor_status' => 'required|in:In,Out',
            'visitor_out_time' => [
                'nullable',
                'regex:/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2} (AM|PM)$/'
            ],
        ]);

        // Convert exit time if provided
        $exitTime = $request->input('visitor_out_time') 
            ? Carbon::createFromFormat('m/d/Y h:i A', $request->input('visitor_out_time'), 'Asia/Manila') 
            : null;
            
        $exitTimeFormatted = $exitTime 
            ? $exitTime->format('Y-m-d H:i:s') 
            : null;

        // Prepare data for updating
        $form_data = [
            'visitor_name' => $validatedData['visitor_name'],
            'visitor_status' => $validatedData['visitor_status'],
            'visitor_out_time' => $exitTimeFormatted,
        ];

        // Update the visitor record
        Visitor::where('id', $validatedData['row_id'])->update($form_data);

        // Redirect with success message
        return redirect('visitor')->with('success', 'Visitor data updated');
    }

    public function view($id)
    {
        $data = Visitor::findOrFail($id);
        return view('view_visitor', compact('data'));
    }

    public function print($id)
    {
        $data = Visitor::findOrFail($id);
        // // Generate QR code and assign to $qrCode variable
        // $qrCode = QrCode::size(300)->generate('https://example.com');
        // $qrCode = QrCode::format('png')->size(150)->generate(route('visitor.print', $id));
        return view('print_visitor', compact('data'));
    }

    public function store(Request $request)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'visitor_name' => 'required|string|max:255',
        'visitor_email' => 'required|email',
        'visitor_mobile_no' => 'required',
        'visitor_meet_person_name' => 'required|string|max:255',
        'visitor_department' => 'required|string|max:255',
        'visitor_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'visitor_reason_to_meet' => 'required|string|max:255',
        'visitor_enter_time' => 'required|date_format:m/d/Y h:i A',
        'visitor_out_time' => 'nullable|date_format:m/d/Y h:i A',
        'visitor_status' => 'required|string',
        'visitor_enter_by' => 'required|string',
    ]);

    // Handle image upload
    $imagePath = null;
    if ($request->hasFile('visitor_image')) {
        $image = $request->file('visitor_image');
        $imagePath = $image->store('images', 'public'); // Stores the image in 'storage/app/public/images'
    }

    // Generate a unique token
    $uniqueToken = Str::uuid()->toString();

    // Convert enter time
    $enterTime = Carbon::createFromFormat('m/d/Y h:i A', $request->input('visitor_enter_time'), 'Asia/Manila');
    $enterTimeFormatted = $enterTime ? $enterTime->format('Y-m-d H:i:s') : null;

    // Convert exit time if provided
    $exitTime = $request->input('visitor_out_time') ? Carbon::createFromFormat('m/d/Y h:i A', $request->input('visitor_out_time'), 'Asia/Manila') : null;
    $exitTimeFormatted = $exitTime ? $exitTime->format('Y-m-d H:i:s') : null;

    // Save the visitor entry with converted times
    $visitor = Visitor::create([
        'visitor_name' => $request->input('visitor_name'),
        'visitor_email' => $request->input('visitor_email'),
        'visitor_mobile_no' => $request->input('visitor_mobile_no'),
        'visitor_meet_person_name' => $request->input('visitor_meet_person_name'),
        'visitor_department' => $request->input('visitor_department'),
        'visitor_image' => $imagePath,
        'visitor_reason_to_meet' => $request->input('visitor_reason_to_meet'),
        'visitor_enter_time' => $enterTimeFormatted,
        'visitor_out_time' => $exitTimeFormatted,
        'visitor_status' => $request->input('visitor_status'),
        'visitor_enter_by' => $request->input('visitor_enter_by'),
        'unique_token' => $uniqueToken // Save the unique token
    ]);

    // Redirect with success message
    return redirect()->route('visitor.print', $visitor->id)
        ->with('success', 'New visitor added');
}

public function verifyToken(Request $request)
{
    $token = $request->input('token');
    $visitor = Visitor::where('unique_token', $token)->first();

    if (!$visitor) {
        return response()->json(['valid' => false, 'message' => '']);
    }

    $now = now();

    // Toggle the visitor's status
    if ($visitor->visitor_status === 'In') {
        $visitor->visitor_status = 'Out';
        $visitor->visitor_out_time = $now; // Set the out time to the current time when the status is "Out"
        $visitor->visitor_enter_out_by = Auth::id(); // Set the user who checked out
    } else {
        $visitor->visitor_status = 'In';
        $visitor->visitor_enter_time = $now;
        $visitor->visitor_enter_by = Auth::id();
        $visitor->visitor_out_time = null; // Clear the out time when checking in
    }

    $visitor->save();

    return response()->json([
        'valid' => true,
        'id' => $visitor->id,
        'status' => $visitor->visitor_status,
        'visitor_name' => $visitor->visitor_name,
        'visitor_email' => $visitor->visitor_email,
        'visitor_mobile_no' => $visitor->visitor_mobile_no,
        'visitor_enter_time' => Carbon::parse($visitor->visitor_enter_time)->timezone('Asia/Manila')->format('m/d/Y h:i A'),
        'visitor_out_time' => $visitor->visitor_out_time ? Carbon::parse($visitor->visitor_out_time)->timezone('Asia/Manila')->format('m/d/Y h:i A') : 'N/A',
        'message' => 'Visitor status updated successfully'
    ]);
}

public function updateVisitorStatus(Request $request)
{
    $visitorId = $request->input('visitor_id');
    $newStatus = $request->input('status');

    $visitor = Visitor::find($visitorId);
    if ($visitor) {
        $visitor->visitor_status = $newStatus;
        if ($newStatus === 'Out') {
            $visitor->visitor_out_time = now()->timezone('Asia/Manila');
        } else {
            // When status is 'In', don't change the out time
            // $visitor->visitor_out_time remains unchanged
        }
        $visitor->save();

        return response()->json(['success' => true, 'message' => 'Visitor status updated successfully']);
    }

    return response()->json(['success' => false, 'message' => 'Visitor not found'], 404);
}

public function getVisitorDetails(Request $request)
{
    $visitorName = $request->input('name');
    $visitor = Visitor::where('visitor_name', 'LIKE', "%{$visitorName}%")
                      ->orderBy('created_at', 'desc')
                      ->first();

    if ($visitor) {
        return response()->json([
            'name' => $visitor->visitor_name,
            'email' => $visitor->visitor_email,
            'phone' => $visitor->visitor_mobile_no
        ]);
    }

    return response()->json([], 404);
}

public function updateVisitorEntry(Request $request)
{
    $request->validate([
        'visitor_id' => 'required|integer',
        'visitor_enter_by' => 'required|integer',
    ]);

    $visitor = Visitor::find($request->visitor_id);
    if ($visitor) {
        $visitor->visitor_enter_by = $request->visitor_enter_by;
        $visitor->save();

        return response()->json(['success' => true, 'message' => 'Visitor entry updated successfully.']);
    }

    return response()->json(['success' => false, 'message' => 'Visitor not found.']);
}

}
