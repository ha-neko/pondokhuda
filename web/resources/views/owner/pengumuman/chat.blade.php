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

<!-- Chat Main -->
<div class="messaging">
  <!-- List Inbox -->
  <div class="inbox_msg">

    <!-- Chat Inti -->
    <div class="mesgs">
        <!-- Heading Search -->
        <div class="headind_srch">
            <div class="recent_heading">
                <h4>Recent</h4>
            </div>
        </div>
        <!-- End Heading Search -->
        
        <!-- Kolom Chat -->
        <div class="msg_history">

          <!-- Chat Masuk -->
          <div class="incoming_msg">
            <div class="incoming_msg_img">
              <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
            </div>
            <div class="received_msg">
              <div class="received_withd_msg">
                <p>Test which is a new approach to have all solutions</p>
                <span class="time_date"> 11:01 AM | June 9</span>
              </div>
            </div>
          </div>
          <!-- End Chat Masuk -->

          <!-- Chat Keluar -->
          <div class="outgoing_msg">
            <div class="sent_msg">
              <p>Test which is a new approach to have all solutions</p>
              <span class="time_date"> 11:01 AM    |    June 9</span>
            </div>
          </div>
          <!-- End Chat Keluar -->

        </div>
        <!-- End Kolom Chat -->

        <!-- Chat Input -->
        <div class="type_msg">
          <div class="input_msg_write">
            <input type="text" class="write_msg" placeholder="Type a message" />
            <button class="msg_send_btn" type="button">
              <i class="material-icons">send</i>
            </button>
          </div>
        </div>
        <!-- End Chat Input -->
      </div>
      <!-- End Chat Inti -->

  </div>
  <!-- End List Inbox -->  
</div>
<!-- End Chat Main -->

@endsection

@section('js-content')

<script type="text/javascript">

    $(function () {
        $('#dataTable').DataTable({
            responsive: true
        });
    });

</script>

@endsection

@include('layouts.footer')