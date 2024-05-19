<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth; //panggil library auth
use Firebase\JWT\JWT;//Panggil library JWT
use Carbon\Carbon;


class googlecontroller extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        
        // Google user object dari google
        $userFromGoogle = Socialite::driver('google')->stateless()->user();


        // Ambil user dari database berdasarkan email
        $userFromDatabase = User::where('email', $userFromGoogle->getEmail())->first();

        //Jika email user sudah digunakan
        if ($userFromDatabase) {
            $payload = [
                'name'=> $userFromGoogle->getEmail(),
                'role'=> 'user',
                'iat'=> Carbon::now()->timestamp,//waktu token di generate
                'exp'=> Carbon::now()->timestamp + 60*60*2 //waktu token expire
    
            ];
            $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
            return response()->json([
                'messages'=>'Token Berhasil digenerate',
                'name'=>$userFromGoogle->getName(),
                'token'=>'Bearer '.$jwt
            ],200);

        }
        // Jika tidak ada user, maka buat user baru
        $newUser = User::create([
            'name' => $userFromGoogle->getName(),
            'email' => $userFromGoogle->getEmail(),
            'password' => bcrypt($userFromGoogle->getEmail())
        ]);

        $payload = [
            'name'=> $userFromGoogle->getEmail(),
            'role'=> 'user',
            'iat'=> Carbon::now()->timestamp,//waktu token di generate
            'exp'=> Carbon::now()->timestamp + 60*60*2 //waktu token expire

        ];
        //generate token
        $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
        //kirim token ke user
        return response()->json([
            'messages'=>'Register Berhasil',
            'name'=>$userFromGoogle->getName(),
            'token'=>'Bearer '.$jwt
        ],200);

    }
    //
}
