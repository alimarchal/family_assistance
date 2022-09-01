<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Mail\SendOtp;
use App\Models\User;
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

        $user = User::where('email', $request->email)->first();

        if (!$user || Hash::check($request->password, $user->password)) {
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'error' => ['The provided credentials are incorrect.'],
                ], 404);
            } elseif ($user->status == 0) {
                return response([
                    'status' => ['Your account is suspended'],
                ], 45);
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
            $user = User::create($request->all());
            Mail::to($user)->send(new SendOtp($user));
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

}
