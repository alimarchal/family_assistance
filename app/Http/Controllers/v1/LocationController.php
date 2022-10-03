<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->isMapAccess)
        {
            if ($request->input('all')) {
                $locations = Location::all();
                return response()->json(['locations' => $locations], 200);
            }
            if ($request->input('family_head_id')) {
                $locations = Location::where('family_head_id', $request->family_head_id)->get();
                return response()->json(['locations' => $locations], 200);
            } else {
                $locations = Location::where('user_id', auth()->user()->id)->get();
                return response()->json(['locations' => $locations], 200);
            }
        } else{
            return response()->json(['message' => 'Permission_Denied'], 403);
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public
    function store(Request $request)
    {
        $request->merge(['user_id' => auth()->user()->id]);
        $location = Location::create($request->all());
        return response()->json(['location' => $location], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function show(Location $location)
    {
        return response()->json(['location' => $location], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $location->update($request->all());
        return response()->json(['location' => $location], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location, Request $request)
    {
        $location->delete();
        return response()->json(['message' => 'Location deleted successfully.'], 200);
    }
}
