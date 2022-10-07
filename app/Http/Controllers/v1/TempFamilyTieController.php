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
