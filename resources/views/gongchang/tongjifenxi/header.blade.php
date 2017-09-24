<form method="get" id="form_filter" class="form-horizontal no-padding">
    <div class="ibox-content">
        <div class="feed-element">
            {{-- 奶站名称 --}}
            <div class="col-md-3">
                <label>奶站名称:</label>
                <input type="text" name="station_name" value="{{$station_name}}">
            </div>

            {{-- 区域 --}}
            <div class="pull-left">
                <label>区域:</label>
                &nbsp;
                <select data-placeholder="" class="chosen-select" name="area_name" tabindex="2" style="height: 30px;">
                    <option value="">全部</option>
                    @foreach($address as $addr)
                        @if($addr->name == $area_name)
                            <option selected value="{{$addr->name}}">{{$addr->name}}</option>
                        @else
                            <option value="{{$addr->name}}">{{$addr->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- 日期 --}}
            <div class="pull-left" id="data_range_select" style="margin-left: 20px;">
                <label class="control-label pull-left" style="padding-top:5px; margin-right: 10px;">日期:</label>
                @if (empty($dateRange))
                    <div class="input-group date pull-left" style="width: 150px;">
                        <input type="text" class="form-control" name="end_date" value="{{$end_date}}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                @else
                    <div class="input-daterange input-group pull-left" id="datepicker" style="width: 250px;">
                        <input type="text" class="input-sm form-control" name="start_date" value="{{$start_date}}"/>
                        <span class="input-group-addon">至</span>
                        <input type="text" class="input-sm form-control" name="end_date" value="{{$end_date}}"/>
                    </div>
                @endif
                &nbsp;&nbsp;&nbsp;&nbsp;
            </div>

            {{-- 操作 --}}
            <div class="pull-right"  style="padding-top:5px;">
                <button type="submit" id="search" class="btn btn-sm btn-success">筛选</button>
                &nbsp;
                {{--<button type="button" class="btn-outline btn btn-sm btn-success" data-action="export_csv">导出</button>--}}
                {{--&nbsp;--}}
                <button type="button" class="btn btn-outline btn-sm btn-success" data-action="print">打印</button>
                &nbsp;
            </div>
        </div>
    </div>
</form>