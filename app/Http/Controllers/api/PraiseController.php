<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Praise;
use App\Models\PraiseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PraiseController extends Controller
{


    public function createPraise(Request $request) //11
    {
        $request->validate([
            'receiver_id' => 'required',
            // 'status' => 'required',
            'category_id' => 'required|exists:praise_categories,id',
        ]);
        // Check if the sender has already sent this type of praisy to the receiver
        $existingPraisy = Praise::where('sender_id', Auth::user()->id)
            ->where('receiver_id', $request->receiver_id)
            ->where('category_id', $request->category_id)
            ->first();

        if ($existingPraisy) {
            return response()->json(['message' => 'You have already sent this praisy type to this user.'], 422);
        }

        $praise = Praise::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => $request->receiver_id,
            'details' => $request->details,
            'category_id' => $request->category_id,
            'time' => Carbon::now(),
            'status' => 0,
        ]);
        return response()->json(['status' => 'success', 'message' => 'Praise created successfully', 'data' => $praise]);
    }

    public function getReceivedPraises() //8
    {
        // $perPage = $request->input('per_page', 10);

        // Fetch the paginated praises
        $praises = Praise::with('praiseCategory', 'Sender', 'Receiver')
            ->where('receiver_id', Auth::id())
            ->where('status', 1)->get();

        // Check if no praises found
        if ($praises->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No praises found',
            ], 404);
        }

        // Calculate total hours for each praise
        $praises->each(function ($praise) {
            $praiseTime = Carbon::parse($praise->time);
            $currentTime = Carbon::now();
            $totalHours = $praiseTime->diffInHours($currentTime);
            $praise->total_hours = $totalHours;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Received Praises retrieved successfully',
            'data' => $praises
        ]);
    }

    public function getSentPraises(Request $request) // 7, 12
    {
        $perPage = $request->input('per_page', 10);

        // Fetch the paginated praises
        $praises = Praise::with('praiseCategory', 'Sender', 'Receiver')
            ->where('sender_id', Auth::id())
            ->where('status', 1)
            ->paginate($perPage);

        // Check if no praises found
        if ($praises->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No praises found',
            ], 404);
        }

        // Calculate total hours for each praise
        $praises->each(function ($praise) {
            $praiseTime = Carbon::parse($praise->time);
            $currentTime = Carbon::now();
            $totalHours = $praiseTime->diffInHours($currentTime);
            $praise->total_hours = $totalHours;
        });


        return response()->json([
            'status' => 'success',
            'message' => 'Praise Reply Sent successfully',
            'data' => $praises
        ]);
    }

    public function getPraiseById(Request $request) //10
    {
        // Fetch the praise by ID
        $praise = Praise::with('praiseCategory', 'Sender', 'Receiver')
            ->where('id', $request->id)
            ->where('status', 1)
            ->first();

        if (!$praise) {
            return response()->json([
                'status' => 'error',
                'message' => 'Praise not found',
            ], 404);
        }

        // Calculate the total hours from praise time to now
        $praiseTime = Carbon::parse($praise->time);
        $currentTime = Carbon::now();
        $totalHours = $praiseTime->diffInHours($currentTime);

        // Include the total hours in the praise data
        $praise->total_hours = $totalHours;

        return response()->json([
            'status' => 'success',
            'message' => 'Praise retrieved successfully',
            'data' => $praise,
        ]);
    }


    public function updateStatus(Request $request) //9
    {
        // dd($request->praise_id);
        $praise = Praise::where('id', $request->praise_id)->update([
            'status' => $request->status
        ]);
        $response = $request->status == 0 ? 'Rejected' : 'Accepted';
        $message = "Praise "  .$response.   " successfully";
        return response()->json(['status' => 'success', 'message' => $message,]);
    }

    public function filters(Request $request)//13
    {

        $prases = Praise::with('receiver')->where('status', 1);
        // if (isset($request->param)) {
        //     $prases->whereHas('receiver', function($query) use ($request) {
        //         $query->where('profession', $request->param);
        //     });
        // }
        if (isset($request->category_id)) {
            $prases->where('category_id', $request->category_id);
        }

        if ($request->region != 'WorldWide') {
        if (isset($request->region)) {
            $prases->whereHas('receiver', function($query) use ($request) {
                    $query->where('country', $request->region)->orwhere('town_city_region',$request->region);
                });

        }}





        $praises = $prases->get();
        return response()->json(['status' => 'success', 'data' => $praises]);

    }
}
