<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Mail\SendOtp;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);


        $check_account_status = User::where('email', $request->email)->first();
        if (!empty($check_account_status)) {
            // check if deleted
            if ($check_account_status->account_deleted) {
                return response(['message' => 'You account has been deleted.'], 200);
            }
        }


        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'error' => ['The provided credentials are incorrect.'],
                ], 404);
            } elseif ($user->status == 0) {
                return response([
                    'status' => ['Your account is suspended'],
                ], 403);
            }
        }

        $user->device_name = $request->device_name;
        $user->save();

        return response([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => $user,
        ], 200);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response(['message' => 'Logged out'], 200);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ], [
            'device_name.required' => 'Device name is required.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = mt_rand(1000, 9999);
        $request->merge(['otp' => $otp]);
        $check_user = User::where('email', $request->email)->first();
        if (empty($check_user)) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'otp' => $request->otp,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'country' => $request->country,
                'city' => $request->city,
                'mobile' => $request->mobile,
                'role' => $request->role,
                'mac_address' => $request->mac_address,
                'device_name' => $request->device_name,
            ]);
            Mail::to($user)->send(new SendOtp($user, $request->subject, $request->description));
            return response([
                'token' => $user->createToken($request->device_name)->plainTextToken,
                'user' => $user,
            ], 200);
        } else {
            return response([
                'error' => 'Integrity constraint violation: 1062 Duplicate Email ID',
            ], 403);
        }
    }


    public function otpVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'string', 'max:255'],
        ], [
            'otp.required' => 'OTP is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail(auth()->user()->id);

        if ($user->otp != $request->otp) {
            return response([
                'error' => ['Incorrect OTP entered.'],
            ], 200);
        }
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();

        return response([
            'user' => 'verified'
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!empty($user)) {
            return response()->json($user, 200);
        } else {
            return response()->json(['message' => 'Not Found!'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(['message' => 'Not Found!'], 404);
        } else {
            $user->update($request->all());
            return response()->json($user, 200);
        }
    }

    public function forgot_password(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT ? response()->json(['status' => __($status)]) : response()->json(['email' => __($status)]);
    }


    public function delete_account_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model_name' => ['required'],
            'sending_mode' => ['required'],
        ], [
            'model_name.required' => 'Model name is required.',
            'sending_mode.required' => 'Sending mode is required.',
        ]);

        $user = User::firstWhere('id', auth()->id());
        if (!$user) {
            return response()->json(['error' => 'Record Not Found'], 404);
        }

        $otp = mt_rand(1000, 9999);
        $user = auth()->user();
        $user->otp = $otp;
        $user->save();

        $subject = $request->subject;
        $description = $request->description;

        $otp_generated = Otp::create([
            'user_id' => auth()->user()->id,
            'user_parent_id' => $request->user_parent_id,
            'model_name' => $request->model_name,
            'otp_code' => $otp,
            'sending_mode' => $request->sending_mode
        ]);

        if (!empty($subject) && !empty($description)) {
            Mail::to($user)->send(new SendOtp($user, $subject, $description));
        } else {
            Mail::to($user)->send(new SendOtp($user));
        }

        return response()->json(['success' => 'OTP has been sent to your registered email.']);
    }


    public function delete_account_request_verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'string', 'max:255'],
        ], [
            'otp.required' => 'OTP is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail(auth()->user()->id);

        if ($user->otp != $request->otp) {
            return response([
                'error' => ['Incorrect OTP entered.'],
            ], 200);
        }
        $user->account_deleted = 1;
        $user->save();
        $otp_obj = Otp::where('user_id', auth()->user()->id)->latest()->first();
        $otp_obj->status = 1;
        $otp_obj->save();

        return response([
            'message' => 'You account has been deleted.'
        ], 200);
    }

}
