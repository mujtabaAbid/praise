<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Praise;
use Illuminate\Http\Request;
use App\Models\PraiseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PraiseController extends Controller
{

    //ok
    public function createPraise(Request $request) //11
    {
        $receiverExists = User::find($request->receiver_id);
        if (!$receiverExists) {
            return response()->json(['success' => false, 'message' => 'The receiver ID does not exist.'],);
        }
        $praiseCategory = PraiseCategory::find($request->category_id);
        if (!$praiseCategory) {
            return response()->json(['success' => false, 'message' => 'Praise category ID does not exist.'],);
        }
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:praise_categories,id',
        ]);

        // Check if the sender has already sent this type of praisy to the receiver
        $existingPraisy = Praise::where('sender_id', Auth::user()->id)
            ->where('receiver_id', $request->receiver_id)
            ->where('category_id', $request->category_id)
            ->first();

        if ($existingPraisy) {
            return response()->json(['success' => false, 'message' => 'You have already sent this praisy type to this user.'], );
        }

        $praise = Praise::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => $request->receiver_id,
            'details' => $request->details,
            'category_id' => $request->category_id,
            'time' => Carbon::now(),
            // 'status' => 'pending',
        ]);
        return response()->json(['success' => true, 'message' => 'Praise created successfully', 'data' => $praise]);
    }

    //ok
    public function getReceivedPraises(Request $request) //8
    {

        if ($request->receiver_id == Auth::id()) {
            return response()->json([ 'success'=>false, 'message'=>'You can not sent paraise to yourself']);
        }else{

        $perPage = $request->input('per_page', 10);

        // Fetch the paginated praises
        $praises = Praise::with('praiseCategory', 'Sender', 'Receiver')
            ->where('receiver_id', Auth::id())->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $request->current_page);
        // ->where('status', 1)
        // ->get();

        // Check if no praises found
        if ($praises->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No record found',
            ],);
        }

        // Calculate total hours for each praise
        $praises->each(function ($praise) {
            $praiseTime = Carbon::parse($praise->time);
            $currentTime = Carbon::now();
            $totalHours = $praiseTime->diffInHours($currentTime);
            $praise->total_hours = $totalHours;
        });

        return response()->json([
            'success' => true,
            'message' => 'Received Praises retrieved successfully',
            'data' => $praises,
            // 'data' => $praises->items(),
            // 'pagination' => [
            //     'total' => $praises->total(),
            //     'per_page' => $praises->perPage(),
            //     'current_page' => $praises->currentPage(),
            //     'last_page' => $praises->lastPage(),
            //     'from' => $praises->firstItem(),
            //     'to' => $praises->lastItem(),
            // ],
        ]);
    }

    }

    //ok
    public function getSentPraises(Request $request) // 7, 12
    {
        // dd('wow');
        $perPage = $request->input('per_page', 10);


        // Fetch the paginated praises
        // $abc = Praise::all()->count();
        // dd($abc);
        $praises = Praise::with('praiseCategory', 'Sender', 'Receiver')
            ->where('sender_id', Auth::id())->orderBy('created_at', 'desc')
            // ->where('status', 1)
            ->paginate($perPage, ['*'], 'page', $request->current_page);

        // Check if no praises found
        if ($praises->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No praises found',
            ],);
        }

        // Calculate total hours for each praise
        $praises->each(function ($praise) {
            $praiseTime = Carbon::parse($praise->time);
            $currentTime = Carbon::now();
            $totalHours = $praiseTime->diffInHours($currentTime);
            $praise->total_hours = $totalHours;
        });


        return response()->json([
            'success' => true,
            'message' => 'Praise Reply Sent successfully',
            'data' => $praises,
            // 'pagination' => [
            //     'total' => $praises->total(),
            //     'per_page' => $praises->perPage(),
            //     'current_page' => $praises->currentPage(),
            //     'last_page' => $praises->lastPage(),
            //     'from' => $praises->firstItem(),
            //     'to' => $praises->lastItem(),
            // ],
        ]);
    }
    //ok
    public function getPraiseById(Request $request) //10
    {
        // Fetch the praise by ID
        if ($request->id != null) {

            $praise = Praise::with('praiseCategory', 'Sender', 'Receiver')
                ->where('id', $request->id)
                ->where('status', 1)
                ->first();

            if (!$praise) {
                return response()->json([
                    'success' => true,
                    'message' => 'Praise not found',
                ],);
            }

            // Calculate the total hours from praise time to now
            $praiseTime = Carbon::parse($praise->time);
            $currentTime = Carbon::now();
            $totalHours = $praiseTime->diffInHours($currentTime);

            // Include the total hours in the praise data
            $praise->total_hours = $totalHours;

            return response()->json([
                'success' => true,
                'message' => 'Praise retrieved successfully',
                'data' => $praise,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Praise id is required',
            ]);
        }
    }

    //ok
    public function updateStatus(Request $request) //9
    {
        // dd($request->praise_id);
        $praiseData = Praise::where('id', $request->praise_id)->first();
        // dd($request->status);
        if ($praiseData != null) {
            if (($request->status == 0 || $request->status == 1) && $request->status != null) {

                $praise = Praise::where('id', $request->praise_id)->update([
                    'status' => $request->status
                ]);
                $response = $request->status == 0 ? 'Rejected' : 'Accepted';
                $message = "Praise "  . $response .   " successfully";
                return response()->json(['success' => true, 'message' => $message,]);
            } else {
                return response()->json(['success' => false, 'message' => 'Please enter valid status',]);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Please enter valid praise id',]);
        }
    }

    // public function filters(Request $request)//13
        // {

            //    $prases = Praise::with('receiver')->where('status', 1);
            //    // if (isset($request->param)) {
            //    //     $prases->whereHas('receiver', function($query) use ($request) {
            //    //         $query->where('profession', $request->param);
            //    //     });
            //    // }
            //    if (isset($request->category_id)) {
            //        $prases->where('category_id', $request->category_id);
            //    }
            //    if ($request->region != 'WorldWide') {
            //    if (isset($request->region)) {
            //        $prases->whereHas('receiver', function($query) use ($request) {
            //                $query->where('country', $request->region)->orWhere('country_id',$request->region);
            //            });
            //    }}


        //     $praises = $prases->get();
        //     return response()->json(['status' => 'success', 'data' => $praises]);

    // }


    public function praisesFilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer', // Ensure category_id is a single integer
        ]);
    
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['success' => false, 'message' => $errorMessage],);
        }
    
        $countryId  = $request->input('country_id');
        $stateId    = $request->input('state_id');
        $cityId     = $request->input('city_id');
        $categoryId = $request->input('category_id');
    
        // Initialize query to get users with received praises and status 1
        $query = User::whereHas('praises', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
            $q->where('status', 1); // Assuming status is in the praises table
        })->with(['praises' => function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
            $q->where('status', 1); // Assuming status is in the praises table
            // $q->with('category');
        }, 'country', 'state', 'city']);
    
        // Apply filters based on country, state, and city
        if (!empty($countryId)) {
            $query->where('country_id', $countryId);
        }
        if (!empty($stateId)) {
            $query->where('state_id', $stateId);
        }
        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }
    
        // Get the results
        $users = $query->get();
    
        // Prepare the response data
        // $response = $users->map(function ($user) {
        //     // Calculate count of praises per category
        //     $receivedPraises = $user->praises->groupBy('category_id')->map->count();
        //     return [
        //         'User_Profile_Image' => $user->profile_image?? 'https://praisy.beckapps.co/upload/images/PIc.png' ,
        //         'Username' => ($user->first_name.' '.$user->last_name),
        //         'Received_Praises' => $receivedPraises->all(), // Convert to array if needed
        //         'Country' => $user->country ? $user->country->name : null,
        //         'State' => $user->state ? $user->state->name : null,
        //         'City' => $user->city ? $user->city->name : null,
        //     ];
        // });
        $response = $users->map(function ($user) {
            // Calculate count of praises per category
            $receivedPraises = $user->praises->groupBy('category_id')->mapWithKeys(function ($praises, $categoryId) {
                $categoryName = $praises->first()->category->name; // Get the category name
                return [$categoryName => $praises->count()]; // Use category name instead of ID
            });
    
            return [
                'User_Profile_Image' => $user->profile_image ?? 'https://praisy.beckapps.co/upload/images/PIc.png',
                'Username' => ($user->first_name . ' ' . $user->last_name),
                'Received_Praises' => $receivedPraises->all(), // Convert to array if needed
                'Country' => $user->country ? $user->country->name : null,
                'State' => $user->state ? $user->state->name : null,
                'City' => $user->city ? $user->city->name : null,
            ];
        });
    
        if ($response->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'No record found', 'data' => $response]);
        }
        // if ($response == '[]'){

        //     return response()->json(['success' => true, 'message' => 'No record found', 'data' => $response]);
        // } 
    
        return response()->json(['success' => true, 'message' => 'Data Fetched successfully', 'data' => $response]);
    }

    public function allUsers(){

        $allUsers = User::withCount('Praises')->get();
        return response()->json(['success'=>true, 'message' => 'All Users fetched Successfully', 'data'=> $allUsers]);
    }

    public function allUsersWithPagination(Request $request){

        $perPage = $request->input('per_page', 10);

        $allUsers = User::withCount('Praises')->paginate($perPage, ['*'], 'page', $request->current_page);
        return response()->json(['success'=>true, 'message' => 'All Users fetched Successfully', 'data'=> $allUsers]);
    }

    public function praisyCategory(){

        $allUsers = PraiseCategory::all();

        return response()->json(['success'=>true, 'message' => 'All Praise Category fetched Successfully', 'data'=> $allUsers]);
        
        
    }
    

   
    public function userDetails(Request $request) {
        $userId = $request->id;
        $userDetails = User::where('id', $userId)
            ->with(['praises' => function ($query) {
                $query->where('status', 1)->with('category');
            }])
            ->withCount(['praises as total_praises'])
            ->first();
    
        // Check if user details were found
        if (!$userDetails) {
            return response()->json(['success' => false, 'message' => 'User not found', 'data' => null], 404);
        }
    
        $acceptedPraise = Praise::select('category_id', \DB::raw('count(*) as count'))
            ->where('receiver_id', $userId)
            ->where('status', 1)
            ->groupBy('category_id')
            ->get();
    
        // Map category IDs to their names
        $categoryNames = PraiseCategory::pluck('name', 'id');
    
        // Prepare the formatted praise count with category names
        $praise = $acceptedPraise->mapWithKeys(function ($item) use ($categoryNames) {
            return [$categoryNames[$item->category_id] => $item->count];
        });
    
        // Convert formatted praise categories count to array
        $praiseArray = $praise->toArray();
    
        // Attach formatted praise categories count to user details
        $userDetails->acceptedPraise = $praiseArray;
    
        return response()->json(['success' => true, 'message' => 'User Details fetched Successfully', 'data' => $userDetails]);
    }


    public function userProfile() {
        $userId = Auth()->id();
        $userDetails = User::where('id', $userId)
            ->with(['praises' => function ($query) {
                $query->where('status', 1)->with('category');
            }])
            ->withCount(['praises as total_praises'])
            ->first();
    
        // Check if user details were found
        if (!$userDetails) {
            return response()->json(['success' => false, 'message' => 'User not found', 'data' => null], 404);
        }
    
        $acceptedPraise = Praise::select('category_id', \DB::raw('count(*) as count'))
            ->where('receiver_id', $userId)
            ->where('status', 1)
            ->groupBy('category_id')
            ->get();
    
        // Map category IDs to their names
        $categoryNames = PraiseCategory::pluck('name', 'id');
    
        // Prepare the formatted praise count with category names
        $praise = $acceptedPraise->mapWithKeys(function ($item) use ($categoryNames) {
            return [$categoryNames[$item->category_id] => $item->count];
        });
    
        // Convert formatted praise categories count to array
        $praiseArray = $praise->toArray();
    
        // Attach formatted praise categories count to user details
        $userDetails->acceptedPraise = $praiseArray;
    
        return response()->json(['success' => true, 'message' => 'User Profile fetched Successfully', 'data' => $userDetails]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search users by username or other fields
        $users = User::where('first_name', 'LIKE', "%{$query}%")
                     ->orWhere('last_name', 'LIKE', "%{$query}%")
                     ->get();


        $praiseCategoryName = PraiseCategory::where('name', 'LIKE', "%{$query}%")->first()->name;

        $praiseCategoryIds = PraiseCategory::where('name', 'LIKE', "%{$query}%")
                                        ->pluck('id')
                                        ->toArray();

        // Step 3: Retrieve praises where category_id matches the retrieved PraiseCategory IDs
        $praises = Praise::whereIn('category_id', $praiseCategoryIds)->pluck('receiver_id')->unique()->toArray();
        // Step 4: Retrieve users with these receiver_ids
        $praiseUsers = User::whereIn('id', $praises)->get();


        return response()->json([
            'success'           => true,
            'message'           => 'Search Result',
            'users'             => $users,
            'praiseName'        => $praiseCategoryName,
            'praisedUser'       => $praiseUsers
        ]);
    }
}
