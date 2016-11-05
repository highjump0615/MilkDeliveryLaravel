@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('zongpingtai/tongji')}}">统计分析</a>
				</li>
				<li class="active">
					<strong>流量统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-lg-1 col-md-2">
							<img alt="img" class="img-responsive" src="<?=asset('img/logo/logo1.png')?>" style="height:60px;">
						</div>
						<label class="col-lg-1 col-md-2" style="padding-top: 20px;">圣牧奶业</label>
					</div>
					<label class="gray-bg col-lg-12" style="padding:5px;">公众号活跃度</label>
					<div class="col-lg-12">
						<div class="col-md-4">
							<div class="col-lg-6">
								<select data-placeholder="Choose..." class="chosen-select" style="width: 100%; height:25px;" tabindex="2">
									<option value="一周">一周</option>
									<option value="半个月">半个月</option>
									<option value="一个月">一个月</option>
								</select>
							</div>
							<div class="col-lg-6">
								<select data-placeholder="Choose..." class="chosen-select" style="width: 100%; height:25px;" tabindex="2">
									<option value="本周">本周</option>
									<option value="上周">上周</option>
								</select>
							</div>	
						</div>
					</div>
					<div class="col-lg-12">					
						<div class="row">
							<div class="col-lg-3"></div>
							<div class="col-lg-6">
								<div class="flot-chart">
									<div class="flot-chart-pie-content" id="flot-pie-chart1"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="table footable table-bordered">
                            <thead>
								<tr>
									<th>排名</th>
									<th>公众号</th>
									<th>访问总量</th>
									<th>比例</th>
									<th>所属平台账号</th>
									<th>操作</th>
								</tr>
                            </thead>
                            <tbody>
								<tr>
									<td>1</td>
									<td>庞大双龙汽车</td>
									<td>475次</td>
									<td>16.6%</td>
									<td>sdfv45</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr>
								<tr>
									<td>2</td>
									<td>唐山上海大</td>
									<td>320次</td>
									<td>16.6%</td>
									<td>sdf23</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>3</td>
									<td>众荣川众德4S店</td>
									<td>320次</td>
									<td>9.6%</td>
									<td>sdf3</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>4</td>
									<td>唐润科技有限公司</td>
									<td>320次</td>
									<td>21.6%</td>
									<td>dfg345</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>5</td>
									<td>燕郊潮趴汇</td>
									<td>320次</td>
									<td>6.6%</td>
									<td>sdf5</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>6</td>
									<td>虎跃堂散打泰拳馆</td>
									<td>320次</td>
									<td>34.6%</td>
									<td>dfg5</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-lg-1 col-md-2">
							<img alt="img" class="img-responsive" src="<?=asset('img/logo/logo2.png')?>" style="height:60px;">
						</div>
						<label class="col-lg-1 col-md-2" style="padding-top: 20px;">圣牧奶业</label>
					</div>
					<label class="gray-bg col-lg-12" style="padding:5px;">公众号活跃度</label>
					<div class="col-lg-12">
						<div class="col-md-4">
							<div class="col-lg-6">
								<select data-placeholder="Choose..." class="chosen-select" style="width: 100%; height:25px;" tabindex="2">
									<option value="一周">一周</option>
									<option value="半个月">半个月</option>
									<option value="一个月">一个月</option>
								</select>
							</div>
							<div class="col-lg-6">
								<select data-placeholder="Choose..." class="chosen-select" style="width: 100%; height:25px;" tabindex="2">
									<option value="本周">本周</option>
									<option value="上周">上周</option>
								</select>
							</div>	
						</div>
					</div>
					<div class="col-lg-12">					
						<div class="row">
							<div class="col-lg-3"></div>
							<div class="col-lg-6">
								<div class="flot-chart">
									<div class="flot-chart-pie-content" id="flot-pie-chart2"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="table footable table-bordered">
                            <thead>
								<tr>
									<th>排名</th>
									<th>公众号</th>
									<th>访问总量</th>
									<th>比例</th>
									<th>所属平台账号</th>
									<th>操作</th>
								</tr>
                            </thead>
                            <tbody>
								<tr>
									<td>1</td>
									<td>庞大双龙汽车</td>
									<td>475次</td>
									<td>16.6%</td>
									<td>sdfv45</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr>
								<tr>
									<td>2</td>
									<td>唐山上海大</td>
									<td>320次</td>
									<td>16.6%</td>
									<td>sdf23</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>3</td>
									<td>众荣川众德4S店</td>
									<td>320次</td>
									<td>9.6%</td>
									<td>sdf3</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>4</td>
									<td>唐润科技有限公司</td>
									<td>320次</td>
									<td>21.6%</td>
									<td>dfg345</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>5</td>
									<td>燕郊潮趴汇</td>
									<td>320次</td>
									<td>6.6%</td>
									<td>sdf5</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr><tr>
									<td>6</td>
									<td>虎跃堂散打泰拳馆</td>
									<td>320次</td>
									<td>34.6%</td>
									<td>dfg5</td>
									<td>
										<a href="{{ url('zongpingtai/tongji/xiangxitongjiliang')}}">查看详细统计量</a>
									</td>
								</tr>
                            </tbody>
                        </table>
                    </div>
				</div>	

			</div>
		</div>
	 </div>
@endsection

@section ('script')

<script src="<?=asset('js/demo/flot-demo.js')?>"></script>
<script type="javascript">
$(function() {

    var data = [{
        label: "Sales 1",
        data: 21,
        color: "#d3d3d3",
    }, {
        label: "Sales 2",
        data: 3,
        color: "#bababa",
    }, {
        label: "Sales 3",
        data: 15,
        color: "#79d2c0",
    }, {
        label: "Sales 4",
        data: 52,
        color: "#1ab394",
    }];

    var plotObj = $.plot($("#flot-pie-chart"), data, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true
        },
        tooltip: true,
        tooltipOpts: {
            content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
            shifts: {
                x: 20,
                y: 0
            },
            defaultTheme: false
        }
    });

});
</script>
@endsection