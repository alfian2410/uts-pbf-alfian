<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator; //panggil library untuk memvalidasi inputan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //panggil library auth
use Firebase\JWT\JWT;//Panggil library JWT
use Carbon\Carbon; //Panggil library Carbon


class authController extends Controller
{
    //
    public function login(Request $request){
        //validasi inputan
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        //jika inputan salah
        if($validator->fails()){
            return response()->json($validator->messages(),422);
        }

        //kondisi inputan ada di tabel users
        if(Auth::attempt($validator->validated())){
            //isian token
            $payload = [
                'name'=> Auth::user()->name,
                'role'=> Auth::user()->role,
                'email'=> Auth::user()->email,
                'iat'=> Carbon::now()->timestamp,//waktu token di generate
                'exp'=> Carbon::now()->timestamp + 60*60*2 //waktu token expire

            ];

            $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');

            return response()->json([
                'messages'=>'Token Berhasil digenerate',
                'name'=>Auth::user()->name,
                'token'=>'Bearer '.$jwt
            ],200);
        }


        return response()->json(
            ['messages'=>"Pengguna tidak ditemukan"],422
        );
    }
}