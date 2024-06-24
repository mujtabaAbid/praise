<?php
namespace App\Http\Controllers\api;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function signUp(Request $request)//1
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'country' => 'required|string|max:255',
            'town_city_region' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $imagePath = $request->image->move(public_path('upload/images'), $imageName);
            $imagePath = 'upload/images/' . $imageName; // Store the relative path
        } else {
            $imagePath = null;
        }

        $user = User::create([
            'first_name' => $request->firstname,
            'last_name' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'town_city_region' => $request->town_city_region,
            'image' => $imagePath,
        ]);
        $token = $user->createToken('auth_token');
        $plainTextToken = $token->plainTextToken;
        $data = [
            'token' => $plainTextToken,
            'user' => $user,
        ];
        return response()->json(['status' => 'success','message' => 'User created successfully', 'data' => $data]);
    }

    public function login()//6
    {
        $auth = Auth::attempt(['email' => request('email'), 'password' => request('password')]);
        if ($auth) {
            $user = User::where('id', Auth::id())->first();
            $token = $user->createToken('auth_token');
            $plainTextToken = $token->plainTextToken;
            $data = [
                'token' => $plainTextToken,
                'user' => $user,
            ];
            return response()->json(['status'=>'success', 'data' => $data, 'message'=>'Login Successfully'],200);

        } else {
            return response()->json(['status'=>'error', 'message'=>'Invalid Credentials'],);;
        }
    }
}
