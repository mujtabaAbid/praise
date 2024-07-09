<?php
namespace App\Http\Controllers\api;
use App\Models\Otp;
use App\Models\User;
use App\Models\City;
use App\Mail\OtpMail;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    //ok
    public function signUp(Request $request)//1
    {
        $validator = Validator::make($request->all(), [
            'firstname'     => 'required|string|max:255',
            'lastname'      => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            'country_id'    => 'required|exists:countries,id',
            'state_id'      => 'exists:countries,id',
            'city_id'       => 'exists:countries,id',
            'image'         => 'nullable|image|mimes:jpeg,png,webp,jpg,svg|max:2048',
        ]);


        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['success' => false,'message' => $errorMessage]);
        }

    
        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $imagePath = $request->image->move(public_path('upload/images'), $imageName);
            $imagePath = 'upload/images/' . $imageName; // Store the relative path
        } else {
            $imagePath = 'upload/images/PIc.png';
        }

        $user = User::create([
            'first_name'    => $request->firstname,
            'last_name'     => $request->lastname,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'country_id'    => $request->country_id,
            'state_id'      => $request->state_id,
            'apple_id'      => $request->apple_id,
            'google_id'     => $request->google_id,
            'state_id'      => $request->state_id,
            'city_id'       => $request->city_id,
            'image'         => $imagePath,
        ]);


        $user->load('Country','State','city');

        $token = $user->createToken('auth_token');
        $plainTextToken = $token->plainTextToken;
        $data = [
            'token' => $plainTextToken,
            'user'  => $user,
        ];

        // $this->generateOtp($user->email);
        return response()->json(['success' => true,'message' => 'User created successfully', 'data' => $data]);
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
            'email'     => 'required|string|email|max:255',
            'password'  => 'required|string|min:8',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['success' => false,'message' => $errorMessage],);
        }
        
        $auth = Auth::attempt(['email' => request('email'), 'password' => request('password')]);
        if ($auth) {
            $user                   = User::where('id', Auth::id())->first();
            $user->load('Country','State','city');
            $userDetails            = $user->toArray();
            $userDetails['image']   = $user->image;
            $token                  = $user->createToken('auth_token');
            $plainTextToken         = $token->plainTextToken;
            $data                   = ['token' => $plainTextToken, 'user' => $userDetails,];

            return response()->json(['success'=>true, 'message'=>'User Logged in successfully', 'data' => $data],);
            
        } else {
            return response()->json(['success'=>false, 'message'=>'Invalid Credentials'],);;
        }
    }
    
    //ok
    public function socialLogin(Request $request)
    {
        try {
            // DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email'     => 'required|email',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ],);
            }
            $user = User::where('email', $request->email)->first();
            if ($user) {
                // if ($request->account_type == 'google') {
                //     $user = User::with(['UserProfile'])->where('google_id', $request->social_id)->first();
                // } else {
                //     $user = User::with(['UserProfile'])->where('apple_id', $request->social_id)->first();
                // }
                // $token        = $user->createToken('mujtaba')->accessToken;
                $token                  = $user->createToken('auth_token');
                $plainTextToken         = $token->plainTextToken;
               
                // $user->update(['fcm_token' => $request->fcm_token]);
                DB::commit();
                return response()->json(['success' => true, "data" => [
                    'token' => $plainTextToken,
                    'user'  => $user,

                ]]);
            } else {
                $validator = Validator::make($request->all(), [
                    'first_name'      => 'required|max:255',
                    'last_name'       => 'required|max:255',
                    'account_type'    => 'required|in:google,apple',
                    'social_id'       => 'required|max:255',
                    'email'           => 'required|email|unique:users',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->first()
                    ], );
                }
                $google_id      = null;
                $apple_id       = null;



                if ($request->account_type == 'google') {
                    $google_id =  $request->social_id;
                } else {
                    $apple_id =  $request->social_id;
                }

                if ($request->hasFile('image')) {
                    $imageName = time().'.'.$request->image->getClientOriginalExtension();
                    $imagePath = $request->image->move(public_path('upload/images'), $imageName);
                    $imagePath = 'upload/images/' . $imageName; // Store the relative path
                } else {
                    $imagePath = 'upload/images/PIc.png';
                }





                $user = User::create([
                    'first_name'        => $request->first_name,
                    'last_name'         => $request->last_name,
                    'email'             => $request->email,
                    'password'          => encrypt('123456dummy'),
                    // 'account_type'      => $request->account_type,
                    'google_id'         => $google_id,
                    'apple_id'          => $apple_id,
                    'image'             => $imagePath,
                ]);

                // $token = $user->createToken('mytoken')->accessToken;
                $token = $user->createToken('auth_token');
                $plainTextToken         = $token->plainTextToken;

                $user = User::where('id', $user->id)->first();
                $data = [
                    'token' => $plainTextToken,
                    'user'  => $user,
                ];
               
                // DB::commit();
                return response()->json(['success' => true,'message'=>'Login Successfull', "data" => $data],);
            }
        } catch (\Throwable $th) {
            // DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], );
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
            'email'             => 'required|email',
            'new_password'      => 'required|min:8',
            'confirm_password'  => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message'=> $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success'=>false,'message' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success'=>true,'message' => 'Password reset successfully.'], 200);
    }

    //ok
    public function getOtp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['success' => false, 'message' => $errorMessage],);
        }
            $otp = rand(1000, 9999);
            
            Otp::updateOrCreate(
                ['email'=> $request->email],
                ['otp'  => $otp, 'created_at' => now()]
            );
            
            // Send OTP to email
            Mail::to($request->email)->send(new OtpMail($otp));
            return response()->json(['success'=>true, 'message' => 'Otp Sent successfully.'], );
            
    }
    //ok
    public function matchOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required|digits:4'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false,'message' => $validator->errors()->first()],);
        }
        
        // Validate OTP
        $otp = Otp::where('email', $request->email)->where('otp', $request->otp)->first();
        if (!$otp) {
            return response()->json(['success' => false,'message' => 'Invalid OTP.'],);
        }
        
        return response()->json(['success' =>true,'message' => 'OTP verified.'],);
    }
    //ok
    public function countries(){
        
        $countries = Country::all();
        
        return response()->json(['success'=>true, 'message' => 'Countries Data fetched Successfully', 'data'=> $countries]);
        
    }
    //ok
    public function state(Request $request){
        
        $validator = Validator::make($request->all(), 
        ['country_id'    => 'required|exists:countries,id',]);
        
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['success' => false,'message' => $errorMessage]);
        }
        
        $data = State::where('country_id',$request->country_id)->get();
        return response()->json(['success'=>true, 'message' => 'state Data fetched Successfully', 'data'=> $data]);
    
    }

    //ok
    public function cities(Request $request){
        
        $validator = Validator::make($request->all(),
        ['state_id' => 'required|exists:states,id'],['state_id' => 'State doesnot exist']);
        
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['success' => false,'message' => $errorMessage]);
        }

        $cities = City::where('state_id',$request->state_id)->get();
        return response()->json(['success'=>true, 'message' => 'state Data fetched Successfully', 'data'=> $cities]);

    }
}

