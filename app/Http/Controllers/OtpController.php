<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Session;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use Auth;

class OtpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showOtpForm()
    {
        return view('otp_form'); // Ensure this is the correct view path
    }

    public function addverify()
    {
        // Fetch all guards from the database
        $guards = Otp::all(); // Assuming `Otp` model holds all the guard data

        // Pass the guards' email addresses to the view
        return view('otp_verify', compact('guards'));
    }



    public function gate1()
    {
       

        $data = Department::all();
        return view('gate1_visitor', compact('data'));
    
        // return view('otp_verify');
    
    }

    
    public function gate2()
    {
       

        $data = Department::all();
        return view('gate2_visitor', compact('data'));
    
        // return view('otp_verify');
    
    }

      
    public function gate3()
    {
       

        $data = Department::all();
        return view('gate3_visitor', compact('data'));
    
        // return view('otp_verify');
    
    }

    public function add()
    {
      
        $data = Department::all();
        return view('add_visitor', compact('data'));
    
        // return view('otp_verify');
    
    }

     /**
     * Store a new guard.
     */
    public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'guard_name' => 'required|string|max:255',
        'guard_email' => 'required|email|unique:otps,guard_email',
    ]);

    // Create a new guard record
    $guard = Otp::create([
        'guard_name' => $request->input('guard_name'),
        'guard_email' => $request->input('guard_email'),
        'otp' => rand(100000, 999999), // Generate a 6-digit numeric OTP
        'expires_at' => now()->addMinutes(720) // Adjust expiration time as needed
    ]);

    // Send OTP to the user via email
    Mail::to($guard->guard_email)->send(new OtpMail($guard->otp));

    // Redirect with success message
    return redirect()->back()->with('success', 'Guard added and OTP sent successfully!');
}



    public function sendOtp(Request $request)
    {
        // Fetch all guards, regardless of status
        $guards = Otp::all();
        $message = "Click 'Send OTP' to generate and send OTPs for on-duty guards.";

        // Return early to prevent auto-resend
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
                'guards' => $guards
            ]);
        } else {
            return view('otp_form', compact('guards'))
                   ->with('info', $message);
        }
    }
    

    public function generateAndSendOtp(Request $request)
    {
        $guards = Otp::where('guard_status', 'On Duty')->get();
        $updatedCount = 0;
        $now = now();

        foreach ($guards as $guard) {
            if (!$guard->otp || !$guard->expires_at || $guard->expires_at <= $now) {
                // Generate new OTP only if it doesn't exist or has expired
                $otp = rand(100000, 999999);
                $guard->update([
                    'otp' => $otp,
                    'expires_at' => $now->addMinutes(720)
                ]);
            } else {
                // Use existing OTP if it hasn't expired
                $otp = $guard->otp;
            }

            Mail::to($guard->guard_email)->send(new OtpMail($otp));
            $updatedCount++;
        }

        $message = $updatedCount > 0 
            ? "OTP sent to {$updatedCount} on-duty guard(s)."
            : "No on-duty guards found to send OTP.";

        return response()->json([
            'message' => $message,
            'guards' => $guards
        ]);
    }

    public function validateOtp(Request $request)
    {
        $request->validate([
            'guard_name' => 'required|string',
            'guard_email' => 'required|email',
            'otp' => 'required|numeric|digits:6', // Ensure OTP is numeric and exactly 6 digits
        ]);

        $name = $request->input('guard_name');
        $email = $request->input('guard_email');
        $otp = $request->input('otp');

        // Find the OTP record in the database
        $otpRecord = Otp::where('guard_email', $email)
                        ->where('otp', $otp)
                        ->where('expires_at', '>', Carbon::now())
                        ->first();

        // Additional session-based OTP verification
        if ($otpRecord || $otp == Session::get('otp')) {
            // OTP is valid, update the record with the correct status
            $otpRecord = Otp::where('guard_email', $email)->first();
            $otpRecord->update([
                'guard_status' => 'On Duty'
            ]);

            // Clear OTP validation session
            Session::forget('otp');
            Session::forget('otp_valid');

            // Redirect to the visitor list with a success message
            return redirect()->route('visitor.index')->with('success', 'Visitor added successfully.');
        } else {
            // OTP is invalid or expired
            return redirect()->back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }
    }
    
    public function verify(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'otp' => 'required|digits:6',
        ]);
    
        $otp = $validated['otp'];
    
        // Retrieve the authenticated user
        $user = Auth::user();
    
        // Check if user is admin
        if ($user->type === 'Admin') {
            return redirect()->route('gate1_visitor.gate1');
        }
    
        // Verify and delete OTP if it exists
        $otpRecord = DB::table('otps')
            ->where('otp', $otp)
            ->first();
    
        if ($otpRecord) {
            
            // Determine the redirection route based on user attributes
            switch ($user->type) {
                case 'User1':
                    return redirect()->route('gate1_visitor.gate1');
                case 'User2':
                    return redirect()->route('gate2_visitor.gate2');
                case 'User3':
                    return redirect()->route('gate3_visitor.gate3');
                default:
                    return redirect()->route('home'); // Fallback route if no match
            }
        } else {
            // Handle invalid OTP scenario (e.g., redirect back with an error)
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP.']);
        }
    }
    
    public function add_validation(Request $request)
    {
        $request->validate([
            'guard_name' => 'required|string',
            'guard_email' => 'required|email',
        ]);

        $name = $request->input('guard_name');
        $email = $request->input('guard_email');
        $otp = rand(100000, 999999); // Generate a 6-digit numeric OTP

        // Store OTP in the database
        Otp::updateOrCreate(
            ['guard_email' => $email],
            [
                'guard_name' => $name,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(720) // Adjust expiration time as needed
            ]
        );

        // Send OTP to the user via email
        Mail::to($email)->send(new OtpMail($otp));

        // Store OTP in session for additional session-based verification
        Session::put('otp', $otp);

        // Save the visitor entry with converted times
        Otp::updateOrCreate(
            ['guard_email' => $email],
            [
                'guard_name' => $name,
                'guard_status' => 'On Duty'
            ]
        );

        // Clear OTP validation session
        Session::forget('otp');
        Session::forget('otp_valid');

        return redirect()->route('otp_form')->with('success', 'New Guard added and OTP sent.');
    }
    
