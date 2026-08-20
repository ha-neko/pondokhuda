@section('menu')
@if(Auth::guard('super-owner')->check())
<!-- Menu -->
<div class="menu">
    <ul class="list">
        <li class="header">Menu</li>
        <li id="dashboard" class="">
            <a href="{{route('super-owner.dashboard')}}">
                <i class="material-icons">home</i>
                <span>Beranda</span>
            </a>
        </li>
        <li id="owner" class="">
            <a href="{{route('super-owner.owner')}}">
                <i class="material-icons">accessibility</i>
                <span>Pemilik</span>
            </a>
        </li>
    </ul>
</div>
@endif

@if(Auth::guard('owner')->check())
<!-- Menu -->
<div class="menu">
    <ul class="list">
        <li class="header">Menu Utama</li>
        <li id="dashboard" class="">
            <a href="{{route('owner.dashboard')}}">
                <i class="material-icons">home</i>
                <span>Beranda</span>
            </a>
        </li>
        <li id="kost" class="">
            <a href="{{route('owner.kost')}}">
                <i class="material-icons">location_city</i>
                <span>Kost</span>
            </a>
        </li>
        <li id="admin" class="">
            <a href="{{route('owner.admin')}}">
                <i class="material-icons">person</i>
                <span>Admin</span>
            </a>
        </li>
        <!-- <li id="penyewa" class="">
            <a href="javascript:void(0);" class="menu-toggle">
                <i class="material-icons">group</i>
                <span>Penyewa</span>
            </a>
            <ul class="ml-menu">
                <li>
                    <a href="{{ route('owner.penyewa') }}">
                        <span>Daftar Penyewa</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('owner.penyewa-laporan')}}">
                        <span>Data Laporan Penyewa</span>
                    </a>
                </li>
            </ul>
        </li> -->
        <li id="penyewa" class="">
            <a href="{{ route('owner.penyewa') }}">
                <i class="material-icons">group</i>
                <span>Penyewa</span>
            </a>
        </li>
        <li id="informasisewa" class="">
            <a href="{{route('owner.informasisewa')}}">
                <i class="material-icons">access_time</i>
                <span>Informasi Sewa</span>
            </a>
        </li>
        <li id="kamar" class="">
            <a href="{{ route('owner.kamar') }}">
                <i class="material-icons">hotel</i>
                <span>Kamar</span>
            </a>
        </li>
        <li id="keluhan" class="">
            <a href="{{ route('owner.keluhan') }}">
                <i class="material-icons">chat</i>
                <span>Keluhan</span>
            </a>
        </li>
        <li id="pengumuman" class="">
            <a href="{{route('owner.pengumuman')}}">
                <i class="material-icons">announcement</i>
                <span>Pengumuman</span>
            </a>
        </li>
        <li id="pemasukan" class="">
            <a href="javascript:void(0);" class="menu-toggle" id="toggle-pemasukan">
                <i class="material-icons">attach_money</i>
                <span>Pemasukan</span>
            </a>
            <ul class="ml-menu" id="submenu-pembayaran">
                <li id="pendapatan-lain" class="">
                    <a href="{{route('owner.pembayaran')}}">
                        Bayar kos
                    </a>
                </li>
                <li id="pendapatan-lain" class="">
                    <a href="{{ route('owner.lainlain') }}">
                        Pendapatan lain-lain
                    </a>
                </li>
            </ul>
        </li>
        <li id="pengeluaran" class="">
            <a href="{{route('owner.pengeluaran')}}">
                <i class="material-icons">payment</i>
                <span>Pengeluaran</span>
            </a>
        </li>
        <li id="laporan-keuangan">
            <a href="javascript:void(0);" class="menu-toggle">
                <i class="material-icons">assignment</i>
                <span>Laporan Keuangan</span>
            </a>
            <ul class="ml-menu">
                <li>
                    <a href="{{route('owner.keu_transaksi')}}">
                        <span>Daftar Transaksi</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('owner.keu_labarugi')}}">
                        <span>Laporan Laba Rugi</span>
                    </a>
                </li>
            </ul>
        </li>
        <li id="log" class="">
            <a href="{{route('owner.log')}}">
                <i class="material-icons">chrome_reader_mode</i>
                <span>Log Admin</span>
            </a>
        </li>
    </ul>
