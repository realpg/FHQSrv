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


class EnterController
{

    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $enters = Enter::orderBy('id', 'desc')->get();
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

}