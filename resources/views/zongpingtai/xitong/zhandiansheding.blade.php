@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('zongpingtai/xitong')}}">统计分析</a>
				</li>
				<li class="active">
					<strong>站点设定</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1">站点信息</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                    <div class="ibox-content">
                                        <form class="form-horizontal m-t-md" action="#">

                                            <div class="ibox-row">
                                                <h3>关闭站点</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">关闭站点</label>
                                                    <div class="col-sm-11">
                                                        <div class="radio radio-info radio-inline">
                                                            <input type="radio" id="inlineRadio1" value="option1" name="radioInline" checked="">
                                                            <label for="inlineRadio1"> 是 </label>
                                                        </div>
                                                        <div class="radio radio-inline">
                                                            <input type="radio" id="inlineRadio2" value="option2" name="radioInline">
                                                            <label for="inlineRadio2"> 否 </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="ibox-row">
                                                <h3>登录站点</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">

                                                    <label class="col-sm-1 control-label">是否开启验证码</label>
                                                    <div class="col-sm-11">
                                                        <div class="radio radio-info radio-inline">
                                                            <input type="radio" id="inlineRadio1" value="option1" name="radioInline" checked="">
                                                            <label for="inlineRadio1"> 是 </label>
                                                        </div>
                                                        <div class="radio radio-inline">
                                                            <input type="radio" id="inlineRadio2" value="option2" name="radioInline">
                                                            <label for="inlineRadio2"> 否 </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="ibox-row">
                                                <h3>版权信息</h3>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">是否显示首页</label>
                                                    <div class="col-sm-11">
                                                        <div class="inline">
                                                            <div class="radio radio-info radio-inline">
                                                                <input type="radio" id="inlineRadio1" value="option1" name="radioInline" checked="">
                                                                <label for="inlineRadio1"> 是 </label>
                                                            </div>
                                                            <div class="radio radio-inline">
                                                                <input type="radio" id="inlineRadio2" value="option2" name="radioInline">
                                                                <label for="inlineRadio2"> 否 </label>
                                                            </div>
                                                            <span class="help-block text-italic">没置"否"后,  打开地址时将直接 跳转到登页而, 否则会跳转到首页</span>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">网站简称</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="宇盈科技">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">网站标题</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="宇盈科技三级揹系統 ">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">网站URL</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="http://zxfx.yuyingkj.com/web/index.php">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">平台域名</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="http://zxfx.yuyingkj.com/web/index.php">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">keywords</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="微信, 微信, 微信公众平台, 公众平台二次开发, 公众平台开源软件">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">description</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="微信, 微信, 微信公众平台, 公众平台二次开发, 公众平台开源软件">
                                                    </div>
                                                </div>

                                             
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">favorite Icon</label>
                                                    <div class="col-sm-11">  
                                                        <!-- image-preview-filename input [CUT FROM HERE]-->
                                                        <div class="input-group">
                                                            <input type="text" class="form-control upload-filename"> <!-- don't give a name === doesn't send on POST/GET -->
                                                            <span class="input-group-btn">
                                                                <div class="btn btn-default btn-white upload-input">
                                                                    <span class="upload-input-title">选择图片</span>
                                                                    <input type="file" accept="image/png, image/jpeg, image/gif" class="upload"> <!-- rename it -->
                                                                </div>
                                                            </span>
                                                        </div><!-- /input-group image-preview [TO HERE]--> 
                                                    </div>

                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">前台二维码</label>
                                                    <div class="col-sm-11">  
                                                        <!-- image-preview-filename input [CUT FROM HERE]-->
                                                        <div class="input-group image-preview" data-original-title="" title="">
                                                            <input type="text" class="form-control image-preview-filename"> <!-- don't give a name === doesn't send on POST/GET -->
                                                            <span class="input-group-btn">
                                                                <!-- image-preview-clear button -->
                                                                <button type="button" class="btn btn-default  image-preview-clear" style="display:none;">
                                                                    <span class="glyphicon glyphicon-remove"></span> Clear
                                                                </button>
                                                                <!-- image-preview-input -->
                                                                <div class="btn btn-default btn-white image-preview-input">
                                                                    <span class="image-preview-input-title">选择图片</span>
                                                                    <input type="file" accept="image/png, image/jpeg, image/gif" name="input-file-preview" class="upload"> <!-- rename it -->
                                                                </div>
                                                            </span>
                                                        </div><!-- /input-group image-preview [TO HERE]--> 
                                                        <span class="help-block text-italic">此logo是指首页及登录页面logo, 建议尺寸220&#215;220.</span>
                                                    </div>

                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">前台logo</label>
                                                    <div class="col-sm-11">  
                                                        <!-- image-preview-filename input [CUT FROM HERE]-->
                                                        <div class="input-group image-preview" data-original-title="" title="">
                                                            <input type="text" class="form-control image-preview-filename"> <!-- don't give a name === doesn't send on POST/GET -->
                                                            <span class="input-group-btn">
                                                                <!-- image-preview-clear button -->
                                                                <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                                                    <span class="glyphicon glyphicon-remove"></span> Clear
                                                                </button>
                                                                <!-- image-preview-input -->
                                                                <div class="btn btn-default btn-white image-preview-input">
                                                                    <span class="image-preview-input-title">选择图片</span>
                                                                    <input type="file" accept="image/png, image/jpeg, image/gif" name="input-file-preview" class="upload"> <!-- rename it -->
                                                                </div>
                                                            </span>
                                                        </div><!-- /input-group image-preview [TO HERE]--> 
                                                        <span class="help-block text-italic">此logo是指首页及登录页面logo, 建议尺寸220&#215;50.</span>
                                                    </div>

                                                </div>

                                                
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">第三方统计代码</label>
                                                    <div class="col-sm-11">
                                                        <textarea rows="10"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">底部右侧信息(上)</label>
                                                    <div class="col-sm-11">
                                                        <textarea  rows="10"></textarea>
                                                        <span class="help-block text-italic">自定义 底部左侧信息, 支持HTML</span>
                                                    </div>

                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">底部左侧信息(下)</label>
                                                    <div class="col-sm-11">
                                                        <textarea  rows="10"></textarea>
                                                        <span class="help-block text-italic">自定义 底部左侧信息, 支持HTML</span>
                                                    </div>
                                                    
                                                </div>


                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">联系人</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="于經理">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">联系电话</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="15313908875">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">QQ</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="80738165">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">邮箱</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">公司名称</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="北京宇盈科技有限公司">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">详细地址</label>
                                                    <div class="col-sm-11">
                                                        <input type="text" class="form-control" placeholder="東燕郊開發區迎實路創業大厦A410">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-1 control-label">地理位置</label>
                                                    <div class="col-sm-11">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" data-mask="999-99-999-9999-9" placeholder="">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" data-mask="999-99-999-9999-9" placeholder="">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <button class="btn btn-default wgray-bg btn-white">选择坐标</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-1 col-md-offset-1">
                                                        <button type="submit" class="btn btn-success">提交</button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
			</div>
		</div>
		
	</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).on('click', '#close-preview', function(){ 
    $('.image-preview').popover('hide');

    $('.image-preview').hover(
        function () {
           $(this).closest('.image-preview').popover('show');
        }, 
         function () {
           $(this).closest('.image-preview').popover('hide');
        }
    );    

});

