@include('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.menubar')
@section('content')

<div class="block-header">
    <h2>{{ $data['pageTitle'] }}</h2>
</div>

@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class') }} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ Session::get('message') }}
</p>
@endif

<!-- TAMBAH KAMAR -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Tambah Kamar
                </h2>
            </div>
            <div class="body">
                <form onSubmit="return validate();" id="form" action="{{route('owner.kamar-create')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($kamars['datakamar'] != null)
                    @foreach($kamars['datakamar'] as $kamar)
                    <input type="hidden" name="nokamar[]" value="{{$kamar['nokamar']}}">
                    @endforeach
                    @endif
                    <div class="form-group form-float">
                        <label class="form-label">Nomor Kamar</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="nomorkamar" name="nomorkamar" onkeypress="space(event);" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Harga Kamar (Rp)</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="harga" name="harga" onkeypress="numberOnly(event);" oninput="hargaKamar(this.value);" required maxlength="19">
                        </div>
                    </div>
                    <input type="hidden" id="hargakamar" name="hargakamar" required>
                    <div class="text-right">
                        <a href="{{route('owner.kamar')}}">
                            <button class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" type="button">
                                <i class="material-icons">keyboard_arrow_left</i>
                                <span class="icon-name">Kembali</span>
                            </button>
                        </a>
                        <button type="submit" onclick="load();" class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
                            <i class="material-icons">check</i>
                            <span class="icon-name">Tambah Kamar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END OF TAMBAH KAMAR -->
<div class="loading" id="loading">Loading&#8230;</div>
@endsection

@section('js-content')
<script src="https://pondok-huda.com/js/validate.js" type="text/javascript"></script>
<script type="text/javascript">
    function load() {
        if ($("#form").valid()) {
            document.getElementById("loading").style.zIndex = "999";
            document.getElementById("loading").style.opacity = "0.8";
            showInputMessage();
        }
    }

    $(document).ready(function(){
        $('#form').validate({
            rules: {
                'nomorkamar': {
                    nomorkamar: true
                }
            },
            highlight: function (input) {
                $(input).parents('.form-line').addClass('error');
            },
            unhighlight: function (input) {
                $(input).parents('.form-line').removeClass('error');
            },
            errorPlacement: function (error, element) {
                $(element).parents('.form-group').append(error);
            }
        });

        $.validator.addMethod('nomorkamar', function (value, element) {
            var nokamars = [];
            for(i = 0; i < document.getElementsByName('nokamar[]').length; i++)
            {
                nokamars.push(document.getElementsByName('nokamar[]')[i].value)
            }
            var n = !nokamars.includes(value);
            return this.optional(element) || n;
        },
            'Nomor kamar sudah tersedia.'
        );
    });

    function hargaKamar(num) {
        if(num != '')
        {
            priceVal = parseFloat(num.replace(/[^0-9-.]/g, ''));
        }
        else
        {
            priceVal = '';
        }
        document.getElementById("hargakamar").value = priceVal;
        ttlVal = document.getElementById("hargakamar").value;
        var c = ttlVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        document.getElementById("harga").value = c;
        if (num == '') {
            document.getElementById("harga").value = '';
            document.getElementById("hargakamar").value = '';
        }
    }
    
    function space(evt) {
      var theEvent = evt || window.event;

      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
      var regex = /\s/g;

      if(regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
      }
    }

    function numberOnly(evt) {
      var theEvent = evt || window.event;

      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
      var regex = /[0-9]|\./;

      if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
      }
    }

</script>

@endsection

@include('layouts.footer')