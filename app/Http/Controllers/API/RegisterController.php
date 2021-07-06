<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $register = DB::table('tb_register')->get();
        return response()->json($register, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $register = DB::table('tb_register')->insert([
                'firstname' => $request->firstname,
                'mobilephone' => $request->mobilephone,
                'email' => $request->email,
                'sex' => $request->sex,
                'age' => $request->age,
                'status' => $request->status,
                'create_date' => $request->create_date,
                'app_id' => $request->app_id,
                'token' => $request->token,
                'language' => $request->language,
                'verify' => $request->verify,
                'verify_code' => $request->verify_code,
                'mainaccount' => $request->mainaccount,
            ]);
            return response()->json([
                'message' => 'ເພີ່ມຂໍ້ມູນສຳເລັດ'
            ], 201);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
