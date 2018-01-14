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
				<li class="active"><strong>配送员配送统计</strong></li>
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
								<th data-sort-ignore="true" rowspan="2">序号</th>
								<th data-sort-ignore="true" rowspan="2" style="min-width:107px;">姓名</th>
								@foreach($dates as $dt)
									<th data-sort-ignore="true" colspan="{{count($products)}}">{{$dt}}</th>
								@endforeach
							</tr>
							<tr>
								@foreach($dates as $dt)
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->simple_name}}</th>
									@endforeach
								@endforeach
							</tr>
						</thead>
						<tbody>
                        <?php $i = 0; ?>
						@foreach($milkmans as $mm)
                            <?php $i++;?>
							<tr>
								<!-- 序号 -->
								<td>{{$i}}</td>
								<!-- 配送员 -->
								<td>{{$mm->station->name}}--{{$mm->name}}</td>
								@foreach($dates as $dt)
									@foreach($products as $p)
										<td>{{showEmptyValue(getEmptyArrayValue($counts, $mm->id, $dt, $p->id))}}</td>
									@endforeach
								@endforeach
							</tr>
						@endforeach
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
        printContent('table1', gnUserTypeFactory, '配送员配送统计');
    });

    $('button[data-action = "export_csv"]').click(function () {
    data_export('table1', gnUserTypeStation, '配送员配送统计', 0, 1);
	});
</script>
@endsection