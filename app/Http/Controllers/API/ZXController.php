<?php
/**
 * File_Name:UserController.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 15:24
 */

namespace App\Http\Controllers\API;


use App\Components\ZXManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\RequestValidator;

class ZXController extends Controller
{
    /*
     * 根据类型获取资讯列表
     *
     * By TerryQi
     *
     * 2017-10-08
     */
    function getZXList(Request $request)
    {
        $data = $request->all();
        $zx_infos = ZXManager::getZXList($data);
        return ApiResponse::makeResponse(true, $zx_infos, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据id获取咨询信息
     *
     * By TerryQi
     * 2017-10-08
     *
     */
    function getZXById(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $zx_detail_info = ZXManager::getZXById($data);
        return ApiResponse::makeResponse(true, $zx_detail_info, ApiResponse::SUCCESS_CODE);
    }


}