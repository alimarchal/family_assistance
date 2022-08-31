<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpApiController extends Controller
{
    //
    public function otpGenerate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model_name' => 'required',
            'sending_mode' => 'required',
        ], [
            'model_name.required' => 'Model name is required.',
            'sending_mode.required' => 'Sending mode is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = mt_rand(1000, 9999);
        $request->merge(['user_id' => auth()->user()->id]);
        $request->merge(['user_parent_id' => $request->user_parent_id]);
        $request->merge(['otp_code' => $otp]);
        $request->merge(['sending_mode' => $request->sending_mode]);

        $otp_generated = Otp::create($request->all());
        return response(['otp' => $otp_generated], 200);
    }

    public function show($id)
    {
        $otps = Otp::where('user_id', $id)->get();
        return response(['otp' => $otps], 200);
    }


    public function showLatest($id)
    {
        $otps = Otp::where('user_id', $id)->latest()->first();
        if (!empty($otps)) {
            return response(['otp' => $otps], 200);
        } else {
            return response()->json(['message' => 'User not Found'], 404);
        }
    }

    public function showUserParentId($showUserParentId)
    {
        $otps = Otp::where('user_parent_id', $showUserParentId)->get();
        return response(['otp' => $otps], 200);
    }
}
