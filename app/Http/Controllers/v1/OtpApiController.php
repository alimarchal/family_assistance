<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Mail\SendOtp;
use App\Models\Otp;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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


        $user = auth()->user();
        $user->otp = $otp;
        $user->save();

        $subject = $request->subject;
        $description = $request->description;


        $otp_generated = Otp::create($request->all());
        if (!empty($subject) && !empty($description)) {
            Mail::to($user)->send(new SendOtp($user, $subject, $description));
        } else {
            Mail::to($user)->send(new SendOtp($user));
        }

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
