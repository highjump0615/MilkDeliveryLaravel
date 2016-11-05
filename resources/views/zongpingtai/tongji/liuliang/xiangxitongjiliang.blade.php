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
					<strong>详细统计量</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content">
					<div class="feed-element">
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
					<div class="feed-element">					
						<div class="row">
							<div class="col-lg-3"></div>
							<div class="col-lg-6">
								<div class="ibox float-e-margins">
									<div class="ibox-title">
										<div ibox-tools></div>
									</div>
									<div class="ibox-content">
										<div>
											<canvas id="lineChart" height="140"></canvas>
										</div>
									</div>
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
									<th>ID</th>
									<th>时间</th>
									<th>访问量</th>
									<th>注册量</th>
									<th>下单量</th>
									<th>下单比例</th>
								</tr>
                            </thead>
                            <tbody>
								<tr>
									<td>1</td>
									<td>2016-6-25  </td>
									<td>320</td>
									<td>235</td>
									<td>165</td>
									<td>56%</td>
								</tr>
								<tr>
									<td>2</td>
									<td>2016-6-25  </td>
									<td>320</td>
									<td>235</td>
									<td>165</td>
									<td>56%</td>
								</tr><tr>
									<td>3</td>
									<td>2016-6-25  </td>
									<td>320</td>
									<td>235</td>
									<td>165</td>
									<td>56%</td>
								</tr><tr>
									<td>4</td>
									<td>2016-6-25  </td>
									<td>320</td>
									<td>235</td>
									<td>165</td>
									<td>56%</td>
								</tr><tr>
									<td>5</td>
									<td>2016-6-25  </td>
									<td>320</td>
									<td>235</td>
									<td>165</td>
									<td>56%</td>
								</tr><tr>
									<td>6</td>
									<td>2016-6-25  </td>
									<td>320</td>
									<td>235</td>
									<td>165</td>
									<td>56%</td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>

			</div>
		</div>
		
	</div>
@endsection
@section('script')
<script src="<?=asset('js/plugins/added/chartjs-demo.js')?>"></script>
@endsection