<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Validator;


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

        if (!$user || !Hash::check($request->password, $user->password)) {
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
        $check_user = User::where('email', $request->email)->first();
        if (empty($check_user)) {
            $user = User::create($request->all());
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
        $user = User::where('email', $request->email)->first();
        if (!empty($user)) {
            return response()->json(['error' => 'Record Not Found'], 404);
        }
        $password = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9);
        $user->update(['password' => Hash::make($password)]);
        return response()->json(['success' => 'New password is sent to your registered email.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
