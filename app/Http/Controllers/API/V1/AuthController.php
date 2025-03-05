<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\User;
        use Illuminate\Support\Facades\Auth;
        use Validator;
use Illuminate\Http\Request;

class AuthController extends BaseController
{

    public function login(Request $request)
            {
                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
                    $user = Auth::user(); 
                    $success['token'] =  $user->createToken('MyApp')-> accessToken; 
                    $success['name'] =  $user->name;
 
                    return $this->sendResponse($success, 'User login successfully.');
                } 
                else{ 
                    return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
                } 
            }
}
