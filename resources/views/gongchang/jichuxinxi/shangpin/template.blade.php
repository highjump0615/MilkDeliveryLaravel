<div id="product_area_set">
    <div class="form-group" style="margin-bottom: 15px !important;">
        <label class="col-md-3 control-label">模板名称 </label>
        <div class="col-md-2">
            <input type="text" id="template_name" class="form-control col-md-2">
        </div>

    </div>
    <div class="form-group area_select" style="margin-bottom: 15px !important;">
        <label class="col-md-3 control-label" style="padding-top: 7px;">销售区域:</label>
        <div class="col-md-2">
            <select class="form-control province_list">
                @if (isset($provinces))
                    @for ($i = 0; $i < count($provinces); $i++)
                        @if($i == 0)
                            <option value="{{$provinces[$i]->name}}"
                                    selected>{{$provinces[$i]->name}}</option>
                        @else
                            <option value="{{$provinces[$i]->name}}"
                            >{{$provinces[$i]->name}}</option>
                        @endif
                    @endfor
                @endif
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-control city_list">
            </select>
        </div>
        <div class="col-md-4">
            <select data-placeholder=""
                    class="form-control chosen-select district_list"
                    multiple style="width: 100%;">
            </select>
        </div>

    </div>
    <div class="form-group" style="margin-bottom: 15px !important;">
        <label class="col-md-3 control-label">零售价 </label>
        <div class="col-md-2">
            <input type="number" min="0.1" step="any" id="retail_price"
                   class="form-control col-md-2">
        </div>
        <label class="control-label">元</label>
    </div>

    <div class="form-group" style="margin-bottom: 15px !important;">
        <label class="col-md-3 control-label">月单 </label>
        <div class="col-md-2">
            <input type="number" min="0.1" step="any" id="month_price"
                   class="form-control col-md-2">
        </div>
        <label class="control-label">元</label>
    </div>

    <div class="form-group" style="margin-bottom: 15px !important;">
        <label class="col-md-3 control-label">季单 </label>
        <div class="col-md-2">
            <input type="number" min="0.1" step="any" id="season_price"
                   class="form-control col-md-2">
        </div>
        <label class="control-label">元</label>
    </div>

    <div class="form-group" style="margin-bottom: 15px !important;">
        <label class="col-md-3 control-label">半年单</label>
        <div class="col-md-2">
            <input type="number" min="0.1" step="any" id="half_year_price"
                   class="form-control col-md-2">
        </div>
        <label class="control-label">元</label>
    </div>
    <div class="form-group" style="margin-bottom: 15px !important;">
        <label class="col-md-3 control-label">结算价 </label>
        <div class="col-md-2">
            <input type="number" min="0.1" step="any" id="settle_price"
                   class="form-control col-md-2">
        </div>
        <label class="control-label col-md-1" style="padding:0; text-align: left;">元</label>

        <div class="form-group col-md-6" style="text-align:left;">
            <button type="button"
                    id="add_price_bt"
                    class="btn btn-outline btn-success"
                    style="margin-right: 10px;"
                    onclick="add_price_template(0, 0)">
                <i class="fa fa-plus"></i>添加价格模板
            </button>
            <button type="button" id="update_price_bt"
                    class="btn btn-outline btn-success"
                    style="margin-right: 10px;">
                <i class="fa fa-save"></i>修改价格模板
            </button>
            <button type="button" class="btn btn-outline btn-success"
                    onclick="init_price_template()">取消
            </button>
        </div>

    </div>

</div>