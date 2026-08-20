@include('layouts.header')
@extends('layouts.sidebar')

@section('content')

<div class="block-header">
    <h2>{{ $data['pageTitle'] }}</h2>
</div>

@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class') }} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ Session::get('message') }}
</p>
@endif

<!-- LIST ADMIN -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Tambah Admin
                </h2>
            </div>
            <div class="body">
                <form id="form_validation" method="POST">
                  @csrf
                  <div class="form-group form-float">
                      <div class="form-line">
                          <input type="text" class="form-control" name="name" required>
                          <label class="form-label">Name</label>
                      </div>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <input type="text" class="form-control" name="surname" required>
                          <label class="form-label">Surname</label>
                      </div>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <input type="email" class="form-control" name="email" required>
                          <label class="form-label">Email</label>
                      </div>
                  </div>
                  <div class="form-group">
                      <input type="radio" name="gender" id="male" class="with-gap">
                      <label for="male">Male</label>

                      <input type="radio" name="gender" id="female" class="with-gap">
                      <label for="female" class="m-l-20">Female</label>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <textarea name="description" cols="30" rows="5" class="form-control no-resize" required></textarea>
                          <label class="form-label">Description</label>
                      </div>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <input type="password" class="form-control" name="password" required>
                          <label class="form-label">Password</label>
                      </div>
                  </div>
                  <div class="form-group">
                      <input type="checkbox" id="checkbox" name="test">
                      <label for="checkbox">I have read and accept the terms</label>
                  </div>
                  <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
              </form>
            </div>
        </div>
    </div>
</div>
<!-- END OF LIST ADMIN -->

@endsection

@section('js-content')

<script type="text/javascript">
  $(function () {
    $('#form_validation').validate({
        rules: {
            'test': {
                required: true
            },
            'gender': {
                required: true
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
  });

  function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
              $('#img-foto').attr('src', e.target.result);
          }

          reader.readAsDataURL(input.files[0]);
      }
  }

</script>

@endsection

@include('layouts.footer')