</div>
<!-- #Menu -->
@endif

@if(Auth::guard('admin')->check())
<!-- Menu -->
<div class="menu">
    <ul class="list">
        <li class="header">Menu Utama</li>
        <li id="dashboard" class="">
            <a href="{{route('admin.dashboard')}}">
                <i class="material-icons">home</i>
                <span>Beranda</span>
            </a>
        </li>
        <li id="keluhan" class="">
            <a href="{{route('admin.keluhan')}}">
                <i class="material-icons">chat</i>
                <span>Keluhan</span>
            </a>
        </li>
        <li id="penyewa" class="">
            <a href="{{route('admin.penyewa')}}">
                <i class="material-icons">account_box</i>
                <span>Penyewa</span>
            </a>
        </li>
        <li id="informasisewa" class="">
            <a href="{{route('admin.informasisewa')}}">
                <i class="material-icons">access_time</i>
                <span>Informasi Sewa</span>
            </a>
        </li>
        <li id="pengumuman" class="">
            <a href="{{route('admin.pengumuman')}}">
                <i class="material-icons">announcement</i>
                <span>Pengumuman</span>
            </a>
        </li>
        <li id="pemasukan" class="">
            <a href="javascript:void(0);" class="menu-toggle" id="toggle-pemasukan">
                <i class="material-icons">attach_money</i>
                <span>Pemasukan</span>
            </a>
            <ul class="ml-menu" id="submenu-pembayaran">
                <li id="bayar-kos" class="">
                    <a href="{{route('admin.pembayaran')}}">
                        Bayar kos
                    </a>
                </li>
                <li id="bayar-kos" class="">
                    <a href="{{route('admin.viewpendpatanll')}}">
                        Pendapatan Lain-lain
                    </a>
                </li>
            </ul>
        </li>
        <li id="pengeluaran" class="">
            <a href="{{route('admin.pengeluaran')}}" id="toggle-pengeluaran">
                <i class="material-icons">payment</i>
                <span>Pengeluaran</span>
            </a>
        </li>
    </ul>
</div>
<!-- #Menu -->
@endif

@if(Auth::guard('penyewa')->check())
<!-- Menu -->
<div class="menu">
    <ul class="list">
        <li class="header">Menu Utama</li>
        <li id="biodata">
            <a href="{{ route('penyewa.dashboard') }}">
                <i class="material-icons">account_box</i>
                <span>Biodata</span>
            </a>
        </li>
        <li id="pengumuman">
            <a href="{{ route('penyewa.pengumuman') }}">
                <i class="material-icons">announcement</i>
                <span>Pengumuman</span>
            </a>
        </li>
        <li id="whatsapp-admin">
            <a href="{{ route('penyewa.keluhan') }}">
                <i class="material-icons">message</i>
                <span>Keluhan</span>
            </a>
        </li>
        <li id="keluhan">
            <a href="javascript:void(0);" data-toggle="modal" data-target="#whatsappAdmin">
                <i class="material-icons">phone_iphone</i>
                <span>Hubungi Admin / Pemilik</span>
            </a>
        </li>
        <li id="pembayaran">
            <a href="javascript:void(0);" class="menu-toggle" id="toggle-pembayaran">
                <i class="material-icons">payment</i>
                <span>Pembayaran</span>
            </a>
            <ul class="ml-menu">
                <li>
                    <a href="{{ route('penyewa.pembayaran') }}">
                        Resume
                    </a>
                </li>
                <li>
                    <a href="{{ route('penyewa.riwayatpembayaran') }}">
                        Riwayat Pembayaran
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>
<!-- #Menu -->
@endif

<script type="text/javascript">
    // document.getElementById({{ $data['activeId'] }}).onclick = function() {
    //     alert({{ $data['activeId'] }});
    // }
    document.addEventListener('DOMContentLoaded', function () {
        var id = <?php echo json_encode(isset($data['activeId']) ? $data['activeId'] : ''); ?>;
        var activeItem = id ? document.getElementById(id) : null;
        if (!activeItem) return;
        activeItem.classList.add('active');
        var submenu = activeItem.closest ? activeItem.closest('.ml-menu') : null;
        if (submenu) {
            submenu.style.display = 'block';
            var parent = submenu.parentElement;
            if (parent) parent.classList.add('active');
        }
    });
</script>
@endsection
