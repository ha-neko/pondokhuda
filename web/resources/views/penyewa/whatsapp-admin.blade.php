<!-- MODAL WHATSAPP -->
<div class="modal fade" id="whatsappAdmin" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->kost->name_color}}">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                Pilih Admin
                            </div>
                            <div class="body">
                                <div class="form-group form-float">
                                    <label class="form-label">Pilih Owner / Admin</label>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="noadmin" id="noadmin" onchange="getNoWA();">
                                            <option value="">-- Pilih Owner / Admin --</option>
                                            @foreach(Auth::user()->kost->ownerkost as $owner)
                                            <option value="{{$owner->owner->nomor_telepon}}">{{$owner->owner->nama}}</option>
                                            @endforeach
                                            @foreach(Auth::user()->kost->adminkost as $admin)
                                            <option value="{{$admin->admin->nomor_telepon}}">{{$admin->admin->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <a id="nowa" href="javascript:void(0);">
                                        <button class="btn waves-effect bg-{{Auth::user()->kost->name_color}}" type="button">
                                            <i class="material-icons">phone_iphone</i>
                                            <span class="icon-name">Hubungi Admin</span>
                                        </button>
                                    </a>
                                    <button type="button" class="btn waves-effect bg-{{Auth::user()->kost->name_color}}" data-dismiss="modal">
                                        <i class="material-icons">close</i>
                                        <span class="icon-name">Batal</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function getNoWA() {
      var nowa = document.getElementById("noadmin").value;
      if(nowa != "")
      {
        document.getElementById("nowa").setAttribute("href", "https://wa.me/62" + nowa.substring(1) + "/?text=Assalamualaikum Admin");
      }
      else
      {
        document.getElementById("nowa").setAttribute("href", "javascript:void(0);"); 
      }
    }
</script>
<!-- END OF MODAL WHATSAPP -->