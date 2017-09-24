@extends('gongchang.layout.master')
@section('css')
@endsection
@section('content')
	@include('gongchang.theme.sidebar')
	<div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">统计分析</a>
				</li>
				<li class="active"><strong>客户订单修改统计</strong></li>
			</ol>
		</div>
		<div class="row">

			@include('gongchang.tongjifenxi.header', [
				'dateRange' => true,
			])

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="table1" class="footable table table-bordered" data-page-size="10">
						<thead>
							<tr>
								<th colspan="11" style="font-size:20px; text-align: center;">客户订单修改信息汇总表</th>
							</tr>
							<tr>
								<th data-sort-ignore="true" rowspan="2">序号</th>
								<th data-sort-ignore="true" rowspan="2">奶站名称</th>
								<th data-sort-ignore="true" rowspan="2">联系电话修改</th>
								<th data-sort-ignore="true" rowspan="2">配送地址変更</th>
								<th data-sort-ignore="true" rowspan="2">暂停配送客户</th>
								<th data-sort-ignore="true" colspan="5">客户订单产品变更</th>
								<th data-sort-ignore="true" rowspan="2">配送规则修改</th>
							</tr>
							<tr>
								<th data-sort-ignore="true">增加单次配送量</th>
								<th data-sort-ignore="true">减少单次配送量</th>
								<th data-sort-ignore="true">鲜奶调换酸奶</th>
								<th data-sort-ignore="true">酸奶调换鲜奶</th>
								<th data-sort-ignore="true">酸奶变更口味</th>
							</tr>
						</thead>
						<tbody>
							<?php $i=0; ?>
							@foreach($stations as $s)
								<?php $i++; ?>
							<tr>
								<td>{{$i}}</td>
								<td><?php
									if(isset($s['name']))
										echo $s['name'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['phone']))
										echo $s['phone'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['address']))
										echo $s['address'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['stopped']))
										echo $s['stopped'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['increased']))
										echo $s['increased'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['decreased']))
										echo $s['decreased'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['milk_yogurt']))
										echo $s['milk_yogurt'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['yogurt_milk']))
										echo $s['yogurt_milk'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['yogurt_kouwei']))
										echo $s['yogurt_kouwei'];
									else
										echo "0";
									?>
								</td>
								<td><?php
									if(isset($s['rule']))
										echo $s['rule'];
									else
										echo "0";
									?>
								</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td colspan="11">
									<ul class="pagination pull-right"></ul>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
	<script src="<?=asset('js/pages/gongchang/kehudingdanxiugui.js') ?>"></script>
@endsection