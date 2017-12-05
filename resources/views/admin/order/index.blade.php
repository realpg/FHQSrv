@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <small>订单管理</small>
        </h1>
        <ol class="breadcrumb">

        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div id="tip_div" class="alert alert-info hidden" role="alert">请在执行相关请求，完成后页面将自动刷新</div>
        <!--列表-->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>订单号</th>
                                <th>商品名</th>
                                <th>下单时间</th>
                                <th>企业名</th>
                                <th>下单用户</th>
                                <th>状态</th>
                                <th>总金额</th>
                                <th>数量</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->trade_no}}</td>
                                    <td>{{$data->good->good_info->title}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td>
                                        @if($data->enter)
                                            {{$data->enter->name}}
                                        @endif
                                    </td>
                                    <td>{{$data->user->nick_name}}</td>
                                    <td>
                                        @if($data->status === '0')
                                            初始
                                        @endif
                                        @if($data->status === '1')
                                            过期
                                        @endif
                                        @if($data->status === '2')
                                            支付成功
                                        @endif
                                        @if($data->status === '3')
                                            支付失败
                                        @endif
                                        @if($data->status === '4')
                                            已经退款
                                        @endif
                                    </td>
                                    <td>
                                        {{$data->total_fee/100}}元
                                    </td>
                                    <td>
                                        {{$data->count}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-5">

            </div>
            <div class="col-sm-7 text-right">
                {!! $datas->links() !!}
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="application/javascript">

        function showTip() {
            $("#tip_div").removeClass('hidden');
        }
    </script>
@endsection