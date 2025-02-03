<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\DataTables\UsersDataTable;
// use App\Models\User;
// use App\Helpers\AuthHelper;
// use Spatie\Permission\Models\Role;
// use App\Http\Requests\UserRequest;

// class UserController extends Controller
// {
//     // /**
//     //  * Display a listing of the resource.
//     //  *
//     //  * @return \Illuminate\Http\Response
//     //  */
//     // public function index(UsersDataTable $dataTable)
//     // {
//     //     $pageTitle = trans('global-message.list_form_title',['form' => trans('users.title')] );
//     //     $auth_user = AuthHelper::authSession();
//     //     $assets = ['data-table'];
//     //     $headerAction = '<a href="'.route('users.create').'" class="btn btn-sm btn-primary" role="button">Add New User </a>';
//     //     return $dataTable->render('global.datatable', compact('pageTitle','auth_user','assets', 'headerAction'));
//     // }
//     /**
//     * Display a listing of the users.
//     *
//     * @return \Illuminate\Http\Response
//     */
//     public function index(UsersDataTable $dataTable)
//     {
//         $pageTitle = trans('global-message.list_form_title',['form' => trans('users.title')] );
//         $auth_user = AuthHelper::authSession();
//         $assets = ['data-table'];
//         $headerAction = '<a href="'.route('users.create').'" class="btn btn-sm btn-primary" role="button">Add New Employee </a>';
    
//         // Fetch all users excluding the password column
//         $users = User::select('id', 'first_name', 'last_name', 'email', 'role', 'phone_number', 'hire_date', 'supervisor_id', 'updated_at', 'created_at')->get();

//         return $dataTable->render('global.datatable', compact('pageTitle','auth_user','assets', 'headerAction'));
//     }

//     /**
//      * Show the form for creating a new resource.
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function create()
//     {
//         $roles = Role::where('status',1)->get()->pluck('title', 'id');

//         return view('users.form', compact('roles'));
//     }

//     /**
//      * Store a newly created resource in storage.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @return \Illuminate\Http\Response
//      */
//     public function store(UserRequest $request)
//     {
//         $request['password'] = bcrypt($request->password);

//         $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100,1000);

//         $user = User::create($request->all());

//         storeMediaFile($user,$request->profile_image, 'profile_image');

//         $user->assignRole('user');

//         // Save user Profile data...
//         $user->userProfile()->create($request->userProfile);

//         return redirect()->route('users.index')->withSuccess(__('message.msg_added',['name' => __('users.store')]));
//     }

//     /**
//      * Display the specified resource.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function show($id)
//     {
//         $data = User::with('userProfile','roles')->findOrFail($id);

//         $profileImage = getSingleMedia($data, 'profile_image');

//         return view('users.profile', compact('data', 'profileImage'));
//     }

//     /**
//      * Show the form for editing the specified resource.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function edit($id)
//     {
//         $data = User::with('userProfile','roles')->findOrFail($id);

//         $data['user_type'] = $data->roles->pluck('id')[0] ?? null;

//         $roles = Role::where('status',1)->get()->pluck('title', 'id');

//         $profileImage = getSingleMedia($data, 'profile_image');

//         return view('users.form', compact('data','id', 'roles', 'profileImage'));
//     }

//     /**
//      * Update the specified resource in storage.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function update(UserRequest $request, $id)
//     {
//         // dd($request->all());
//         $user = User::with('userProfile')->findOrFail($id);

//         $role = Role::find($request->user_role);
//         if(env('IS_DEMO')) {
//             if($role->name === 'admin'&& $user->user_type === 'admin') {
//                 return redirect()->back()->with('error', 'Permission denied');
//             }
//         }
//         $user->assignRole($role->name);

//         $request['password'] = $request->password != '' ? bcrypt($request->password) : $user->password;

//         // User user data...
//         $user->fill($request->all())->update();

//         // Save user image...
//         if (isset($request->profile_image) && $request->profile_image != null) {
//             $user->clearMediaCollection('profile_image');
//             $user->addMediaFromRequest('profile_image')->toMediaCollection('profile_image');
//         }

//         // user profile data....
//         $user->userProfile->fill($request->userProfile)->update();

//         if(auth()->check()){
//             return redirect()->route('users.index')->withSuccess(__('global-message.update_form',['name' => __('message.user')]));
//         }
//         return redirect()->back()->withSuccess(__('message.update_form',['name' => 'My Profile']));

//     }

//     /**
//      * Remove the specified resource from storage.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function destroy($id)
//     {
//         $user = User::findOrFail($id);
//         $status = 'errors';
//         $message= __('global-message.delete_form', ['form' => __('users.title')]);

//         if($user!='') {
//             $user->delete();
//             $status = 'success';
//             $message= __('global-message.delete_form', ['form' => __('users.title')]);
//         }

//         if(request()->ajax()) {
//             return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
//         }

//         return redirect()->back()->with($status,$message);

//     }
//}

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
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('users.title')]);
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

        return redirect()->route('users.index')->withSuccess(__('User added successfully!'));
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

        return view('users.form', compact('user', 'roles', 'id'));
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

        return redirect()->route('users.index')->withSuccess(__('User updated successfully!'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $user->delete();
            return redirect()->back()->withSuccess(__('User deleted successfully.'));
        }

        return redirect()->back()->withErrors(__('Error deleting user.'));
    }
}

