<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login user to obtain token.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'rememberMe' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email or password invalid.'
            ], 401);
        }

        $user = $request->user();

        $tokenGenerator = $user->createToken('Personal Access Token');
        $token = $tokenGenerator->token;
        if ($request->rememberMe) {
            $token->expires_at = Carbon::now()->addWeeks(4);
        }
        $token->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged in',
            'token' => $tokenGenerator->accessToken,
            'type' => 'Bearer',
            'expiresAt' => Carbon::parse($tokenGenerator->token->expires_at)->toDateTimeString(),
            'user' => $user,
        ]);
    }

    /**
     * Logout user (revoke token).
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Info logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user()
        ]);
    }

    /**
     * Update user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id
        ]);

        $userInputs = $request->only(['name', 'email']);
        foreach ($userInputs as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile successfully updated',
            'user' => $user
        ]);
    }

    /**
     * Change password user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'string|required|',
            'new_password' => 'string|required|min:6|confirmed'
        ]);

        $user = $request->user();

        $isSame = Hash::check($request->current_password, $user->password);
        if (!$isSame) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password'
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been changed',
            'user' => $user
        ]);
    }
}
