<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AllController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUpload $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $imageName = $this->fileUploadService->handleFileUpload($request, new User());

        $user = User::create([
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'qualification' => $request->qualification,
            'email' => $request->email,
            'designation' => $request->designation,
            'mobile' => $request->mobile,
            'image' => $imageName,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'status' => true,
            'message' => 'User registered successfully.',
            'user' => $user,
        ], 200);
    }

    public function login(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Login validation failed',
                'errors' => $validateUser->errors()->all()
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('API token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Recruiter successfully.',
            'user' => $user,
        ], 200);
    }

    public function allViewData()
    {
        $users = User::all();

        if (!$users) {
            return response()->json([
                'status' => false,
                'message' => 'No users found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Recruiters Profile',
            'users' => $users,
        ], 200);
    }

    public function updateData(Request $request)
    {
        $user = User::find($request->id);
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        if ($request->hasFile('image')) {
            $imageName = $this->fileUploadService->handleFileUpload($request, $user);
        } else {
            $imageName = $user->image;
        }
        $user->update([
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'qualification' => $request->qualification,
            'email' => $request->email,
            'designation' => $request->designation,
            'mobile' => $request->mobile,
            'image' => $imageName, 
            'role' => $request->role,
        ]);
    
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'status' => true,
            'message' => 'Recruiter Profile updated successfully.',
            'user' => $user,
        ], 200);
    }


    public function create_job(){
        
    }
    
}