<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Student;
use DataTables;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('student');
    }

    public function fetchAll(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::select('id', 'stud_id', 'student_department', 'student_course', 'student_enter_time', 'student_out_time', 'student_status')
            ->orderBy('updated_at', 'desc'); // Sort by updated_at in descending order

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actions = '';

                    // Check if the user is an admin
                    if (Auth::user()->type == 'Admin') {
                        $actions .= '<button class="btn btn-outline-secondary btn-sm mx-1" title="Print" onclick="window.open(\'/student/print/' . $row->stud_id . '\', \'_blank\')">
                                        <i class="bi bi-printer"></i>
                                    </button>';
                        $actions .= '<button class="btn btn-outline-info btn-sm mx-1 qr-code-btn" title="QR Code" data-id="' . $row->stud_id . '">
                                        <i class="bi bi-qr-code"></i>
                                    </button>';
                        $actions .= '<a href="/student/edit/' . $row->stud_id . '" class="btn btn-outline-primary btn-sm mx-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>';
                        $actions .= '<button class="btn btn-outline-danger btn-sm mx-1 delete-student" title="Delete" data-id="' . $row->stud_id . '">
                                        <i class="bi bi-trash"></i>
                                    </button>';
                    }

                    return $actions;
                })
                ->editColumn('student_enter_time', function($row) {
                    return $row->student_enter_time ? Carbon::parse($row->student_enter_time)->setTimezone('Asia/Manila')->format('m/d/Y h:i A') : null;
                })
                ->editColumn('student_out_time', function($row) {
                    return $row->student_out_time ? Carbon::parse($row->student_out_time)->setTimezone('Asia/Manila')->format('m/d/Y h:i A') : null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function add()
    {
        $departments = Department::all();
        return view('add_student', compact('departments'));
    }

    public function add_validation(Request $request)
    {
        $request->validate([
            'stud_id' => 'required|unique:students',
            'student_department' => 'required',
            'student_course' => 'required',
        ]);

        Student::create([
            'stud_id' => $request->stud_id,
            'student_department' => $request->student_department,
            'student_course' => $request->student_course,
            'student_status' => 'Out',
            'unique_token' => Str::random(32),
        ]);

        return redirect('student')->with('success', 'New Student Added');
    }

    public function verify(Request $request)
    {
        $input = $request->input('token');

        // Check if the input is a JSON string (from QR code)
        $decodedInput = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decodedInput['token'])) {
            $input = $decodedInput['token'];
        }

        $student = Student::where('unique_token', $input)
                          ->orWhere('stud_id', $input)
                          ->first();

        if (!$student) {
            return response()->json(['valid' => false, 'message' => '']);
        }

        $now = now();

        // Toggle the student's status
        if ($student->student_status === 'In') {
            $student->student_status = 'Out';
            $student->student_out_time = $now;
        } else {
            $student->student_status = 'In';
            $student->student_enter_time = $now;
        }

        $student->save();

        return response()->json([
            'valid' => true,
            'stud_id' => $student->stud_id,
            'status' => $student->student_status,
            'student_enter_time' => Carbon::parse($student->student_enter_time)->setTimezone('Asia/Manila')->format('m/d/Y h:i A'),
            'student_out_time' => $student->student_out_time ? Carbon::parse($student->student_out_time)->setTimezone('Asia/Manila')->format('m/d/Y h:i A') : null,
        ]);
    }

    public function printStudent($id)
    {
        $this->authorize('admin-only'); // Ensure the user is an admin
        $student = Student::where('stud_id', $id)->firstOrFail();
        $qrCode = QrCode::size(300)->generate(json_encode(['token' => $student->unique_token]));
        return view('print_student', compact('student', 'qrCode'));
    }

    public function destroy($id)
    {
        $this->authorize('admin-only'); // Ensure the user is an admin
        $student = Student::where('stud_id', $id)->firstOrFail();
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function edit($id)
    {
        $student = Student::where('stud_id', $id)->firstOrFail();
        $departments = Department::all();
        return view('edit_student', compact('student', 'departments'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'stud_id' => 'required|unique:students,stud_id,'.$request->id,
            'student_department' => 'required',
            'student_course' => 'required',
        ]);

        $student = Student::findOrFail($request->id);
        $student->update([
            'stud_id' => $request->stud_id,
            'student_department' => $request->student_department,
            'student_course' => $request->student_course,
        ]);

        return redirect()->route('student.index')->with('success', 'Student Updated Successfully');
    }

    public function getToken($id)
    {
        $student = Student::where('stud_id', $id)->firstOrFail();
        return response()->json(['token' => $student->unique_token]);
    }
}
