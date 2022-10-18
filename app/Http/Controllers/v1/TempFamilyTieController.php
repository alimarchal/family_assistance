<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\TempFamilyTie;
use Illuminate\Http\Request;

class TempFamilyTieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('all')) {
            $tempFamilyTie = TempFamilyTie::all();
            return response()->json(['tempFamilyTie' => $tempFamilyTie], 200);
        } else {
            $tempFamilyTie = TempFamilyTie::where('my_id', auth()->user()->id)->get();
            return response()->json(['tempFamilyTie' => $tempFamilyTie], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['my_id' => auth()->user()->id]);
        $random_code = mt_rand(1000, 9999);
        $tie_code = auth()->user()->id . '_' . $random_code;
        $request->merge(['tie_code' => $tie_code]);
        $temp_family_tie = TempFamilyTie::create($request->all());

        return response()->json(['temp_family_tie' => $temp_family_tie], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(TempFamilyTie $tempFamilyTie)
    {
        return response()->json(['tempFamilyTie' => $tempFamilyTie], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TempFamilyTie $tempFamilyTie)
    {
        $tempFamilyTie->update($request->all());
        return response()->json(['tempFamilyTie' => $tempFamilyTie], 200);
    }


    public function verifyTie(Request $request)
    {
        $tie_code = $request->tie_code;
        $temp_family_tie = TempFamilyTie::where('tie_code', $tie_code)->first();
        $head_id = auth()->user()->id;
        if (!empty($temp_family_tie) && $head_id != $temp_family_tie->my_id) {
            $temp_family_tie->head_id = $head_id;
            $temp_family_tie->tie_code = NULL;
            $temp_family_tie->accepted = 1;
            $temp_family_tie->save();
            return response()->json(['message' => 'Tie successful'], 200);
        } else {
            return response()->json(['message' => 'Code not found'], 404);
        }
    }


    public function showTie(Request $request)
    {
        $user_auth_id =  auth()->user()->id;
        $family_tie = TempFamilyTie::where('my_id', $user_auth_id)->get();
        $family_tie_1 = TempFamilyTie::where('head_id',$user_auth_id)->get();
        $my_tire = ['my_ties' => $family_tie, 'other_tie' => $family_tie_1];
        return response()->json(['family_tie' => $my_tire], 200);
    }


    public function myTie()
    {
        $token_id = auth()->user()->id;
        $my_ties = TempFamilyTie::where('my_id', $token_id)->get();
        return response()->json(['my_ties' => $my_ties], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TempFamilyTie $tempFamilyTie)
    {
        $tempFamilyTie->delete();
        return response()->json(['message' => 'Temporary family tie deleted successfully.'], 200);
    }
}
