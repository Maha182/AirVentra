<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use App\Models\User;
use App\Helpers\AuthHelper;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
    * Display a listing of the users.
    */
    public function index(UsersDataTable $dataTable)
    {
        $pageTitle = 'Employee List';
        //trans('global-message.list_form_title', ['form' => trans('users.title')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('users.create').'" class="btn btn-sm btn-primary" role="button">Add New Employee </a>';

        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = ['admin' => 'Admin', 'employee' => 'Employee']; // Define available roles
        $supervisors = User::whereNotNull('role')->pluck('first_name', 'id');

        return view('users.form', compact('roles', 'supervisors'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request)
    {
        $validatedData = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'role'          => 'required|in:admin,employee',
            'phone_number'  => 'nullable|string|max:20',
            'hire_date'     => 'required|date',
            'supervisor_id' => 'nullable|exists:users,id',
            'password'      => 'required|min:6|confirmed',
        ]);

        $validatedData['password'] = Hash::make($request->password);

        User::create($validatedData);

        return redirect()->route('users.index')->withSuccess(__('Employee added successfully!'));
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('users.profile', compact('user'));
    }

    /**
     * Show the form for editing a user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = ['admin' => 'Admin', 'employee' => 'Employee']; // Available roles
        $supervisors = User::whereNotNull('role')->pluck('first_name', 'id'); // Fetch supervisors
    
        return view('users.form', compact('user', 'roles', 'id', 'supervisors'));
    }
    
    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,'.$id,
            'role'          => 'required|in:admin,employee',
            'phone_number'  => 'nullable|string|max:20',
            'hire_date'     => 'required|date',
            'supervisor_id' => 'nullable|exists:users,id',
            'password'      => 'nullable|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        return redirect()->route('users.index')->withSuccess(__('Employee updated successfully!'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $user->delete();
            return redirect()->back()->withSuccess(__('Employee deleted successfully.'));
        }

        return redirect()->back()->withErrors(__('Error deleting Employee.'));
    }
}

