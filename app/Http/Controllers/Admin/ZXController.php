<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\ZXManager;
use App\Models\AD;
use App\Models\Admin;
use App\Models\ZX;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class ZXController
{

    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $zxs = ZX::orderBy('seq', 'desc')->orderBy('id', 'desc')->get();
//        dd($ads);
        return view('admin.zx.index', ['admin' => $admin, 'datas' => $zxs]);
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
        $zx = ZX::where('id', '=', $id)->first();
        $zx->status = $opt;
        $zx->save();
        return redirect('/admin/zx/index');
    }


    //删除资讯
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数资讯id$id']);
        }
        $zx = ZX::find($id);
        $zx->delete();
        return redirect('/admin/zx/index');
    }


    //新建或编辑资讯-get
    public function edit(Request $request)
    {
        $data = $request->all();
        $zx = new ZX();
        if (array_key_exists('id', $data)) {
            $zx = ZX::find($data['id']);
        }
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        return view('admin.zx.edit', ['admin' => $admin, 'data' => $zx, 'upload_token' => $upload_token]);
    }

    //新建或编辑资讯->post
    public function editPost(Request $request)
    {
        $data = $request->all();
        //专门处理role
        if (array_key_exists('flag', $data)) {
            $data['flag'] = '1';
        } else {
            $data['flag'] = '0';
        }
        $zx = new ZX();
        //存在id是保存
        if (array_key_exists('id', $data)) {
            $zx = ZX::find($data['id']);
        }
        $zx = ZXManager::setZX($zx, $data);
        $zx->save();
        return redirect('/admin/zx/index');
    }

}