@extends('weixin.layout.master')
@section('title','填写收货地址')

@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/plugin/LArea.min.css')?>">
    <style type="text/css">
        label {
            color: #aaaaaa;
        }
    </style>
@endsection

@section('content')
    <header>
        @if(isset($order) && isset($type))
            <a class="headl fanh" href="{{url('weixin/dizhiliebiao').'?order='.$order.'&&type='.$type}}"></a>
        @else
            <a class="headl fanh" href="{{url('weixin/dizhiliebiao')}}"></a>
        @endif
        <h1>填写收货地址</h1>
    </header>

    <div class="addrbox">
        <form id="form1" method="post" action="{{url('/weixin/dizhitianxie')}}">
            @if(isset($order) && isset($type))
                <input type="hidden" name="order" value="{{$order}}"/>
                <input type="hidden" name="type" value="{{$type}}"/>
            @endif
            <input type="hidden" name="wxuser_id" value="{{$wxuser_id}}"/>
            @if(isset($address_id))
            <input type="hidden" name="address_id" value="{{$address_id}}"/>
            @endif
            <ul class="adrul">
                <li>
                    <label>收货人：</label><input required name="name" type="text" value="{{$name}}">
                </li>
                <li>
                    <label>电话：</label><input required name="phone" id="phone" type="text" value="{{$phone}}">
                </li>
                <li>
                    <label>所在地区：</label>
                    <input id="area" name="area" type="text" readonly placeholder="请选择省市区" />
                    <input id="val_area" type="hidden" />
                </li>
                <li>
                    <label>街道小区：</label>
                    <input id="street" name="street" type="text" readonly placeholder="请选择街道小区" />
                    <input id="val_street" type="hidden" />
                </li>
                <li>
                    <label>详细地址：</label>
                    <input required name="sub_address" type="text" placeholder="楼号、门牌号等" value="{{$sub_address}}">
                </li>

                <li>
                    <label>设为默认地址：</label>
                <span class='tg-list-item'>
                <input name="primary" class='tgl tgl-ios' id='cb2' type='checkbox'
                       @if($primary)
                       checked
                        @endif
                >
                <label class='tgl-btn' for='cb2'></label>
                </span>
                </li>
            </ul>

            <input type="submit" class="ordqr" value="确认">
        </form>
    </div>
@endsection
@section('script')

    {{-- 地址选择器 --}}
    <script src="<?=asset('weixin/js/plugin/LArea.js')?>"></script>

    <script type="text/javascript">

        {{-- 获取地址列表数据并转换成 --}}
        <?php
        $jsonProvince = json_encode($provinces);
        $jsonCities = json_encode($cities);
        $jsonDistricts = json_encode($districts);
        $jsonStreets = json_encode($streets);
        $jsonVillages = json_encode($villages);

        echo "var provs_data = " . $jsonProvince . ";";
        echo "var cities_data = " . $jsonCities . ";";
        echo "var districts_data = " . $jsonDistricts . ";";
        echo "var streets_data = " . $jsonStreets . ";";
        echo "var villages_data = " . $jsonVillages . ";";
        ?>

        $(document).ready(function () {
            $('#form1').on('submit', function(e) { //use on if jQuery 1.7+

                var oPhone = $('#phone');

                if(phonenumber($(oPhone).val())) {
                } else {
                    alert("手机号码格式不正确");
                    return false;
                }

                // 查看地址是否选好
                if (!$('#street').val()) {
                    alert("请选择街道和小区信息");
                    return false;
                }
            });

            function phonenumber(inputtxt)
            {
                var phoneno = /^1[345678][0-9]{9}$/;
                if(inputtxt.match(phoneno))
                {
                    return true;
                }

                return false;
            }
        });

        /**
         * 通过text获取value
         * @param valuesSrc
         * @param text
         * @returns {string}
         */
        function getValueFromText(valuesSrc, text) {
            var strValue = null;
            for (i = 0; i < valuesSrc.length; i++) {
                if (valuesSrc[i].text === text) {
                    strValue = valuesSrc[i].value;
                    break;
                }
            }

            return strValue;
        }

        // 初始化地区选择器
        var areaArea = new LArea();
        areaArea.init({
            'trigger': '#area',
            'valueTo': '#val_area',
            'keys': {
                id: 'value',
                name: 'text'
            },
            'type': 2,
            'data': [provs_data, cities_data, districts_data]
        });

        // 初始化地区选择器
        var distId;
        var areaStreet = new LArea();

        areaArea.success(function() {
            if (distId === areaArea.value[2]) {
                return;
            }

            distId = areaArea.value[2];
            $('#street').val('');

            initStreetList(distId);
        });

        /**
         * 初始化街道和小区列表
         * @param districtId
         */
        function initStreetList(districtId) {
            areaStreet.init({
                'trigger': '#street',
                'valueTo': '#val_street',
                'keys': {
                    id: 'value',
                    name: 'text'
                },
                'type': 2,
                'data': [streets_data[districtId], villages_data]
            });
        }

        // 初始化地址
        function initAddress(address) {
            var addresses = strAddress.split(' ');

            // 设置text
            $('#area').val(addresses[0] + ' ' + addresses[1] + ' ' + addresses[2]);
            $('#street').val(addresses[3] + ' ' + addresses[4]);

            var strProvince = getValueFromText(provs_data, addresses[0]);
            if (!strProvince) {
                return;
            }

            areaArea.value[0] = strProvince;
            var strCity = getValueFromText(cities_data[strProvince], addresses[1]);
            if (!strCity) {
                return;
            }

            areaArea.value[1] = strCity;

            var strDistrict = getValueFromText(districts_data[strCity], addresses[2]);
            if (!strDistrict) {
                return;
            }

            areaArea.value[2] = strDistrict;
            initStreetList(strDistrict);

            var strStreet = getValueFromText(streets_data[strDistrict], addresses[3]);
            if (!strStreet) {
                return;
            }

            areaStreet.value[0] = strStreet;
            var strVillage = getValueFromText(villages_data[strStreet], addresses[4]);
            if (!strVillage) {
                return;
            }

            areaStreet.value[1] = strVillage;
        }

        // 初始选择地址
        @if (!empty($address_id))

        var strAddress = '{{$address}}';
        initAddress(strAddress);

        @endif
        /////

    </script>

@endsection