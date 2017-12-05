<?php
/**
 * File_Name:UserController.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 15:24
 */

namespace App\Http\Controllers\API;

use App\Components\EnterManager;
use App\Components\GoodManager;
use App\Components\OrderManager;
use App\Components\UserManager;
use App\Models\Good;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Response;
use Yansongda\Pay\Pay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\RequestValidator;
use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{

    private function getConfig()
    {
        $config = [
            'wechat' => [
                'app_id' => 'wx07fc3214de557db6',
                'mch_id' => '1491165562',
                'notify_url' => 'https://fhq.isart.me/api/wxpay/notify',
                'key' => 'LltO2bE5r4sGVJZjn0zd4aRsNQHezIiJ',
                'cert_client' => app_path() . '/cert/apiclient_cert.pem',
                'cert_key' => app_path() . '/cert/apiclient_key.pem',
            ],
        ];
        return $config;
    }

    /*
     * 支付接口
     *
     * By TerryQi
     *
     * 2017-10-13
     *
     */
    public function prepay(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'en_id' => 'required',
            'good_id' => 'required',
            'count' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //处理相关工作
        $good = Good::where('id', '=', $data['good_id'])->get()->first();
        $order = new Order();
        $order = OrderManager::setOrder($order, $data);
        $user = User::where('id', '=', $data['user_id'])->get()->first();
        //配置信息
        $order->trade_no = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $order->total_fee = $good->price * $data['count'];
        $order->description = $good->title;
        $order->notify_url = 'https://fhq.isart.me/api/wxpay/notify';
        //进行支付
        $config_biz = [
            'out_trade_no' => $order->trade_no,
            'total_fee' => $order->total_fee,
            'body' => $order->description,
            'spbill_create_ip' => '47.93.127.4',
            'openid' => $user->xcx_openid,
        ];
        //配置config
        $config = $this->getConfig();
        $pay = new Pay($config);
        $pay_result = $pay->driver('wechat')->gateway('mp')->pay($config_biz);
        $order->prepay_id = str_replace("prepay_id=", "", $pay_result['package']);  //获取prepay_id
        $order->save();     //保存订单信息
        return ApiResponse::makeResponse(true, $pay_result, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 监听支付结果通知
     *
     * By TerryQi
     *
     * 2017-11-20
     *
     */
    public function notify(Request $request)
    {
        $pay = new Pay($this->getConfig());
        $verify = $pay->driver('wechat')->gateway('mp')->verify($request->getContent());
        $out_trade_no = $verify['out_trade_no'];    //获取外部订单号
        $order = Order::where('trade_no', $out_trade_no)->get()->first();
        $order->status = '2';       //保存支付状态，支付成功
        $order->save();
        //应答接口
        $response_xml = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
        echo $response_xml;
        exit;
    }


    /*
     * 根据用户id获取订单列表
     *
     * By TerryQi
     *
     * 2017-10-13
     *
     */
    public function getListByUserId(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $orders = Order::where('user_id', '=', $data['user_id'])->orderby('id', 'desc')->get();
        foreach ($orders as $order) {
            $order = OrderManager::getOrderDetailInfo($order);
        }
        return ApiResponse::makeResponse(true, $orders, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据企业id获取订单列表
     *
     * By TerryQi
     *
     * 2017-10-13
     *
     */
    public function getListByEnterId(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'en_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $orders = Order::where('en_id', '=', $data['en_id'])->orderby('id', 'desc')->get();
        foreach ($orders as $order) {
            $order = OrderManager::getOrderDetailInfo($order);
        }
        return ApiResponse::makeResponse(true, $orders, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 获取订单详情
     *
     * By TerryQi
     *
     * 2017-10-13
     *
     */
    public function getById(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $order = Order::where('id', '=', $data['id'])->first();
        $order = OrderManager::getOrderDetailInfo($order);
        return ApiResponse::makeResponse(true, $order, ApiResponse::SUCCESS_CODE);
    }

}