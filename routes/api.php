<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Helper\ApiResponseHelper;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserCollection;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function (){
    Route::get('/test', function (Request $request) {
        return 'Hey, Its working!';
    });

    Route::post('/user/login', function (Request $request) {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = 'Validation failed';
            response()->setResponse(false, $message, $errors, null, 422);
        } else{
            $user = User::where('email', $request->email)->first();

            if(! $user || ! Hash::check($request->password, $user->password)) {
                $errors = ['email' => ['The provided credentials are incorrect.']];
                response()->setResponse(false, 'Validation failed', $errors, null, 422);
            } else{
                $data = ['user' => $user, 'token' => $user->createToken($request->device_name)->plainTextToken];
                response()->setResponse(true, null, null, $data, 200);
            }
        }

        return response()->getResponse();
    })->name('login');

    Route::middleware(['auth:sanctum'])->group( function (){
        Route::get('/user', function (Request $request) {
            response()->setResponse(true, null, null, $request->user(), 200);
            return response()->getResponse();
        });

        Route::get('/user/{id}', function ($id) {
            $data = User::findOrFail($id);
            response()->setResponse(true, null, null, $data, 200);
            return response()->getResponse();
        });
    });
});
