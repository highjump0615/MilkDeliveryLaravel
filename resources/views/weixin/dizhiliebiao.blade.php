@extends('weixin.layout.master')
@section('title','地址列表')
@section('css')

@endsection
@section('content')
    <header>
        @if(isset($order) && isset($type))
            <a class="headl fanh" href="{{url('weixin/show_xuedan').'?order='.$order.'&&type='.$type.'&&from=xuedan'}}"></a>
        @else
            <a class="headl fanh" href="{{url('weixin/querendingdan')}}"></a>
    @endif
    <!--a class="headl fanh" href="javascript:history.back();"></a-->
        <h1>管理收货地址</h1>
    </header>

    @forelse($address_list as $a)
        <div class="addrbox">
            <form id="select-form{{$a->id}}" class="sel_adr_form" data-id="{{$a->id}}" method="post"
                  action="{{url('/weixin/select_address')}}">
                <input type="hidden" name="address" value="{{$a->id}}">
                @if(isset($order) && isset($type))
                    <input type="hidden" name="order" value="{{$order}}">
                    <input type="hidden" name="type" value="{{$type}}">
                @endif
                <div class="adrtop pa2t">
                    <p>{{$a->name}} {{$a->phone}}<br>{{$a->address}}</p>
                    <p>{{$a->sub_address}}</p>
                </div>
            </form>
            <div class="mrsz clearfix pa2t">
                    <span class="adrdz" style="cursor:pointer;" onclick="select_address({{$a->id}})">
                        <input name="dzrad" type="radio" value="" style="cursor:pointer;"
                               @if($a->primary == 1)
                               checked
                                @endif
                        >默认地址
                    </span>

                <span style="float:right">

                    <form id='delete-form{{$a->id}}' method="post" action="{{url('/weixin/delete_address')}}">
                         @if(isset($order) && isset($type))
                            <input type="hidden" name="order" value="{{$order}}">
                            <input type="hidden" name="type" value="{{$type}}">
                        @endif
                        @if(isset($order) && isset($type))
                            <a href="{{url('/weixin/dizhitianxie?address='.$a->id.'&&order='.$order.'&&type='.$type)}}">
                                <span class="glyphicon glyphicon-edit">编辑</span>
                            </a> &nbsp;
                        @else
                            <a href="{{url('/weixin/dizhitianxie?address='.$a->id)}}">
                                <span class="glyphicon glyphicon-edit">编辑</span>
                            </a> &nbsp;
                        @endif
                        <input type="hidden" name="address" value="{{$a->id}}">
                        <span class="glyphicon glyphicon-trash" onclick="delete_address({{$a->id}});"
                              style="cursor:pointer">删除</span>
                    </form>
                </span>
            </div>
            <hr>
        </div>

    @empty
    @endforelse

    <div class="addrbox">
        <div class="adrtop pa2t">
            @if(isset($order) && isset($type))
                <a href="{{url('/weixin/dizhitianxie').'?order='.$order.'&&type='.$type}}">
                    <span class="glyphicon glyphicon-plus">添加地址</span>
                </a> &nbsp;
            @else
                <a href="{{url('/weixin/dizhitianxie')}}">
                    <span class="glyphicon glyphicon-plus">添加地址</span>
                </a> &nbsp;
            @endif
        </div>
    </div>

    <div class="rqtc">
        <div class="rqtti rqtadr">确认要删除该地址吗？</div>
        <div class="rqbot">
            <span><button class="rqbot1">确定</button></span>
            <span><button class="rqbot2">取消</button></span>
        </div>
    </div>

@endsection
@section('script')
    <script>

        @if(isset($message))
            var msg = "{{$message}}";
            show_err_msg(msg);
        @endif
        $('.adrsc').click(function () {
            $('.rqtc').show();
        });

        $('.rqbot1').click(function () {
            delete_address();
            $('.rqtc').hide();
        });

        $('.rqbot2').click(function () {
            $('.rqtc').hide();
        });

        $('.sel_adr_form').click(function () {
            $(this).submit();
        });

        function delete_address(id) {
            $('#delete-form' + id).submit();
        }

        function select_address(id) {
            $('#select-form' + id).submit();
        }

    </script>
@endsection
