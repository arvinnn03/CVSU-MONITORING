<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class QRScannerController extends Controller
{
    /**
     * Verify the scanned QR code token and update visitor/student status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken(Request $request)
    {
        $token = $request->input('token');
        $visitor = Visitor::where('unique_token', $token)->first();
        $student = Student::where('unique_token', $token)->first();

        if ($visitor) {
            return $this->handleVisitor($visitor);
        } elseif ($student) {
            return $this->handleStudent($student);
        }

        return response()->json(['valid' => false, 'message' => 'Invalid token']);
    }

    private function handleVisitor($visitor)
    {
        $newStatus = $visitor->visitor_status === 'In' ? 'Out' : 'In';
        $visitor->visitor_status = $newStatus;
        if ($newStatus === 'Out') {
            $visitor->visitor_out_time = now();
        } else {
            $visitor->visitor_enter_time = now();
            $visitor->visitor_out_time = null; // Clear the out time when the visitor is timed in again
        }
        $visitor->save();

        return response()->json([
            'valid' => true,
            'type' => 'visitor',
            'status' => $newStatus,
            'visitor_name' => $visitor->visitor_name,
            'visitor_email' => $visitor->visitor_email,
            'visitor_mobile_no' => $visitor->visitor_mobile_no,
            'visitor_meet_person_name' => $visitor->visitor_meet_person_name,
            'visitor_department' => $visitor->visitor_department,
            'visitor_reason_to_meet' => $visitor->visitor_reason_to_meet,
            'visitor_enter_time' => $visitor->visitor_enter_time->format('m/d/Y h:i A'),
            'visitor_out_time' => $visitor->visitor_out_time ? $visitor->visitor_out_time->format('m/d/Y h:i A') : null
        ]);
    }

    private function handleStudent($student)
    {
        $newStatus = $student->student_status === 'In' ? 'Out' : 'In';
        $student->student_status = $newStatus;
        if ($newStatus === 'Out') {
            $student->student_out_time = now();
        } else {
            $student->student_enter_time = now();
            $student->student_out_time = null;
        }
        $student->save();

        return response()->json([
            'valid' => true,
            'type' => 'student',
            'status' => $newStatus,
            'stud_id' => $student->stud_id,
            'student_department' => $student->student_department,
            'student_course' => $student->student_course,
            'student_enter_time' => $student->student_enter_time->format('m/d/Y h:i A'),
            'student_out_time' => $student->student_out_time ? $student->student_out_time->format('m/d/Y h:i A') : null
        ]);
    }

    public function updateVisitorEntry(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|integer',
            // Removed visitor_enter_by validation
        ]);

        $visitor = Visitor::find($request->visitor_id);
        if ($visitor) {
            $visitor->visitor_enter_by = auth()->id(); // Set the logged-in user's ID
            $visitor->save();

            return response()->json(['success' => true, 'message' => 'Visitor entry updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Visitor not found.']);
    }
}
