<?php

namespace App\Http\Controllers;
use App\Models\Categories;
use App\Models\products;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class productcontroller extends Controller
{
    public function read(){
        $Products = Products::all();
        return response()->json($Products)->setStatusCode(200);
    }

    public function create(Request $request){
        //Melakukan validasi inputan
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|max:255|string',
            'description' => 'sometimes|string',
            'price'=> 'required|numeric',
            'category_id' => 'required|exists:categories,name',
            'expired_at'=>'required|date',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            

        ]);
        $imageName = null;
        $jwt = $request->bearerToken();
        $decode = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));
        $email = $decode->email;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // Membuat nama file unik
            $image->storeAs('public/images',$imageName);
            
        }
        //Apabila inputan salah
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }   
        //inputan yang sudah benar
        $validated = $validator->validate();

        $category = Categories::where('name', $validated['category_id'])->first();
        // input ke tabel Products
        Products::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $category->id, // Gunakan ID kategori yang ditemukan
            'expired_at' => $validated['expired_at'],
            'image' => 'http://127.0.0.1:8000/storage/images/'.$imageName,
            'modified_by' => $email,
        ]);
        return response()->json('Produk berhasil disimpan')->setStatusCode(201);
    }
    public function update(Request $request,$id){
        $jwt = $request->bearerToken();
        $decode = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));
        $email = $decode->email;

        //Melakukan validasi inputan
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|max:255|string',
            'description' => 'sometimes|string',
            'price'=> 'required|numeric',
            'category_id' => 'required|exists:categories,name',
            'expired_at'=>'required|date',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ]);
        //Apabila inputan salah
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // Membuat nama file unik
            $image->storeAs('public/images',$imageName);
            
            
        }
        //inputan yang sudah benar
        $validated = $validator->validate();

        //Pencarian by ID
        $Products = Products::find($id);

        $category = Categories::where('name', $validated['category_id'])->first();
        if($Products){
    
            $Products->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category_id' => $category->id, // Gunakan ID kategori yang ditemukan
                'expired_at' => $validated['expired_at'],
                'image' => 'http://127.0.0.1:8000/storage/app/public/images/'.$imageName?? $Products->image, // Jika tidak ada gambar baru, gunakan gambar lama
                'modified_by' => $email,
            ]);
        
            
            return response()->json('Produk berhasil diubah')->setStatusCode(201);
        }
        return response()->json('Data produk tidak ditemukan')->setStatusCode(404); 
    }
    public function delete($id){

        //cari id yang akan di delete
        $checkData = Products::find($id);

        //kondisi jika datanya ada
        if($checkData){
            //Code untuk soft delete
            Products::where('id', $id)->delete();

            
            // Products::destroy($id);
            return response()->json('produk berhasil dihapus')->setStatusCode(200);
        }
        return response()->json('Data produk tidak ditemukan')->setStatusCode(404);
    }
    //
}
