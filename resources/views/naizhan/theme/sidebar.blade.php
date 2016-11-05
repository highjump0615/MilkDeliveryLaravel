<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="align-center profile-element">
                    <img alt="image" class="img-responsive" src="<?=asset('img/milk/cow.png') ?>" style="width:75%; margin: auto;"/>
                    <label class="logo-text">奶站管理</label>
                </div>
            </li>

            @foreach($pages as $p)
                <li @if($p->page_ident==$parent) class="active" @endif><a href={{URL::to($p->page_url)}}><i  class="{{$p->icon_name}}"></i><span class="nav-label">{{$p->name}}</span><span class="fa arrow"></span></a>
                    @foreach($p->sub_pages as $s)
                        <ul class="nav nav-second-level">
                            <li @if($s->page_ident==$child) class="active" @endif><a href={{URL::to($s->page_url)}}>{{$s->name}}</a></li>
                        </ul>
            @endforeach
            @endforeach
        </ul>

    </div>
</nav>