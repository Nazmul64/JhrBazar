<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Return the authenticated user's profile.
     */
    public function profile(Request $request)
    {
        $user = $request->user(); // works with JWT auth middleware
        return new UserResource($user);
    }
}
