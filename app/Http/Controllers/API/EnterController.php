<?php
/**
 * File_Name:UserController.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 15:24
 */

namespace App\Http\Controllers\API;


use App\Components\EnterManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Enter;
use Illuminate\Http\Request;
use App\Components\RequestValidator;

class EnterController extends Controller
{
    /*
     * 根据user_id获取企业列表信息
     *
     * By TerryQi
     *
     * 2017-10-10
     */
    function getListByUserId(Request $request)
    {
        $data = $request->all();
        //合规校验user_idd
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $enters = Enter::where('user_id', $data['user_id'])->where('status', '1')->orderBy('id', 'desc')->get();
        return ApiResponse::makeResponse(true, $enters, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据id获取企业详情
     *
     *By TerryQi
     *
     * 2017-10-10
     *
     */
    function getById(Request $request)
    {
        $data = $request->all();
        $enter = Enter::where('id', $data['id'])->where('status', '1')->first();
        return ApiResponse::makeResponse(true, $enter, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 删除企业信息
     *
     * By TerryQi
     * 2017-10-08
     *
     */
    function del(Request $request)
    {
        $data = $request->all();
        //合规校验id
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $enter = Enter::find($data['id']);
        if ($enter != null) {
            $enter->status = '0';
            $enter->save();
        }
        return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
    }


    /*
     * 新建、编辑企业信息
     *
     * By TerryQi
     *
     * 2017-10-10
     *
     */
    function edit(Request $request)
    {
        $data = $request->all();
        //合规校验id
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $enter = new Enter();
        if (array_key_exists('id', $data)) {
            $enter = Enter::find($data['id']);
        }
        $enter = EnterManager::setEnter($enter, $data);
        $enter->save();
        return ApiResponse::makeResponse(true, $enter, ApiResponse::SUCCESS_CODE);
    }

}