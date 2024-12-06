<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Hash;
use Illuminate\Support\Facades\Auth;

class SubUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('sub_user');
    }

    public function fetch_all(Request $request)
    {
        if ($request->ajax()) {
            $data = User::whereIn('type', ['User1', 'User2', 'User3'])->get();
    
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                   
                    // Edit Button with Font Awesome icon
                    $editBtn = '<a href="/sub_user/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm mx-1" title="Edit" data-toggle="tooltip" data-placement="top"><i class="fas fa-pencil-alt"></i></a>';
                    
                    // Delete Button with Font Awesome icon
                    $deleteBtn = '<button type="button" class="btn btn-outline-danger btn-sm mx-1 delete" data-id="' . $row->id . '" title="Delete" data-toggle="tooltip" data-placement="top"><i class="fas fa-trash-alt"></i></button>';
                    
    
                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    
    public function add()
    {
        return view('add_sub_user');
    }

    public function add_validation(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:6',
            'type'          => 'required|in:User1,User2,User3|unique:users,type'
        ]);

        $data = $request->all();

        User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'type'      => $data['type']
        ]);

        return redirect('sub_user')->with('success', 'New User Added');
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);

        return view('edit_sub_user', compact('data'));
    }

    public function edit_validation(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'name'      => 'required',
            'type'      => 'required|in:User1,User2,User3|unique:users,type,'.$request->hidden_id
        ]);

        $data = $request->all();

        $form_data = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'type'  => $data['type'] // Ensure type is updated as well
        ];

        if (!empty($data['password'])) {
            $form_data['password'] = Hash::make($data['password']);
        }

        User::whereId($data['hidden_id'])->update($form_data);

        return redirect('sub_user')->with('success', 'User Data Updated');
    }

    public function delete($id)
    {
        $data = User::findOrFail($id);

        $data->delete();

        return redirect('sub_user')->with('success', 'User Data Removed');
    }
}
