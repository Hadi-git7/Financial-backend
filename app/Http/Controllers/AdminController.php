<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;


class AdminController extends Controller
{
    use SoftDeletes;

    public function register(Request $request){
    $fields = $request->validate([
        'username' => 'required|string|unique:users',
        'password' => 'required|string|confirmed',
        'is_super' => 'nullable|boolean',
    ]);

    $check = Admin::where('username', $fields['username'])->first();
    if($check){
        return [
            'message' => "Username already exists,Choose another one please"
        ];
    }

    // Get the current authenticated admin
    $authenticatedAdmin = auth()->user();

    // If there are no super admins in the database yet, create the first super admin account
    if (Admin::where('is_super', true)->count() === 0) {
        $data = [
            'username' => $fields['username'],
            'password' => bcrypt($fields['password']),
            'is_super' => true,
            'admin_id' => null,
            'deleted_by' => null,
            'created_by' =>auth()->check() ? auth()->user()->username : null,
        ];
    } else {
        // If there is at least one super admin in the database, check if the authenticated admin is a super admin
        if (!$authenticatedAdmin || !$authenticatedAdmin->is_super) {
            return [
                'message' => "You do not have permission to create new admins."
            ];
        }

        // Merge $validatedData and $fields arrays
        $validatedData['admin_id'] = $authenticatedAdmin->id;
        $validatedData['deleted_by'] = null; // initialize deleted_by to null
        $validatedData['created_by'] = auth()->check() ? auth()->user()->username : null;

        $data = array_merge($validatedData, $fields);

        // Cast is_super field to boolean
        $data['is_super'] = (bool) $data['is_super'];
    }

    $admin = Admin::create([
        'username' => $data['username'],
        'password' => $data['password'],
        'is_super' => $data['is_super'] ?? false,
        'admin_id' => $data['admin_id'],
        'deleted_by' => $data['deleted_by'],
    ]);

    $token = $admin->createToken('myapptoken')->plainTextToken;

    $response = [
        'admin' => $admin,
        'token' => $token,
        'is_super' => $admin->is_super,
    ];

    return response($response,201);
}

    // Login function
    public function login(Request $request){
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        //   Check username
        $admin = Admin::where('username', $fields['username'])->first();

        // Check password
        if(!$admin || !Hash::check($fields['password'], $admin->password)){
            return response([
                'message' => 'Invalid username or password'
            ],401);
        }

        $token = $admin->createToken('myapptoken')->plainTextToken;

        $response = [
            'admin' => $admin,
            'token' => $token,
            'is_super' => $admin->is_super,
        ];

        return response($response,201);
    }

    // Logout function
    public function logout(Request $request){
        auth()->user()->tokens()->delete();

        return [
            'message' => "Logged out"
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Admin::all();
    }


    /**
     * Display the specified resource.
     */
    public function show(Admin $id)
    {
        return Admin::find($id);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        if(auth()->user()->is_super){

            $validatedData = $request->validate([
                'username',
                'password',
                'is_super',
                
            ]);


        // Hash the password
        $validatedData['password'] = Hash::make($request->password);


         // Get the current authenticated admin
         $validatedData['admin_id'] = auth()->user()->id;
         $validatedData['updated_by'] = auth()->user()->username;

         // Merge data from $request->all() and $validatedData
          $mergedData = array_merge($request->all(), $validatedData);

         $admin->update($mergedData);
        //  $admin->update($validatedData);
         $updatedAdmin = Admin::find($id); // retrieve updated data from the database

  
        return $updatedAdmin;
    }else if(!auth()->user()->is_super){
        return [
            'message' => "Only super admins can make this action"
        ];
    }
    else{
        return [
            'message' => "Admin not found"
        ];
    }
}
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $admin =Admin::find($id);
        if(auth()->user()->is_super){

         // Get the current authenticated admin
         $validatedData['deleted_by'] = auth()->user()->username;
            $admin->update($validatedData);
            $admin->delete();
            return [
                'message' => "Deleted successfuly"
            ];
        }else if(!auth()->user()->is_super){
            return [
                'message' => "Only super admins can make this action"
            ];
        }
        else{
        return [
            'message' => "Admin not found"
        ];}
    }

    // Create new admins by super admin
    public function createAdmin(Request $request)
{
    // Check if the current authenticated admin is a super admin
    if (!auth()->user()->is_super) {
        return response(['message' => 'Unauthorized action. Only super admin can create new admins.'], 401);
    }

    $fields = $request->validate([
        'username' => 'required|string|unique:users',
        'password' => 'required|string|confirmed',
        'is_super' => 'nullable|boolean',
    ]);

    $check = Admin::where('username', $fields['username'])->first();
    if ($check) {
        return [
            'message' => "Username already exists. Choose another one please."
        ];
    }

    // Get the current authenticated admin
    $validatedData['admin_id'] = auth()->check() ? auth()->user()->id : null;
    $validatedData['deleted_by'] = null; // initialize deleted_by to null
    $validatedData['created_by'] = auth()->user()->username ;

    // Merge $validatedData and $fields arrays
    $data = array_merge($validatedData, $fields);

    // Cast is_super field to boolean
    $data['is_super'] = false;

      $admin = Admin::create([
        'username' => $data['username'],
        'password' => bcrypt($data['password']),
        'is_super' => $data['is_super'] ?? false,
        'admin_id' => $data['admin_id'],
        'created_by' => $data['created_by'],
        'deleted_by' => $data['deleted_by'],
    ]);

    $token = $admin->createToken('myapptoken')->plainTextToken;

    $response = [
        'admin' => $admin,
        'token' => $token,
        'is_super' => $admin->is_super,
        'created_by' => $validatedData['created_by']
    ];

    return response($response,201);
}
}