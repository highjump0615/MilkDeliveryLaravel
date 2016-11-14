<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="align-center profile-element">
                    <img alt="image" class="img-responsive" src="<?=asset('img/milk/cow.png') ?>"
                         style="width:75%; margin: auto;"/>
                    <label class="logo-text">总平台管理</label>
                </div>
            </li>

            <?php
            $role_id = Auth::guard('zongpingtai')->user()->user_role_id;
            $role_pages = \App\Model\UserModel\UserPageAccess::where('user_role_id', $role_id)->get();
            ?>

            @foreach($pages as $p)
                @foreach($role_pages as $rp)
                    @if($rp->page_id == $p->id)

                        <li @if($p->page_ident==$parent) class="active" @endif>
                            <a href={{URL::to($p->page_url)}}>
                                <i class="{{$p->icon_name}}"></i>
                                <span class="nav-label">{{$p->name}}</span>
                                <span class="fa arrow"></span>
                            </a>
                            @foreach($p->sub_pages as $s)
                                @foreach($role_pages as $rp)
                                    @if($rp->page_id == $s->id)
                                        <ul class="nav nav-second-level collapse @if($p->page_ident == $parent) in @endif">
                                            <li @if($s->page_ident==$child) class="active" @endif>
                                                <a href={{URL::to($s->page_url)}}>{{$s->name}}</a>
                                            </li>
                                        </ul>
                                    @endif
                                @endforeach
                            @endforeach
                        </li>

                    @endif
                @endforeach
            @endforeach
        </ul>
    </div>
</nav>