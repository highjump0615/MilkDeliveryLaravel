@extends('zongpingtai.layout.master')

@section('content')
    @include('zongpingtai.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('zongpingtai.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li class="active">
                    <a href="{{ url('zongpingtai/xitong')}}">统计分析</a>
                </li>
                <li class="active">
                    <strong>数据库</strong>
                </li>
            </ol>
        </div>
        <div class="row wrapper">
            <div class="ibox float-e-margins">
                <div class="ibox-content">

                    <table id="customerTable" class="table footable table-bordered no-paging footable-loaded" data-page-size="15">
                        <thead style="background-color:#33cccc;">
                        <tr>
                            <th class="footable-sortable">数据库表名<span class="footable-sort-indicator"></span></th>
                            <th class="footable-sortable">数据库表注释<span class="footable-sort-indicator"></span></th>
                            <th class="footable-sortable">数据数量<span class="footable-sort-indicator"></span></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($factory as $fa)
                            <tr>
                                <td class="user">{{$fa->Name}}</td>
                                <td class="phone">{{$fa->Comment}}</td>
                                <td class="naizhan_count"><?php echo $fa->Auto_increment -1; ?></td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection