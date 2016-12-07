@extends('weixin.layout.master')
@section('title','登录页')
@section('css')

@endsection
@section('content')
    <header>
        <a class="headl fanh" href="javascript:history.back()"></a>
        <h1>注册</h1>

    </header>

    <div class="pa2">
        <img class="lglg" src="images/lglogo.jpg">
        <div class="lgbox">
            <h2 class="lgh2">塞茵苏订奶查询系统</h2>
            <div class="pa2">
                <div class="lgli">
                    <span class="lg-l">手机号码：</span>
                    <input class="lgin1" name="phone_number" pattern = "^1[345678][0-9]{9}$" id="phone_number" type="text" placeholder="请输入订奶人的手机号码">
                </div>
                <div class="lgli">
                    <span class="lg-l">验 证 码：</span>
                    <input class="lgin2" id="code" name="verify_code" type="text" placeholder="请输入验证码">
                    <button type="button" class="fsyzm" onclick="send_verify_code_to_phone();">发送验证码</button>
                </div>
                <button class="lgcx"  type="button" onclick="check_verify_code();">点击查询</button>
            </div>
        </div>
        <div class="lgcopy">
            版权所有：内蒙古圣牧低温乳品有限公司<br>
            网站策划：宇盈科技
        </div>
    </div>
@endsection
@section('script')
    <script>
        function send_verify_code_to_phone()
        {
            var phone_number = $('#phone_number').val();
            var pattern = $('#phone_number').attr('pattern');

            if(pattern && phone_number.match(new RegExp(pattern)))
            {

                $('#phone_number').prop('readonly', true);

                $.ajax({
                    type:"POST",
                    url: SITE_URL + "weixin/api/send_verify_code_to_phone",
                    data: {
                        "phone_number": phone_number
                    },
                    success:function(data)
                    {
                        if(data.status == "success")
                        {
                            show_info_msg('验证码发送，请输入正确的验证码.');

                        } else {
                            show_warning_msg('客户不存在');
                            $('#phone_number').prop('readonly', false);
                        }
                    },
                    error: function(data)
                    {
                        console.log(data);
                        $('#phone_number').prop('readonly', false);
                    }
                });
            } else {
                show_warning_msg('请输入有效的电话号码');
            }

        }

        function check_verify_code()
        {
            var phone_number = $('#phone_number').val();
            var input_code = $('#code').val();
            var group_id  = $('#group_id').val();

            $.ajax({
                type:"post",
                url: SITE_URL + "weixin/api/check_verify_code",
                data: {
                    "phone_number": phone_number,
                    "code": input_code,
                },
                success:function(data)
                {
                    console.log(data);
                    if(data.status == "success")
                    {
                        $('#phone_number').prop('readonly', false);

                        @if(isset($to))
                                window.location = SITE_URL+"weixin/querendingdan";
                        @else
                                window.location = SITE_URL+"weixin/qianye";
                        @endif

                    } else {
                        show_warning_msg('验证代码不正确，请重试.');
                        $('#phone_number').prop('readonly', false);
                    }

                },
                error: function(data)
                {
                    console.log(data);
                }
            });

        }
    </script>
@endsection
