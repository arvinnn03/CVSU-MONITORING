<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Department;

use DataTables;

use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('department');
    }

    function fetch_all(Request $request)
    {
        if($request->ajax())
        {
            $data = Department::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                     // Edit Button with Font Awesome icon
                     $editBtn = '<a href="/department/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm mx-1" title="Edit" data-toggle="tooltip" data-placement="top"><i class="fas fa-pencil-alt"></i></a>';
                    
                     // Delete Button with Font Awesome icon
                     $deleteBtn = '<button type="button" class="btn btn-outline-danger btn-sm mx-1 delete" data-id="' . $row->id . '" title="Delete" data-toggle="tooltip" data-placement="top"><i class="fas fa-trash-alt"></i></button>';
                     
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    
    function add()
    {
        return view('add_department');
    }

    function add_validation(Request $request)
    {
        $request->validate([
            'department_name'       =>  'required',
            'course_name'        =>  'required'
        ]);

        $data = $request->all();

        Department::create([
            'department_name'       =>  $data['department_name'],
            'course_name'        =>  implode(", ", $data['course_name'])
        ]);

        return redirect('department')->with('success', 'New Department Added');
    }

    public function edit($id)
    {
        $data = Department::findOrFail($id);

        return view('edit_department', compact('data'));
    }

    function edit_validation(Request $request)
    {
        $request->validate([
            'department_name'       =>  'required',
            'course_name'        =>  'required'
        ]);

        $data = $request->all();

        $form_data = array(
            'department_name'       =>  $data['department_name'],
            'course_name'        =>  implode(", ", $data['course_name'])
        );

        Department::whereId($data['hidden_id'])->update($form_data);

        return redirect('department')->with('success', 'Department Data Updated');
    }

    function delete($id)
    {
        $data = Department::findOrFail($id);

        $data->delete();

        return redirect('department')->with('success', 'Department Data Removed');
    }
}
