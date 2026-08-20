@if(Auth::guard('owner')->check())
    @if(count(Auth::user()->ownerkost) > 0)
        <body class="theme-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" id="body">
    @else
        <body id="body">
    @endif
@elseif(Auth::guard('admin')->check())
<body class="theme-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" id="body">
@elseif(Auth::guard('penyewa')->check())
<body class="theme-{{Auth::user()->kost->name_color}}" id="body">
@else
<body class="theme-light-green" id="body">
@endif
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Mohon tunggu...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars" role="button" aria-label="Buka menu navigasi" aria-controls="leftsidebar" aria-expanded="false"></a>
                <!-- <a class="navbar-brand" href="{{ route('loginForm') }}">{{ config('app.name') }}</a> -->
                <!-- <img src="https://pondok-huda.com/Assets/images/logo/logo.png" width="80px"> -->
                @php
                    if (Auth::guard('super-owner')->check()) {
                        $homeUrl = route('super-owner.dashboard');
                        $manualUrl = asset('user-manual/super-owner.pdf');
                    } elseif (Auth::guard('owner')->check()) {
                        $homeUrl = route('owner.dashboard');
                        $manualUrl = asset('user-manual/owner.pdf');
                    } elseif (Auth::guard('admin')->check()) {
                        $homeUrl = route('admin.dashboard');
                        $manualUrl = asset('user-manual/admin.pdf');
                    } else {
                        $homeUrl = route('penyewa.dashboard');
                        $manualUrl = null;
                    }
                @endphp
                <a class="navbar-brand" id="text-title" href="{{ $homeUrl }}">
                    <span class="brand-mark"><img src="{{ asset('Assets/images/logo/default-logo-white.png') }}" alt=""></span>
                    <span class="brand-copy">
                    @if(Auth::guard('super-owner')->check())
                    {{'Si Juragan Kost'}}
                    @else
                    {{ isset($appName) ? $appName : 'PondokHuda' }}
                    @endif
                    </span>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <!-- CHANGE THEME AND SETTINGS -->
                    <li class="navbar-user-label pull-right">
                        <span>{{ Auth::user()->nama }}</span>
                    </li>
                    <!-- END OF CHANGE THEME AND SETTINGS -->
                    @if(Auth::guard('owner')->check())
                    <!-- KELOLA KOST OWNER -->
                    <li class="dropdown pull-right">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="material-icons">location_city</i>
                            <span class="label-count"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">PILIH KOST UNTUK DIKELOLA</li>
                            <li class="body">
                                <ul class="menu">
                                    @if(count(Auth::user()->ownerkost) != 0)
                                    @foreach(Auth::user()->ownerkost as $key => $kost)
                                    <li>
                                        <a href="{{ route('owner.kost-ganti', $key) }}">
                                            <div class="icon-circle bg-light-green">
                                                <i class="material-icons">home</i>
                                            </div>
                                            <div class="menu-info">
                                                <h4>{{$kost['kost']['nama_kost']}}</h4>
                                                <p>
                                                    @if($key == $indexKost)
                                                    <span class="col-light-green">
                                                        <i class="material-icons">done</i>
                                                        sedang dikelola
                                                    </span>
                                                    @else
                                                    <span class="col-red">
                                                        <i class="material-icons">swap_horiz</i>
                                                        pilih kost
                                                    </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                    @endforeach
                                    @endif
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="{{route('owner.kost')}}">Lihat Daftar Kost</a>
                            </li>
                        </ul>
                    </li>
                    <!-- END of KELOLA KOST OWNER -->
                    @elseif(Auth::guard('admin')->check())
                    <!-- KELOLA KOST OWNER -->
                    <li class="dropdown pull-right">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="material-icons">location_city</i>
                            <span class="label-count"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">PILIH KOST UNTUK DIKELOLA</li>
                            <li class="body">
                                <ul class="menu">
                                    @if(count(Auth::user()->adminkost) != 0)
                                    @foreach(Auth::user()->adminkost as $key => $kost)
                                    <li>
                                        <a href="{{ route('admin.kost-ganti', $key) }}">
                                            <div class="icon-circle bg-light-green">
                                                <i class="material-icons">home</i>
                                            </div>
                                            <div class="menu-info">
                                                <h4>{{$kost['kost']['nama_kost']}}</h4>
                                                <p>
                                                    @if($key == $indexKost)
                                                    <span class="col-light-green">
                                                        <i class="material-icons">done</i>
                                                        sedang dikelola
                                                    </span>
                                                    @else
                                                    <span class="col-red">
                                                        <i class="material-icons">swap_horiz</i>
                                                        pilih kost
                                                    </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                    @endforeach
                                    @endif
                                </ul>
                            </li>
                            <li class="footer">
                                <!-- <a href="javascript:void(0);">Lihat Daftar Kost</a> -->
                            </li>
                        </ul>
                    </li>
                    <!-- END of KELOLA KOST OWNER -->
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="image">
                    @php
                        $rawPhoto = Auth::user()->urlfoto;
                        $photoUrl = strpos($rawPhoto, 'http') === 0 ? $rawPhoto : asset(ltrim($rawPhoto, '/'));
                    @endphp
                    <div id="img-foto" class="user-avatar" style="background-image: url('{{ $photoUrl }}');"></div>
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{Auth::user()->nama}}</div>
                    @if(Auth::guard('owner')->check())
                        @if(count(Auth::user()->ownerkost) > 0)
                            <div class="email">{{Auth::user()->ownerkost[$indexKost]['kost']['emailkost']}}</div>
                        @else
                        @endif
                    @elseif(Auth::guard('admin')->check())
                        <div class="email">{{Auth::user()->adminkost[$indexKost]['kost']['emailkost']}}</div>
                    @elseif(Auth::guard('penyewa')->check())
                        <div class="email">{{Auth::user()->email}}</div>
                    @else
                    @endif
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <!-- <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                            <li role="separator" class="divider"></li> -->
                            @if($manualUrl)
                            <li>
                                <a href="{{ $manualUrl }}" target="_blank" rel="noopener noreferrer">
                                <i class="material-icons">description</i>Buku Panduan (PDF)</a>
                            </li>
                            @endif
                            @if(Auth::guard('penyewa')->check())
                            <li>
                                <a href="https://pondokhuda.com/deploy/app/asharilabs/ysm0118123/ph2k18v0.apk" download><i class="material-icons">get_app</i>Download APK</a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i class="material-icons">input</i>Logout</a>
                            </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            @yield('menu')
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    &copy; {{ date('Y') }}
                    <a href="javascript:void(0);">
                    {{'Pondok Huda'}}
                    </a>.
                </div>
                <div class="version">
                    <b>Version: </b> 2.0.0
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        <!-- <aside id="rightsidebar" class="right-sidebar">
            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                <li role="presentation" class="active"><a href="#settings" data-toggle="tab">PENGATURAN</a></li>
                <li role="presentation"><a href="#skins" data-toggle="tab">TEMA</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active in active" id="settings">
                    <div class="demo-settings">
                        <p>GENERAL SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Report Panel Usage</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Email Redirect</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>SYSTEM SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Notifications</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Auto Updates</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>ACCOUNT SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Offline</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Location Permission</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="skins">
                    <ul class="demo-choose-skin">
                        <li data-theme="red" class="active">
                            <div class="red"></div>
                            <span>Red</span>
                        </li>
                        <li data-theme="pink">
                            <div class="pink"></div>
                            <span>Pink</span>
                        </li>
                        <li data-theme="purple">
                            <div class="purple"></div>
                            <span>Purple</span>
                        </li>
                        <li data-theme="deep-purple">
                            <div class="deep-purple"></div>
                            <span>Deep Purple</span>
                        </li>
                        <li data-theme="indigo">
                            <div class="indigo"></div>
                            <span>Indigo</span>
                        </li>
                        <li data-theme="blue">
                            <div class="blue"></div>
                            <span>Blue</span>
                        </li>
                        <li data-theme="light-blue">
                            <div class="light-blue"></div>
                            <span>Light Blue</span>
                        </li>
                        <li data-theme="cyan">
                            <div class="cyan"></div>
                            <span>Cyan</span>
                        </li>
                        <li data-theme="teal">
                            <div class="teal"></div>
                            <span>Teal</span>
                        </li>
                        <li data-theme="green">
                            <div class="green"></div>
                            <span>Green</span>
                        </li>
                        <li data-theme="light-green">
                            <div class="light-green"></div>
                            <span>Light Green</span>
                        </li>
                        <li data-theme="lime">
                            <div class="lime"></div>
                            <span>Lime</span>
                        </li>
                        <li data-theme="yellow">
                            <div class="yellow"></div>
                            <span>Yellow</span>
                        </li>
                        <li data-theme="amber">
                            <div class="amber"></div>
                            <span>Amber</span>
                        </li>
                        <li data-theme="orange">
                            <div class="orange"></div>
                            <span>Orange</span>
                        </li>
                        <li data-theme="deep-orange">
                            <div class="deep-orange"></div>
                            <span>Deep Orange</span>
                        </li>
                        <li data-theme="brown">
                            <div class="brown"></div>
                            <span>Brown</span>
                        </li>
                        <li data-theme="grey">
                            <div class="grey"></div>
                            <span>Grey</span>
                        </li>
                        <li data-theme="blue-grey">
                            <div class="blue-grey"></div>
                            <span>Blue Grey</span>
                        </li>
                        <li data-theme="black">
                            <div class="black"></div>
                            <span>Black</span>
                        </li>
                    </ul>
                </div>
            </div>
        </aside> -->
        <!-- #END# Right Sidebar -->
    </section>

    <section class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </section>
