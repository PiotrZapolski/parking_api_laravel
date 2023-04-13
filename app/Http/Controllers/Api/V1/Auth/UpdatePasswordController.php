<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\Response;

/**
 * @group Auth
 */
class UpdatePasswordController extends Controller
{
    public function __invoke(UpdatePasswordRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->json(['message' => 'Password has been updated'], Response::HTTP_ACCEPTED);
    }
}
