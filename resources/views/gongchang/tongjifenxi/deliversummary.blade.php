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
				<li class="active"><strong>配送汇总表</strong></li>
			</ol>
		</div>

		<div class="row">
			@include('gongchang.tongjifenxi.header', [
				'dateRange' => true,
			])

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="table1" class="table table-bordered">
						<thead>
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true">日期</th>
								@foreach($products as $p)
									<th data-sort-ignore="true">{{$p->simple_name}}</th>
								@endforeach
								<th data-sort-ignore="true">应回收瓶</th>
								<th data-sort-ignore="true">已回收瓶</th>
								<th data-sort-ignore="true">差瓶</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$i = 0;
						$dtIndex = $start_date;
						?>
						@foreach($milkmans as $mm)
                            <?php $i++;?>
							<tr>
								<td>{{$i}}</td>
								<td>{{$dtIndex}}</td>
								@foreach($products as $p)
                                    <?php
                                    $strCount = "";
                                    if (!empty($counts[$dtIndex])) {
									foreach ($counts[$dtIndex] as $cc) {
										if ($cc->product_id == $p->id) {
											$strCount = $cc->count;
											break;
										}
									}
									}
                                    ?>
									<td>{{$strCount}}</td>
								@endforeach
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<?php $dtIndex = getNextDateString($dtIndex); ?>
						@endwhile
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
<script type="text/javascript">
    $('#data_range_select .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true
    });

    $('button[data-action = "print"]').click(function () {
        printContent('table1', gnUserTypeFactory, '配送汇总表');
    });
</script>
@endsection