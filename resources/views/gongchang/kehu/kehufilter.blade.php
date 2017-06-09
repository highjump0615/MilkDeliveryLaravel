<form method="get">
    <div class="ibox-content">
        <div class="col-md-3">
            <label>收货人:</label>
            <input type="text"
                   name="name"
                   @if (!empty($name)) value="{{$name}}" @endif>
        </div>
        <div class="col-md-3">
            <label>手机号:</label>
            <input type="text"
                   name="phone"
                   @if (!empty($phone)) value="{{$phone}}" @endif>
        </div>
        <div class="col-md-3">
            <label>区域:</label>
            <input type="text"
                   name="area"
                   @if (!empty($area)) value="{{$area}}" @endif>
        </div>
        <div class="col-md-3">
            <input type="submit" class="btn btn-success btn-m-d" value="筛选" />
            &nbsp;
            <button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
            &nbsp;
            <button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
        </div>
    </div>
</form>