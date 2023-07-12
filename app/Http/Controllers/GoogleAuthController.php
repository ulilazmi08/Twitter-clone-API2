<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
class GoogleAuthController extends Controller
{
    public function redirect()  
    {
        return Socialite::driver('google')->stateless()->with(
            ['client_id' => '808195630701-hr4ugpe652kd1srqu59l26h8svv1dl9u.apps.googleusercontent.com'],
            ['client_secret' => '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'],
            ['redirect' => 'http://127.0.0.1:8000/api/auth/google/callback'])->redirect()->getTargetUrl();
    }
    public function callbackGoogle() 
    {
        try {
            $google_user = Socialite::driver('google')->stateless()->user();
            $user = User::where('google_id', $google_user->getId())->first();
            
            if (!$user) {
                $newuser = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                ]);
                Auth::login($newuser);
                return response()->json($user);
           
            }  else {
                Auth::login($user);
                return response()->json($user);
                
            }
        } catch (\Throwable $th) {  
            dd('Something went wrong !' . $th->getMessage());
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}
