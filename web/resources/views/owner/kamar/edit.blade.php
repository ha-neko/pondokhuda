@include('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.menubar')
@section('content')

<div class="block-header">
	<h2>{{ $data['pageTitle'] }}</h2>
</div>

<!-- Basic Examples -->
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
                <h2>
                    Data Kamar
                </h2>
            </div>
            <div class="body">
                <form onSubmit="return validate()" id="form" action="{{route('owner.kamar-update')}}" id="formCreatePenyewa" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{ $kamar['datakamar'][0]['kode'] }}" id="id" name="id">
                    @if($kamars['datakamar'] != null)
                    @for($i = 0; $i < count($kamars['datakamar']); $i++)
                    <input type="hidden" name="nokamar[]" value="{{$kamars['datakamar'][$i]['nokamar']}}">
                    @endfor
                    @endif
                    <input type="hidden" value="{{ $kamar['datakamar'][0]['nokamar'] }}" id="nmrkamar" name="nmrkamar">
                    <div class="form-group form-float">
                        <label class="form-label">Nomor Kamar</label>
                        <div class="form-line">
                            <input value="{{ $kamar['datakamar'][0]['nokamar'] }}" type="text" class="form-control" id="nomorkamar" name="nomorkamar" placeholder="Isi Nomor Kamar" onkeypress="space(event);" required>
                        </div>
                    </div>
                    <div id="gasewa">
                        <div class="form-group">
                            <input type="radio" name="statuskamar" id="sewa" class="radio-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} with-gap" value="Untuk Sewa" required>
                            <label for="sewa">Untuk Sewa</label>

                            <input type="radio" name="statuskamar" id="bukansewa" class="radio-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} with-gap" value="Bukan Untuk Sewa" required>
                            <label for="bukansewa">Bukan Untuk Sewa</label>
                        </div>
                        <div class="form-group form-float">
                            <label class="form-label">Harga Kamar (Rp)</label>
                            <div class="form-line">
                                <input value="{{ $kamar['datakamar'][0]['harga'] }}" type="text" class="form-control currency" id="hkamar" name="hkamar" placeholder="Isi Harga Kamar" oninput="hargaKamar(this.value);" onkeypress="numberOnly(event)" maxlength="19" required>
                            </div>
                        </div>
                    </div>
                    <input value="{{ $kamar['datakamar'][0]['harga'] }}" type="hidden" id="hargakamar" name="hargakamar">
                    <div id="disewa">
                        <div class="form-group form-float">
                            <label class="form-label">Harga Kamar (Rp)</label>
                            <div class="form-line">
                                <input value="{{ $kamar['datakamar'][0]['hargadisewakan'] }}" type="text" class="form-control currency" id="hargasewa" name="hargasewa" onkeypress="numberOnly(event);" oninput="hargaSewa(this.value);" maxlength="19" placeholder="Isi Harga Kamar">
                            </div>
                        </div>
                        <input value="{{ $kamar['datakamar'][0]['hargadisewakan'] }}" type="hidden" id="hargadisewakan" name="hargadisewakan">
                    </div>
                    <div class="text-right">
                        <a href="{{route('owner.kamar')}}">
                            <button class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" type="button">
                                <i class="material-icons">keyboard_arrow_left</i>
                                <span class="icon-name">Kembali</span>
                            </button>
                        </a>
                        <button type="submit" onclick="load();" class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
                            <i class="material-icons">check</i>
                            <span class="icon-name">Ubah Data Kamar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
        if(document.getElementById('hargadisewakan').value != "")
        {
            document.getElementById('gasewa').style.display = "none";
        }
        else
        {
            document.getElementById('disewa').style.display = "none";
        }

        $("input[name=statuskamar][value='{!! $kamar['datakamar'][0]['statuskamar'] !!}']").prop('checked',true);

        var hargasewa = $('#hargadisewakan').val();
        if (hargasewa != '')
        {
            $("#hargasewa").attr("readonly", false);
            $("#hargasewa").attr("required", true);

            $("#bukansewa").attr("disabled",true);
        }
        else
        {
            $("#hargasewa").attr("readonly", true);
        }

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
            return this.optional(element) || n || value == document.getElementById('nmrkamar').value;
        },
            'Nomor kamar sudah tersedia.'
        );

        for (var i = 0 ; i < document.getElementsByClassName("currency").length; i++)
        {
            document.getElementsByClassName("currency")[i].value =
            currency(document.getElementsByClassName("currency")[i].value);
        }

    });

    function currency(val) {
        var currency = val.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

        return currency;
    }

    function hargaSewa(num) {
        if(num != '')
        {
            priceVal = parseFloat(num.replace(/[^0-9-.]/g, ''));
        }
        else
        {
            priceVal = '';
        }
        document.getElementById("hargadisewakan").value = priceVal;
        ttlVal = document.getElementById("hargadisewakan").value;
        var c = ttlVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        document.getElementById("hargasewa").value = c;
        if (num == '') {
            document.getElementById("hargasewa").value = '';
            document.getElementById("hargadisewakan").value = '';
        }
    }

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
        document.getElementById("hkamar").value = c;
        if (num == '') {
            document.getElementById("hkamar").value = '';
            document.getElementById("hargakamar").value = '';
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
</script>

@endsection

@include('layouts.footer')