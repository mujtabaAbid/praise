<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function users()
    {
        return view('pages.users.index');
    }

    public function getUser(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];
    
        $query = User::query();
    
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
            });
        }
    
        $recordsFiltered = $query->count();
        $fetch_data = $query->latest()->offset($start)->limit($limit)->get();
        $recordsTotal = User::count();
    
        $data = [];
        $SrNo = $start + 1;
    
        foreach ($fetch_data as $item) {
            $EditRoute = url('edit.user', array(encrypt($item->id)));
            $Text = $item->about ?: 'Not Added';
    
            $data[] = [
                'id' => $SrNo,
                'name' => "<td>$item->name</td>",
                'email' => "<td>$item->email</td>",
                'about' => "<td>$Text</td>",
                'status' => "<td>$item->profile_status</td>"
            ];
    
            $SrNo++;
        }
    
        return response()->json([
            "draw" => intval($request->post('draw')),
            "iTotalRecords" => $recordsTotal,
            "iTotalDisplayRecords" => $recordsFiltered,
            "aaData" => $data
        ]);
    }
    
}
