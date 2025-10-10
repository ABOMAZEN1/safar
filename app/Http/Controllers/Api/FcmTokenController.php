<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $user = $request->user();
        $user->fcm_token = $request->token;
        $user->save();

        return response()->json(['status' => 'ok']);
    }
}