public function resendOtp(Request $request)
{
    $request->validate([
        'guard_email' => 'required|email',
    ]);

    // Fetch the guard's OTP record based on the email
    $guard = Otp::where('guard_email', $request->input('guard_email'))->first();

    if ($guard) {
        // Check if the guard is "On Duty"
        if ($guard->guard_status !== 'On Duty') {
            return response()->json(['message' => 'OTP cannot be resent. Guard is not On Duty.'], 403);
        }

        // Check if the OTP is still valid
        if ($guard->expires_at > now()) {
            // Send the existing OTP via email
            Mail::to($guard->guard_email)->send(new OtpMail($guard->otp));

            return response()->json(['message' => 'OTP has been resent successfully.']);
        } else {
            return response()->json(['message' => 'OTP has expired. Please request a new OTP.'], 400);
        }
    } else {
        return response()->json(['message' => 'No guard found with this email address.'], 404);
    }
}


public function destroy($id)
{
    $otp = Otp::findOrFail($id);

    if ($otp->guard_status === 'On Duty') {
        return response()->json(['message' => 'Cannot delete a guard who is on duty.'], 403);
    }

    $otp->delete();

    return response()->json(['message' => 'Guard deleted successfully.']);
}

public function updateStatus(Request $request)
{
    $request->validate([
        'id' => 'required|exists:otps,id',
        'status' => 'required|string'
    ]);

    $otp = Otp::findOrFail($request->id);
    $otp->guard_status = $request->status;
    $otp->save();

    return response()->json(['message' => 'Guard status updated successfully.']);
}

}

