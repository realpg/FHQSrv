<?php
/**
 * File_Name:UserController.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 15:24
 */

namespace App\Http\Controllers\API;

use App\Components\HomeManager;
use App\Components\UserManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Libs\wxDecode\ErrorCode;
use App\Libs\wxDecode\WXBizDataCrypt;
use App\Models\ViewModels\HomeView;
use Illuminate\Http\Request;
use App\Components\RequestValidator;

class UserController extends Controller
{

    const APPID = "wx07fc3214de557db6";
    const APPSECRET = "f840ff7278f22ae4208f75af156057f0";


    /*
     * 根据code获取小程序的openid（启用）
     *
     * By TerryQi
     *
     * 2017-09-28
     */
    public function getOpenIdForXCX(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'code' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $code = $data['code'];  //获取小程序code
        //触发后端
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . self::APPID . "&secret=" . self::APPSECRET . "&code=" . $code . "&grant_type=authorization_code");//通过code换取网页授权access_token
        $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
        $array = get_object_vars($jsondecode);//转换成数组
        if (array_key_exists('openid', $array)) {
            return ApiResponse::makeResponse(true, $array, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, ApiResponse::$errorMassage[ApiResponse::UNKNOW_ERROR], ApiResponse::UNKNOW_ERROR);
        }
    }

    /*
     * 通过code换取open_id和session_key
     *
     * By TerryQi
     *
     * 2017-10-08
     */
    public function getXCXOpenId(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'code' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $code = $data['code'];  //获取小程序code
        //触发后端
        $ret_str = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=" . self::APPID . "&secret=" . self::APPSECRET . "&js_code=" . $code . "&grant_type=authorization_code");//通过code换取网页授权access_token

        $ret_val = json_decode($ret_str, true);

        return ApiResponse::makeResponse(true, $ret_val, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 用户登录
     *
     * @request 用户相关信息
     *
     * By TerryQi
     *
     */
    public function login(Request $request)
    {
        $data = $request->all();    //request转array
        $user = null;          //返回user信息
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'account_type' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //如果是小程序
        if ($data['account_type'] == 'xcx') {
            //合规校验account_type
            $requestValidationResult = RequestValidator::validator($request->all(), [
                'xcx_openid' => 'required',
            ]);
            if ($requestValidationResult !== true) {
                return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
            }
            //进行注册
            $user = UserManager::login($data);
        }
        //如果注册失败，返回失败
        if ($user == null) {
            return ApiResponse::makeResponse(false, ApiResponse::$errorMassage[ApiResponse::REGISTER_FAILED], ApiResponse::REGISTER_FAILED);
        } else {
            return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
        }
    }

    /*
     * 根据id更新用户信息
     *
     * @request id:用户id
     *
     * By TerryQi
     *
     */
    public function updateUserById(Request $request)
    {
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $data = $request->all();
        $user = UserManager::updateUser($data);
        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 根据id获取用户信息
     *
     * @request id：用户id
     *
     * By TerryQi
     *
     * 2017-09-28
     *
     */
    public function getUserById(Request $request)
    {
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $data = $request->all();
        $user = UserManager::getUserInfoById($data['id']);
        if ($user) {
            return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, ApiResponse::$errorMassage[ApiResponse::NO_USER], ApiResponse::NO_USER);
        }
    }

    /*
     * 根据id获取用户信息带token
     *
     * @request id：用户id
     *
     * By TerryQi
     *
     * 2017-09-28
     *
     */
    public function getUserInfoByIdWithToken(Request $request)
    {
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);

        $data = $request->all();

        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        $user = UserManager::getUserInfoByIdWithToken($data['id']);
        if ($user) {
            return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, ApiResponse::$errorMassage[ApiResponse::NO_USER], ApiResponse::NO_USER);
        }
    }

    /*
     * 解密encryptedData，用于返回uniodcode等信息
     *
     * By TerryQi
     *
     * 2017-10-08
     *
     */
    public function encryptedData(Request $request)
    {
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'encryptedData' => 'required',
            'iv' => 'required',
            'session_key' => 'required',
        ]);
        $data = $request->all();
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        $pc = new WXBizDataCrypt(self::APPID, $data['session_key']);

        $errCode = $pc->decryptData($data['encryptedData'], $data['iv'], $encode_val);
        dd("pc" . $errCode);
    }

    /*
     * 获取首页信息
     *
     * By TerryQi
     *
     * 2017-10-08
     */
    public function getHome(Request $request)
    {
        $data = $request->all();
        $result = HomeManager::getHome($data);
        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }


}