@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="">瓶框管理</a>
				</li>
				<li class="active">
					<strong>瓶框统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<div><hr></div>
				<div class="feed-element">
					<div class="col-md-3 col-md-offset-1">
						<label class="col-lg-4" style="padding-top:5px;">日期</label>
						<div class="col-lg-8">
						<select id="month" data-placeholder="" class="chosen-select" style="height:34px; width:100%;" tabindex="2">
							<option value="">全都</option>
							<option value="1">一月</option>
							<option value="2">二月</option>
							<option value="3">三月</option>
							<option value="4">四月</option>
							<option value="5">五月</option>
							<option value="6">六月</option>
							<option value="7">七月</option>
							<option value="8">八月</option>
							<option value="9">九月</option>
							<option value="10">十月</option>
							<option value="11">十一月</option>
							<option value="12">十二月</option>
						</select>
						</div>
					</div>
					<div class="col-md-3 col-md-offset-5"  style="padding-top:5px;">
						<button type="button" class="btn btn-success btn-m-d" data-action="show_selected">筛选</button>
						{{--&nbsp;--}}
						{{--<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>--}}
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="view_table" class="footable table table-bordered" data-page-size="{{$rows * 3}}">
							<thead>
								<tr>
									<th data-sort-ignore="true">日期</th>
									<th data-sort-ignore="true">瓶类</th>
									<th data-sort-ignore="true">期初库存</th>
									<th data-sort-ignore="true">收货数量</th>
									<th data-sort-ignore="true">客户退回瓶数</th>
									<th data-sort-ignore="true">返厂数量</th>
									<th data-sort-ignore="true">站内破损</th>
									<th data-sort-ignore="true">期末库存</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i = 0; ?>
							@foreach($dsrefundinfo as $month=>$ti)
								@if($month != '')
								<?php $i++; $j = 0; ?>
								@foreach($ti as $type=>$t)
									<?php $j++; ?>
									<tr value="{{$month}}">
										@if($j == 1)
										<td rowspan="{{count($ti)}}">{{$month}}月</td>
										@endif
										<td>{{$type}}</td>
										<td class="init_val">{{$t['init']}}</td>
										<td class="received">{{$t['received']}}</td>
										<td class="milkman">{{$t['milkman']}}</td>
										<td class="factory">{{$t['from_factory']}}</td>
										<td class="damage">{{$t['damaged']}}</td>
										<td class="total"></td>
									</tr>
								@endforeach
								@endif
							@endforeach
                            </tbody>
                            <tfoot>
                            	<tr>
                            		<td colspan="100%"><ul class="pagination pull-right"></ul></td>
                            	</tr>
                            </tfoot>
                        </table>

						<table id="filter_table" class="footable table table-bordered" data-page-size="15" style="display: none">
							<thead>
							<tr>
								<th data-sort-ignore="true">日期</th>
								<th data-sort-ignore="true">瓶类</th>
								<th data-sort-ignore="true">期初库存</th>
								<th data-sort-ignore="true">收货数量</th>
								<th data-sort-ignore="true">客户退回瓶数</th>
								<th data-sort-ignore="true">返厂数量</th>
								<th data-sort-ignore="true">站内破损</th>
								<th data-sort-ignore="true">期末库存</th>
							</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
							<tr>
								<td colspan="100%"><ul class="pagination pull-right"></ul></td>
							</tr>
							</tfoot>
						</table>
                    </div>
                </div>
			</div>
		</div>
	</div>
@endsection

@section('script')
	<script type="text/javascript" src="<?=asset('js/pages/naizhan/pingkunagtongji.js')?>"></script>
@endsection