<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Auth
 */
class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(['user' => new UserResource($request->user())], Response::HTTP_OK);
    }

    public function update(UpdateUserRequest $request)
    {
        $request->user()->update($request->validated());

        return response()->json(['user' => new UserResource($request->user())], Response::HTTP_ACCEPTED);
    }
}
