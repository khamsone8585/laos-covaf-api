<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //register [post]
    public function register(Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3'
        ],[
            'name.required' => 'ກະລຸນາປ້ອນຊື່ກ່ອນ',
            'email.required' => 'ກະລຸນາປ້ອນອີເມລ',
            'email.email' => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
            'email.unique' => 'ອີເມລນີ້ມີຜູ້ໃຊ້ງານໃນລະບົບແລ້ວ',
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
        //QueryBuilder

        // $user = DB::table('users')->insert([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password), 
        // ]);
        
        //ORM
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password); 
        $user->save();

        return response()->json([
            'message' => 'ສະໝັກສະມາຊິກສຳເລັດ'
        ], 201);
    }

    //login [post]
    public function login(Request $request) {

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:3',
            'device_name' => 'required'
        ],[
            'email.required' => 'ກະລຸນາປ້ອນອີເມລ',
            'email.email' => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
            'password.required' => 'ກະລຸນາປ້ອນລະຫັດຜ່ານກ່ອນ',
            'password.min' => 'ກະລຸນາປ້ອນລະຫັດຢ່າງໜ້ອຍ 3 ຕົວ',
            'device_name.required' => 'ກະລຸນາເລືອກອຸປະກອນ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'message' => $validator->errors()
                ]
            ], 422);
        }

        //ກວດ email ແລະ password ວ່າຖືກຕ້ອງບໍ
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['ອີເມລຫຼືລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ'],
            ]);
        }
    
        $token = $user->createToken($request->device_name)->plainTextToken;

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

    //get profile
    public function me(Request $request) {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ], 200);
    }


}
