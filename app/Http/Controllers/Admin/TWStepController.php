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
use App\Components\TWManager;
use App\Components\ZXManager;
use App\Models\AD;
use App\Models\Admin;
use App\Models\Good;
use App\Models\TWStep;
use App\Models\ZX;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class TWStepController
{

    //删除步骤
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数步骤id$id']);
        }
        $twStep = TWStep::find($id);
        $redirect_url = '/admin/tw/edit?f_id=' . $twStep->f_id . '&f_type=' . $twStep->f_type;
//        dd($redirect_url);
        $twStep->delete();
        return redirect($redirect_url);
    }


    //新建或编辑步骤-get
    public function edit(Request $request)
    {
        $data = $request->all();

        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'f_id' => 'required',
            'f_type' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $f_data = null;
        //获取父层信息
        if ($data['f_type'] == '0') { //资讯
            $f_data = ZX::find($data['f_id']);
        }
        if ($data['f_type'] == '1') {     //服务
            $f_data = Good::find($data['f_id']);
        }
        $f_data->f_type = $data['f_type'];  //设置f_type，在取数据过程中丢失
        //获取已经有的图文信息
        $twSteps = TWStep::where('f_id', '=', $data['f_id'])->where('f_type', '=', $data['f_type'])->get();
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        return view('admin.tw.edit', ['admin' => $admin, 'datas' => $twSteps, 'f_data' => $f_data, 'upload_token' => $upload_token]);
    }

    //新建或编辑步骤->post
    public function editPost(Request $request)
    {
        $data = $request->all();
        $twStep = new TWStep();
        $twStep = TWManager::setTW($twStep, $data);
        $twStep->save();
        return redirect('/admin/tw/edit?f_id=' . $data['f_id'] . '&f_type=' . $data['f_type']);
    }

}