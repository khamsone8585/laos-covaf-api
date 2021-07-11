<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    //register [post]
    public function register(Request $request) {

        $validator = Validator::make($request->all(),[
            'user_name' => 'required|unique:users',
            'password' => 'required|min:3'
        ],[
            'user_name.required' => 'ກະລຸນາປ້ອນຊື່ກ່ອນ',
            'user_name.user_name' => 'ຮູບແບບຂື່ຜູ້ໃຊ້ບໍ່ຖືກຕ້ອງ',
            'user_name.unique' => 'ອີເມລນີ້ມີຜູ້ໃຊ້ງານໃນລະບົບແລ້ວ',
            'password.required' => 'ກະລຸນາປ້ອນລະຫັດຜ່ານກ່ອນ',
            'password.min' => 'ກະລຸນາປ້ອນລະຫັດຢ່າງໜ້ອຍ 3 ຕົວ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'message' => $validator->errors()
                ]
            ], 422);
        }

        //เพิ่ม user ใหม่ 
        // QueryBuilder

        $user = DB::table('users')->insert([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password)
        ]);
        
        // //ORM
        // $user = new User();
        // $user->username = $request->username;
        // $user->password = Hash::make($request->password); 
        // $user->save();

        return response()->json([
            'message' => 'ສະໝັກສະມາຊິກສຳເລັດ'
        ], 201);
    }

    //login [post]
    public function login(Request $request) {

        $validator = Validator::make($request->all(),[
            'user_name' => 'required',
            'password' => 'required|min:3'
        ],[
            'user_name.required' => 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ກ່ອນ',
            'user_name.user_name' => 'ຮູບແບບຊື່ຜູ້ໃຊ້ບໍ່ຖືກຕ້ອງ',
            'user_name.unique' => 'ອີເມລນີ້ມີຜູ້ໃຊ້ງານໃນລະບົບແລ້ວ',
            'password.required' => 'ກະລຸນາປ້ອນລະຫັດຜ່ານກ່ອນ',
            'password.min' => 'ກະລຸນາປ້ອນລະຫັດຢ່າງໜ້ອຍ 3 ຕົວ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'message' => $validator->errors()
                ]
            ], 422);
        }

        //ກວດ email ແລະ password ວ່າຖືກຕ້ອງບໍ
        $user = User::where('user_name', $request->user_name)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'user_name' => ['ອີເມລຫຼືລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ'],
            ]);
        }

        $token = $user->createToken('token_name')->plainTextToken;
        $personal_token = PersonalAccessToken::findToken($token);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $personal_token->created_at->addMinutes(config('sanctum.expiration'))
        ], 200);
    }

    //logout [post]
    public function logout(Request $request) {
        //ຫາຄ່າຄໍລັມ id ຂອງ token ທີ່ກຳລັງລັອກອິນຢູ່
        $id = $request->user()->currentAccessToken()->id;        

        //ລົບ record token user ໃນຕາຕະລາງຂໍ້ມູນ
        $request->user()->tokens()->where('id', $id)->delete();    

        return response()->json([
            'message' => 'ອອກຈາກລະບົບສຳເລັດ'
        ], 200);
    }
}
