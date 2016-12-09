@extends('gongchang.layout.master')

@section('css')
    <link href="<?=asset('css/plugins/tagsinput/bootstrap-tagsinput.css') ?>" rel="stylesheet">
    <style>
        .bootstrap-tagsinput {
            width: 100%;
        }
        th.fixedaddr {
            width:70px;
        }
    </style>
@endsection

@section('content')
    @include('errors.error')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">基础信息管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/jichuxinxi/dizhiku')}}><strong>地址库管理</strong></a>
                </li>
            </ol>
        </div>
        <div class="row">
            <div class="ibox-content">
                <div class="col-md-12">
                    <button data-action="insert_modal" class="btn btn-success btn-outline" type="button">添加</button>
                    <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-content">

                    <table class="footable table table-bordered" data-page-size="10" id="address_tb">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">序号</th>
                            <th data-sort-ignore="true" class="fixedaddr">省</th>
                            <th data-sort-ignore="true" class="fixedaddr">市</th>
                            <th data-sort-ignore="true" class="fixedaddr">区</th>
                            <th data-sort-ignore="true">街道</th>
                            <th data-sort-ignore="true">小区</th>
                            <th data-sort-ignore="true">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (isset($addresses))
                            <?php
                            $i = 0;
                            $rowcount = 0;
                            ?>
                            @foreach($addresses as $key => $streets)
                                <?php
                                // 考虑合并因分页分开的情况
                                $streetcount = count($streets);
                                $row_span = min($streetcount, 10 - $rowcount % 10);
                                $j = 0;
                                ?>
                                @foreach($streets as $street)
                                    <tr>
                                        @if ($rowcount % 10 == 0 && $j > 0)
                                            <?php
                                            $streetcount -= $row_span;
                                            $row_span = min($streetcount, 10);
                                            $j = 0;
                                            ?>
                                        @endif

                                        @if ($j == 0)
                                            <td rowspan="{{$row_span}}">{{$i+1}}</td>
                                            <td rowspan="{{$row_span}}">{{$street->province->name}}</td>
                                            <td rowspan="{{$row_span}}">{{$street->city->name}}</td>
                                            <td rowspan="{{$row_span}}">{{$street->district->name}}</td>
                                        @endif
                                        <td class="street_td"
                                            data-content="{{ $street->name }}"
                                            data-province="{{$street->province->name}}"
                                            data-city="{{$street->city->name}}"
                                            data-district="{{$street->district->name}}">
                                            {{$street->name}}
                                        </td>
                                        <td class="xiaoqu_td"
                                            data-content="{{ $street->sub_addresses_str }}">{{ $street->sub_addresses_str }}
                                        </td>
                                        <td>
                                            @if ($street->is_active == \App\Model\BasicModel\Address::ADDRESS_ACTIVE)
                                                <button type="button" data-action="set_flag" data-enable="1"
                                                        class="btn btn-sm btn-success">停用
                                                </button>
                                            @else
                                                <button type="button" data-action="set_flag" data-enable="0"
                                                        class="btn btn-sm">
                                                    使用
                                                </button>
                                            @endif
                                            <button type="button" data-action="update_modal"
                                                    class="btn btn-success btn-sm">编辑
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" data-action="delete">删除
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                    $j++;
                                    $rowcount++;?>
                                @endforeach
                                <?php $i++;?>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="100%">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div id="insert_modal" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form role="form" method="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="col-md-3">省:</label>
                                    <div class="col-md-9">
                                        <select required name="province" class="form-control" id="insert_province">
                                            @if (isset($provinces))
                                                <option value="none_province"></option>
                                                @for ($i = 0; $i < count($provinces); $i++)
                                                    @if($provinces[$i]->name == '北京')
                                                        <option value="{{$provinces[$i]->name}}"
                                                                selected>{{$provinces[$i]->name}}</option>
                                                    @else
                                                        <option value="{{$provinces[$i]->name}}">{{$provinces[$i]->name}}</option>
                                                    @endif
                                                @endfor
                                            @else
                                                <option value="empty_province">No Province Ready</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="feed-element col-md-12">
                                    <label class="col-md-3">市:</label>
                                    <div class="col-md-9">
                                        <select required name="city" class="form-control" id="insert_city">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="col-md-3">区:</label>
                                    <div class="col-md-9">
                                        <select required name="district" class="form-control" id="insert_district">
                                        </select>
                                    </div>
                                </div>
                                <div class="feed-element col-md-12">
                                    <label class="col-md-3">街道:</label>
                                    <div class="col-md-9">
                                        <input type="text" required name="street" style="width:100%;">
                                    </div>
                                </div>
                                <div class="feed-element col-md-12">
                                    <label class="col-md-3">小区:</label>
                                    <div class="col-md-9">
                                        <input type="text" data-role="tagsinput" rows="3" id="xiaoqu-insert" required name="xiaoqu" cols="40" value=""/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-action="insert_addr">确定</button>
                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div>

                <div id="modal-spinner-frame-insert" class="modal-spinner-frame">
                    <div class="modal-spinner">
                        <div class="sk-spinner sk-spinner-circle">
                            <div class="sk-circle1 sk-circle"></div>
                            <div class="sk-circle2 sk-circle"></div>
                            <div class="sk-circle3 sk-circle"></div>
                            <div class="sk-circle4 sk-circle"></div>
                            <div class="sk-circle5 sk-circle"></div>
                            <div class="sk-circle6 sk-circle"></div>
                            <div class="sk-circle7 sk-circle"></div>
                            <div class="sk-circle8 sk-circle"></div>
                            <div class="sk-circle9 sk-circle"></div>
                            <div class="sk-circle10 sk-circle"></div>
                            <div class="sk-circle11 sk-circle"></div>
                            <div class="sk-circle12 sk-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="update_modal" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            &nbsp;
                            <div class="col-md-12">
                                <label class="col-md-3">省:</label>
                                <div class="col-md-9">
                                    <input required type="text" class="form-control" readonly name="province">
                                </div>
                            </div>
                            <div class="feed-element col-md-12">
                                <label class="col-md-3">市:</label>
                                <div class="col-md-9">
                                    <input required readonly class="form-control" name="city" id="city"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-3">区:</label>
                                <div class="col-md-9">
                                    <input required readonly class="form-control" id="district" name="district"/>
                                </div>
                            </div>
                            <div class="feed-element col-md-12">
                                <label class="col-md-3">街道:</label>
                                <div class="col-md-9">
                                    <input type="text" required class="form-control" style="width:100%;" name="street">
                                    <input type="text" class="hidden" name="origin_street">
                                </div>
                            </div>
                            <div class="feed-element col-md-12">
                                <label class="col-md-3">小区:</label>
                                <div class="col-md-9">
                                    <input type="text" data-role="tagsinput"  required class="form-control" id="xiaoqu-update" name="xiaoqu" value=""/>
                                    <input type="text" class="hidden" name="origin_xiaoqu"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-action="update_addr">确定</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                    </div>
                </div>

                <div id="modal-spinner-frame-update" class="modal-spinner-frame">
                    <div class="modal-spinner">
                        <div class="sk-spinner sk-spinner-circle">
                            <div class="sk-circle1 sk-circle"></div>
                            <div class="sk-circle2 sk-circle"></div>
                            <div class="sk-circle3 sk-circle"></div>
                            <div class="sk-circle4 sk-circle"></div>
                            <div class="sk-circle5 sk-circle"></div>
                            <div class="sk-circle6 sk-circle"></div>
                            <div class="sk-circle7 sk-circle"></div>
                            <div class="sk-circle8 sk-circle"></div>
                            <div class="sk-circle9 sk-circle"></div>
                            <div class="sk-circle10 sk-circle"></div>
                            <div class="sk-circle11 sk-circle"></div>
                            <div class="sk-circle12 sk-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript" src="<?=asset('js/plugins/tagsinput/bootstrap-tagsinput.js') ?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/address_manage.js')?>"></script>

@endsection