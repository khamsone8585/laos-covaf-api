<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('user_types')->get();
        return response()->json([
            'data' => $data
        ], 200);
    }
    public function search()
    {
        $query = request()->query('user_name');
        $keyword = '%'.$query.'%';
        $data = DB::table('user_type')->where('user_name','like', $keyword)->get();

        if ($data->isEmpty()) {
            return response()->json([
                'errors' => [
                    'status_code' => 404,
                    'message' => 'ບໍ່ພົບຂໍ້ມູນ'
                ]
            ],404);
        }

        return response()->json([
            'data' => $data
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'u_type_name' => 'required|unique:user_types|max:64',
            'u_type_nameEng' => 'required|unique:user_types|max:64',
        ],
        [
            'u_type_name.required' => 'ກະລຸນາປ້ອນຂໍ້ມູນກ່ອນ!!',
            'u_type_name.max' => 'ຊື່ປະເພດໜ້ອຍກວ່າ 64 ຕົວອັກສອນ!!',
            'u_type_nameEng.required' => 'ກະລຸນາປ້ອນຂໍ້ມູນກ່ອນ!!',
            'u_type_nameEng.max' => 'ຊື່ປະເພດໜ້ອຍກວ່າ 64 ຕົວອັກສອນ!!',
        ]
        );

        //Query Builder
        $data = array();
        $data['u_type_name'] = $request->u_type_name;
        $data['u_type_nameEng'] = $request->u_type_nameEng;
        // $data['modified_date'] = Carbon::now();
        DB::table('user_types')->insert($data);

        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DB::table('user_types')->where('id',$id)->first();
        return response()->json([
            'data' => $data
        ], 200);
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
        $data = array();
        $data['u_type_name'] = $request->u_type_name;
        $data['u_type_nameEng'] = $request->u_type_nameEng;
        // $data['modified_date'] = Carbon::now();
        DB::table('user_types')->where('id',$id)->update($data);

        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = DB::table('user_types')
        ->where('id',$id)
        ->delete($id);
        return response()->json([
            'data' => $data
        ], 200);
    }
}
