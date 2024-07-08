<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function admin_login(Request $request)
    {
        // dd($request);
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $checkUser = Admin::query()->where('email', $request->email)->exists();
        // dd($checkUser);
        if (!$checkUser) {
            return response()->json([
                'success' => false,
                'message' => 'E-mail you entered is Invalid. Try again with valid E-mail'
            ]);
        }

        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            // dd('aaaa ');
            return response()->json([
                'success' => true,
                'message' => 'Welcome to Admin Panel'
            ]);
        } else {
            if (Admin::query()->where('email', $request->email)->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password you entered is Incorrect'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong try again Later'
                ]);
            }
        }
    }

    public function admin_logout()
    {
        Auth::guard('admin')->logout();

        return redirect()->route('login');
    }

}
