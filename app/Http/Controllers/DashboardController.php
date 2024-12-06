<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Student;
use Carbon\Carbon;
use Auth;


class DashboardController extends Controller
{
    public function dashboard()
    {
        if (Auth::check()) {
            // Calculate visitor statistics
            $totalTodayVisits = Visitor::where('visitor_status', 'In', Carbon::today())->count();
            $totalLast7DaysVisits = Visitor::where('visitor_status', 'Out', Carbon::now()->subDays(7))->count();
            $totalStudentsIn = Student::where('student_status', 'In', Carbon::today())->count();
            $totalStudentsOut = Student::where('student_status', 'Out', Carbon::today())->count(); 
           
            // Pass variables to view
            return view('dashboard', compact(
                'totalTodayVisits', 
                'totalLast7DaysVisits', 
                 'totalStudentsIn', 
                 'totalStudentsOut'
            ));
        }
    
        // Redirect to login if not authenticated
        return redirect('login');
    }


}