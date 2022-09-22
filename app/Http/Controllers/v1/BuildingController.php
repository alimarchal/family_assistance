<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('all')) {
            $buildings = Building::all();
            return response()->json(['buildings' => $buildings], 200);
        } else {
            $buildings = Building::where('user_id', auth()->user()->id)->get();
            return response()->json(['buildings' => $buildings], 200);
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
     * @param \App\Http\Requests\StoreBuildingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBuildingRequest $request)
    {
        $request->merge(['user_id' => auth()->user()->id]);
        $building = Building::create($request->all());
        return response()->json(['building' => $building], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Building $building
     * @return \Illuminate\Http\Response
     */
    public function show(Building $building)
    {
        return response()->json(['building' => $building], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Building $building
     * @return \Illuminate\Http\Response
     */
    public function edit(Building $building)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateBuildingRequest $request
     * @param \App\Models\Building $building
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Building $building)
    {
//        $request->merge(['user_id' => auth()->user()->id]);
        $building->update($request->all());
        return response()->json(['building' => $building], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Building $building
     * @return \Illuminate\Http\Response
     */
    public function destroy(Building $building, Request $request)
    {
        $building->delete();
        return response()->json(['message' => 'Building deleted successfully.'], 200);
    }
}
