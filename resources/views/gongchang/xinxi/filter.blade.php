<!-- 筛选 -->
<form method="get">

    <!-- 阅读状态 -->
    <div class="col-lg-12">
        <div class="form-group col-lg-12">
            <label class="col-md-2" style="padding-top: 5px;">阅读状态:</label>
            &nbsp;
            <select name="read" data-placeholder="Choose..." class="chosen-select" style="width:305px; height:35px;" tabindex="2">
                <option value="2">
                    全部
                </option>
                <option value="0"
                        @if (isset($read) && $read == 0) selected @endif>
                    未读
                </option>
                <option value="1"
                        @if (isset($read) && $read == 1) selected @endif>
                    已读
                </option>
            </select>
        </div>
    </div>

    <!-- 消息分类 -->
    <div class="col-lg-12">
        <div class="form-group col-lg-12">
            <label class="col-md-2" style="padding-top: 5px;">消息分类:</label>
            &nbsp;
            <select name="category" data-placeholder="Choose..." class="chosen-select" style="width:305px; height:35px;" tabindex="2">
                <option value="">全部</option>
                @foreach($categories as $ca)
                    <option value="{{$ca['id']}}"
                            @if (!empty($category) && $category == $ca['id']) selected @endif>
                        {{$ca['name']}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- 发送日期 -->
    <div class="col-lg-12">
        <div class="col-lg-10" id="data_range_select">
            <div class="col-md-2">
                <label class="control-label" style="padding-top: 5px;">发送日期:</label>
            </div>
            <div class="col-md-4 input-daterange input-group" id="datepicker" style="padding-left: 45px;">
                <input type="text"
                       class="input-sm form-control"
                       name="start"
                       @if (!empty($start)) value="{{$start}}" @endif />
                <span class="input-group-addon">至</span>
                <input type="text"
                       class="input-sm form-control"
                       name="end"
                       @if (!empty($end)) value="{{$end}}" @endif />
            </div>
        </div>
        <div class="col-lg-2">
            <button class="btn btn-success btn-outline" type="submit" >
                筛选
            </button>
        </div>
    </div>
</form>