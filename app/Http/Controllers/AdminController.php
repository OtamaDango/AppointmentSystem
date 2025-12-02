<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminController extends Controller
{
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            return response()->json([
                'message' => 'Login successful',
                'admin' => $admin], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

    }
    public function logout(Request $request){
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }
}