$(function() {

    $(".upload-input input:file").change(function (){ 
        var file = this.files[0];
        $(this).parent().parent().parent().find(".upload-filename").val(file.name);
    });  


    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class","close pull-right");
    // Set the popover default content
    $('.image-preview').popover({
        trigger:'manual',
        html:true,
        title: "<strong>Preview</strong>"+$(closebtn)[0].outerHTML,
        content: "There's no image",
        placement:'bottom'
    });
    // Clear event
    $('.image-preview-clear').click(function(){
        $(this).closest('.image-preview').attr("data-content","").popover('hide');
        $(this).parent().parent().find('.image-preview-filename').val("");
        $(this).closest('.image-preview-clear').hide();
        $(this).closest('.image-preview-input input:file').val("");
        $(this).closest(".image-preview-input-title").text("Upload"); 
    }); 
    // Create the preview image
    $(".image-preview-input input:file").change(function (){     
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200
        });
        var file = this.files[0];
        var reader = new FileReader();
        var target = this;
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(target).parent().parent().find(".image-preview-clear").show();
            $(target).parent().parent().parent().find(".image-preview-filename").val(file.name);
            img.attr('src', e.target.result);
            $(target).parent().parent().parent().attr("data-content",$(img)[0].outerHTML).popover("show");
        }        
        reader.readAsDataURL(file);
    });  
});
</script>
@endsection