<?php
namespace App\Http\Controllers\api;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Otp;
// use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    //ok
    public function signUp(Request $request)//1
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'country' => 'required|string|max:255',
            'town_city_region' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            // return response()->json($validator->errors());
            return response()->json(['status' => 'false','message' => $errorMessage]);
            
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
            'image' => 'https://praisy.beckapps.co/'.$imagePath,
        ]);
        $token = $user->createToken('auth_token');
        $plainTextToken = $token->plainTextToken;
        $data = [
            'token' => $plainTextToken,
            'user' => $user,
        ];

        // $this->generateOtp($user->email);
        return response()->json(['status' => 'true','message' => 'User created successfully', 'data' => $data]);
    }
    

    // public function matchOtp(Request $request)
        // {
        //     $validator = Validator::make($request->all(), [
        //         'email' => 'required|email',
        //         'otp' => 'required|digits:4'
        //     ]);

        //     if ($validator->fails()) {
        //         return response()->json(['status' => 'error','message'=> $validator->errors()], 422);
        //     }

        //     // Validate OTP
        //     // Assume Otp is a model that stores OTPs
        //     $otp = Otp::where('email', $request->email)->where('otp', $request->otp)->first();

        //     if (!$otp) {
        //         return response()->json(['status' => 'error','message' => 'Invalid OTP.'], 400);
        //     }

        //     // Activate user
        //     $user = User::where('email', $request->email)->first();
        //     $user->email_verified_at = now();
        //     $user->save();

        //     return response()->json(['message' => 'Email verified successfully.'], 200);
 // }

    //ok
    public function login(Request $request)//6
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            // return response()->json($validator->errors());
            return response()->json(['status' => 'false','message' => $errorMessage],404);
        }
        
        $auth = Auth::attempt(['email' => request('email'), 'password' => request('password')]);
        if ($auth) {
            $user = User::where('id', Auth::id())->first();
            $userDetails = $user->toArray();
            $userDetails['image'] = 'https://praisy.beckapps.co/' . $user->image;
            $token = $user->createToken('auth_token');
            $plainTextToken = $token->plainTextToken;
            $data = [
                'token' => $plainTextToken,
                'user' => $userDetails,
            ];
            return response()->json(['status'=>'true', 'data' => $data, 'message'=>'User Logged in successfully'],200);
            
        } else {
            return response()->json(['status'=>'false', 'message'=>'Invalid Credentials'],404);;
        }
    }
    

    public function socialLogin(Request $request)
    {
        try {
            // DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email'           => 'required|email',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }
            $user = User::where('email', $request->email)->first();
            if ($user) {
                // if ($request->account_type == 'google') {
                //     $user = User::with(['UserProfile'])->where('google_id', $request->social_id)->first();
                // } else {
                //     $user = User::with(['UserProfile'])->where('apple_id', $request->social_id)->first();
                // }
                $token        = $user->createToken('mujtaba')->accessToken;
               
                $user->update(['fcm_token' => $request->fcm_token]);
                DB::commit();
                return response()->json(['status' => 'success', "result" => [
                    'token' => $token,
                    'user'  => $user,

                ]]);
            } else {
                $validator = Validator::make($request->all(), [
                    'first_name'      => 'required|max:255',
                    'last_name'       => 'required|max:255',
                    'email'           => 'required|email|unique:users',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $validator->errors()->first()
                    ], 422);
                }
                $google_id      = null;
                $apple_id       = null;

                if ($request->account_type == 'google') {
                    $google_id =  $request->social_id;
                } else {
                    $apple_id =  $request->social_id;
                }
                $user = User::create([
                    'first_name'        => $request->firstname,
                    'last_name'         => $request->lastname,
                    'email'             => $request->email,
                    'password'          => encrypt('123456dummy'),
                    'google_id'         => $google_id,
                    'apple_id'          => $apple_id,
                ]);

                $token = $user->createToken('mytoken')->accessToken;
                $user = User::where('id', $user->id)->first();
                $data = [
                    'token' => $token,
                    'user'  => $user,
                ];
               
                // DB::commit();
                return response()->json(['status' => 'true', "data" => $data,'message'=>'Login Successfull'],200);
            }
        } catch (\Throwable $th) {
            // DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    // public function forgotPassword(Request $request)
        // {
        //     $validator = Validator::make($request->all(), [
        //         'email' => 'required|email'
        //     ]);

        //     if ($validator->fails()) {
        //         return response()->json($validator->errors(), 422);
        //     }

        //     // Generate OTP and send to email
        //     $this->generateOtp($request->email);

        //     return response()->json(['message' => 'OTP sent to email.'], 200);
        // }


    //ok
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'false','message'=> $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status'=>'false','message' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['status'=>'true','message' => 'Password reset successfully.'], 200);
    }

    //ok
    public function getOtp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            // return response()->json($validator->errors());
            return response()->json(['status' => 'false','message' => $errorMessage],404);
        }
            $otp = rand(1000, 9999);
            
            Otp::updateOrCreate(
                ['email' => $request->email],
                ['otp' => $otp, 'created_at' => now()]
            );
            
            // Send OTP to email
           $res = Mail::to($request->email)->send(new OtpMail($otp));
            dd($res);
            return response()->json(['status'=>'true', 'message' => 'Otp Sent successfully.'], 200);

    }

    //ok
    public function matchOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'false','message' => $validator->errors()->first()], 422);
        }

        // Validate OTP
        $otp = Otp::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$otp) {
            return response()->json(['status' => 'false','message' => 'Invalid OTP.'], 400);
        }

        return response()->json(['status' =>'true','message' => 'OTP verified.'], 200);
    }


}
