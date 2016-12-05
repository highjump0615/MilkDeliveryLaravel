<div class="he50"></div>
<div class="menu">
    <ul class="menul">
        <li>
            <a href="{{url('/weixin/qianye')}}"><i class="mni1"></i>奶吧</a>
        </li>
        <li>
            <a href="{{url('/weixin/shangpinliebiao')}}"><i class="mni2"></i>快速订奶</a>
        </li>
        <li>
            @if(isset($cartn))
                <a href="{{url('/weixin/gouwuche')}}"><i class="mni3"></i>购物车@if($cartn>0)<span class="cartn">{{$cartn}}</span>@endif</a>
            @else
                <a href="{{url('/weixin/gouwuche')}}"><i class="mni3"></i>购物车<span class="cartn">0</span></a>
            @endif
        </li>
        <li>
            <a href="{{url('/weixin/gerenzhongxin')}}"><i class="mni4"></i>个人中心</a>
        </li>
    </ul>
</div>