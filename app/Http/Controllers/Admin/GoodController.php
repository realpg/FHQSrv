<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin;

use App\Components\QNManager;
use App\Components\GoodManager;
use App\Models\AD;
use App\Models\Admin;
use App\Models\Good;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class GoodController
{

    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $goods = Good::orderBy('seq', 'desc')->get();
//        dd($ads);
        return view('admin.good.index', ['admin' => $admin, 'datas' => $goods]);
    }

    //设置状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'opt' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        //opt必须为0或者1
        $opt = $data['opt'];
        if (!($opt == '0' || $opt == '1')) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数,opt必须为0或者1，现值为' . $opt]);
        }
        $good = Good::where('id', '=', $id)->first();
        $good->status = $opt;
        $good->save();
        return redirect('/admin/good/index');
    }


    //删除资讯
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数资讯id$id']);
        }
        $good = Good::find($id);
        $good->delete();
        return redirect('/admin/good/index');
    }


    //新建或编辑资讯-get
    public function edit(Request $request)
    {
        $data = $request->all();
        $good = new Good();
        if (array_key_exists('id', $data)) {
            $good = Good::find($data['id']);
        }
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        return view('admin.good.edit', ['admin' => $admin, 'data' => $good, 'upload_token' => $upload_token]);
    }

    //新建或编辑资讯->post
    public function editPost(Request $request)
    {
        $data = $request->all();
        //专门处理role
        if (array_key_exists('flag', $data)) {
            $data['flag'] = '1';
        }
        $good = new Good();
        //存在id是保存
        if (array_key_exists('id', $data)) {
            $good = Good::find($data['id']);
        }
        $good = GoodManager::setGood($good, $data);
        $good->save();
        return redirect('/admin/good/index');
    }

}