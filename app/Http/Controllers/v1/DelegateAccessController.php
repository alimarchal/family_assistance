<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Mail\SendOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DelegateAccessController extends Controller
{

    public function requestOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delegate_mail_id' => 'required',
        ]);

        $user = User::where('email', $request->delegate_mail_id)->first();
        if (!empty($user)) {

            $otp = mt_rand(1000, 9999);
            $request->merge(['user_id' => $user->id]);
            $request->merge(['otp_code' => $otp]);
            $request->merge(['sending_mode' => 'Delegate Access']);

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

            return response(['otp' => $otp], 200);

        } else {
            return response(['message' => 'User not found!'], 404);
        }
    }


    public function delegateAccessVerifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delegate_mail_id' => 'required',
            'otp' => 'required',
            'temporary_family_tie' => 'required',
        ]);

        $user = User::where('email', $request->delegate_mail_id)->first();

        $auth_user = auth()->user();

        if (!empty($user)) {
            if ($user->otp == $request->otp) {
                $auth_user->temporary_family_tie = $auth_user->temporary_family_tie . ',' . $request->temporary_family_tie;
                $auth_user->save();

                $otp_obj = Otp::where('user_id', auth()->user()->id)->latest()->first();
                $otp_obj->status = 1;
                $otp_obj->save();

                return response(['message' => 'Delegate access has been granted.'], 200);
            } else {
                return response(['message' => 'OTP not matching!'], 404);
            }
        } else {
            return response(['message' => 'User not found!'], 404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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
