<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Foundation\Validation\ValidateRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Mail\UserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class UserController extends Controller
{
    use AuthorizesRequests, DispatchesJobs;

    public function ___contructor(){
       $this->middleware('auth:api', ['except'=>['signIn','register']]);
    }

    public function signIn(Request $request){
        $request->validate([
         'email'=>'required|email',
         'password'=>'required|min:6'
        ]);

        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
   
    public function register(Request $request){
      $request->validate([
      'firstname'=>'required|string',
      'lastname'=>'required|string',
      'email'=>'required|email|unique:users',
      'password'=>'required|string',
      ]);
       $user= new User();
       $user->firstname=$request->firstname;
       $user->lastname=$request->lastname;
       $user->email=$request->email;
       $user->user_type= 'user';
       $user->password=Hash::make($request->password);
       $user->status= User::pending;
       
       $admins = User::all();
       if($user->save()){
         foreach($admins as $admin){
          if($admin['user_type'] === 'admin'){
            Mail::to($admin->email)->send(new UserMail($user->email));
          }
         }
         return response()->json(['message'=>'Registration Successful']);
       }else{
         return response()->json(['message'=>'Fill up the fields with accurately']);
       }
        return response()->json(['message'=>'hello from signup method of user controller']);
    }

  
    public function approveUser(Request $request,$id){
        $request->validate([
          'status'=>'required|string' //approved || rejected
          ]);
        $user = User::find($id);
        $admin = Auth::user();
        if($admin->user_type === 'admin'){
          $user->status = Str::lower($request->status);
          $user->save();
          return response()->json(['message'=>'user has been '.$request->status]);
         }
        return response()->json(['message'=>'Only admin can approve user']);
      
      }

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    
}
