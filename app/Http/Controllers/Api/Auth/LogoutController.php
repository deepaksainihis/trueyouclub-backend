<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $user = $request->user();
       
        // Revoke all user's tokens to logout
        $user->tokens()->delete();

        // Return response
        $responseData = [
            'status'    => true,
            'message'   => 'Logged out successfully!',
        ];
        return response()->json($responseData, 200);
    }
}
