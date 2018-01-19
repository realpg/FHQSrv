<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin;

use App\Components\ADManager;
use App\Components\QNManager;
use App\Libs\CommonUtils;
use App\Models\AD;
use App\Models\Enter;
use App\Models\User;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;
use Excel;
use GuzzleHttp\Client;

class EnterController
{
    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $enters = Enter::orderBy('id', 'desc')->get();
//        dd($enters);

        return view('admin.enter.index', ['admin' => $admin, 'datas' => $enters]);
    }

    //搜索-按照企业名称
    public function search(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        if (!array_key_exists('name', $data)) {
            $data['name'] = '';
        }
        $enters = Enter::where('name', 'like', "%" . $data['name'] . "%")->get();
        return view('admin.enter.index', ['admin' => $admin, 'datas' => $enters]);
    }

    //企业信息详情
    public function info(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');

        $enter = Enter::where('id', '=', $data['id'])->get()->first();
        if ($enter == null) {
            return redirect('/admin/enter/index');
        }
        $user = User::where('id', '=', $enter->user_id)->get()->first();
        return view('admin.enter.info', ['admin' => $admin, 'datas' => $enter, 'user' => $user]);
    }

    //删除企业
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数企业id$id']);
        }
        $enter = Enter::find($id);
        $enter->delete();
        return redirect('/admin/enter/index');
    }
    //打印所有企业
    public function exportAllEnter(Request $request)
    {
        $data = $request->all();
        $enters = Enter::orderBy('id', 'desc')->get();
        $cellData = [
            ['序号', '企业名称', '营业执照', '地址', '邮编', '税号', '税务登记证', '法人姓名', '法人身份证正面', '法人身份证反面', '法人手持身份证', '法人身份证号', '法人电话',],
        ];
        foreach ($enters as $enter) {
            array_push($cellData, [$enter->id, $enter->name, $enter->lice_img, $enter->address, $enter->code, $enter->tax_code, $enter->tax_img, $enter->owner, $enter->owner_card1, $enter->owner_card2, $enter->owner_card3, $enter->owner_no, $enter->owner_tel]);
        }
        Excel::create('AllEnter', function ($excel) use ($cellData) {
            $excel->sheet('score', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
    //打印企业
    public function export(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $enter = Enter::where('id', '=', $data['id'])->get()->first();
        if ($enter == null) {
            return redirect('/admin/enter/index');
        }
        $user = User::where('id', '=', $enter->user_id)->get()->first();
//        return view('admin.enter.info', ['admin' => $admin, 'datas' => $enter, 'user' => $user]);
        $cellData = [
            ['序号', '企业名称', '营业执照', '地址', '邮编', '税号', '税务登记证', '法人姓名', '法人身份证正面', '法人身份证反面', '法人手持身份证', '法人身份证号', '法人电话',],
            [$enter->id, $enter->name, $enter->lice_img, $enter->address, $enter->code, $enter->tax_code, $enter->tax_img, $enter->owner, $enter->owner_card1, $enter->owner_card2, $enter->owner_card3, $enter->owner_no, $enter->owner_tel,],
        ];
        Excel::create('学生成绩', function ($excel) use ($cellData) {
            $excel->sheet('score', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
    //打印企业营业执照
        public function lice_img(Request $request)
       {
           $data = $request->all();
           $enter = Enter::where('id', '=', $data['id'])->get()->first();
           $client = new Client(['verify' => false]);  //忽略SSL错误
           $response = $client->get($enter->lice_img, ['save_to' => realpath(base_path('public')).'/lice_img.jpg']); //保存远程url到文件
           return response()->download(realpath(base_path('public')).'/lice_img.jpg', $enter->name." 企业营业执照.jpg");
        }
    //打印税务登记照片
    public function tax_img(Request $request)
    {
        $data = $request->all();
        $enter = Enter::where('id', '=', $data['id'])->get()->first();
        $client = new Client(['verify' => false]);  //忽略SSL错误
        $client->get($enter->tax_img, ['save_to' => realpath(base_path('public')).'/tax_img.jpg']); //保存远程url到文件
        return response()->download(realpath(base_path('public')).'/tax_img.jpg', $enter->name." 税务登记照片.jpg");
    }
    //打印证件照片2
    public function owner_card1(Request $request)
    {
        $data = $request->all();
        $enter = Enter::where('id', '=', $data['id'])->get()->first();
        $client = new Client(['verify' => false]);  //忽略SSL错误
        $client->get($enter->owner_card1, ['save_to' => realpath(base_path('public')).'/owner_card1.jpg']); //保存远程url到文件
        return response()->download(realpath(base_path('public')).'/owner_card1.jpg', $enter->name." 证件照片1.jpg");
    }
    //打印证件照片2
    public function owner_card2(Request $request)
    {
        $data = $request->all();
        $enter = Enter::where('id', '=', $data['id'])->get()->first();
        $client = new Client(['verify' => false]);  //忽略SSL错误
        $client->get($enter->owner_card2, ['save_to' => realpath(base_path('public')).'/download/owner_card2.jpg']); //保存远程url到文件
        return response()->download(realpath(base_path('public')).'/owner_card2.jpg', $enter->name." 证件照片2.jpg");
    }
}