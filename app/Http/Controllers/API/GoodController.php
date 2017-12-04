<?php
/**
 * File_Name:UserController.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 15:24
 */

namespace App\Http\Controllers\API;


use App\Components\GoodManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\RequestValidator;


class GoodController extends Controller
{
    /*
     * 根据类型获取服务列表
     *
     * By TerryQi
     *
     * 2017-10-08
     */
    function getGoodList(Request $request)
    {
        $data = $request->all();
        $goods = GoodManager::getGoodList($data);
        return ApiResponse::makeResponse(true, $goods, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据id获取咨询信息
     *
     * By TerryQi
     * 2017-10-08
     *
     */
    function getGoodById(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $zx_detail_info = GoodManager::getGoodById($data['id']);
        return ApiResponse::makeResponse(true, $zx_detail_info, ApiResponse::SUCCESS_CODE);
    }

}