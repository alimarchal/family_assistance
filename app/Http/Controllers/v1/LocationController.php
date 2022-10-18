<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Location;
use App\Models\TempFamilyTie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->isMapAccess) {
            if ($request->input('all')) {
                $locations = Location::all();
                return response()->json(['locations' => $locations], 200);
            }
            if ($request->input('family_head_id')) {

                $family_tie = TempFamilyTie::where('my_id', auth()->user()->id)->where('accepted', 1)->whereNull('untie_request')->get();
                $head_id = $family_tie->pluck('head_id');

                $all_ids = [auth()->user()->id];
                foreach ($head_id as $x) {
                    $all_ids[] = $x;
                }

                $collection = collect();
                foreach ($all_ids as $x) {
                    $test_query = DB::select("SELECT * FROM locations INNER JOIN users ON locations.user_id = users.id WHERE locations.family_head_id = " . $x . " GROUP BY locations.user_id DESC;");
                    $collection->push($test_query);
                }


                return response()->json(['locations' => $collection], 200);

            } else {
                $locations = Location::where('user_id', auth()->user()->id)->get();
                return response()->json(['locations' => $locations], 200);
            }
        } else {
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
