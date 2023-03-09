<!-- Modal-->

<div id="track_pr_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <!-- <h5 id="exampleModalLabel" class="modal-title">Track Purchase Request</h5> -->
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <center><h2><div>Track Purchase Request</div></h2></center>
        <div class="container px-1 px-md-4 py-5 mx-auto">
          <div class="card">
            <div class="row d-flex justify-content-between px-3 top">
              <div class="d-flex">
                <h5>Purchase Request #: <span class="text-primary font-weight-bold">#6152</span></h5>
              </div>
              <div class="d-flex flex-column text-sm-right">
                <p class="mb-0">Expected Arrival <span>01/06/20</span></p>
                <p>Grasshoppers <span class="font-weight-bold"><a href="https://www.grasshoppers.lk/customers/action/track/V534HB">V534HB</a></span></p>
              </div>
            </div> <!-- Add class 'active' to progress -->
            <div class="row d-flex justify-content-center">
              <div class="col-12">
                <ul id="progressbar" class="text-center">
                  <li class="active step0"></li>
                  <li class="active step0"></li>
                  <li class="step0"></li>
                  <li class="step0"></li>
                  <li class="step0"></li>
                </ul>
              </div>
            </div>
            <div class="row justify-content-between top">
              <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                  <p class="font-weight-bold">Make<br>Request</p>
                </div>
              </div>
              <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                  <p class="font-weight-bold">Canvass<br>Request</p>
                </div>
              </div>
              <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                  <p class="font-weight-bold">Verified<br>Request</p>
                </div>
              </div>
              <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                  <p class="font-weight-bold">Check for<br>Fund</p>
                </div>
              </div>
              <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('dist/img/icon/checklist.png')}}">
                <div class="d-flex flex-column">
                  <p class="font-weight-bold">Approved<br>Request</p>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $().ready(function() {
      $('.track_btn').on('click', function() {
        $('#track_pr_modal').appendTo('body').modal('show');
        
        $tr = $(this).closest('tr');
        
        var data = $tr.children("td").map(function(){
          return $(this).text();
        }).get();

        console.log(data);

        $('#pr_id').val(data[0]);
        var id=$('#pr_id').val();
        console.log(id);
        
      });
    });
  </script>
  <style>
    body {
      color: #000;
      overflow-x: hidden;
      height: 100%;
      background-repeat: no-repeat
    }
    .modal-content{
    border-color: green;
    border-radius: 1rem;
}
    .card {
      z-index: 0;
      background-color: #ECEFF1;
      padding-bottom: 20px;
      margin-top: 10px;
      margin-bottom: 90px;
      border-radius: 10px
    }

    .top {
      padding-top: 40px;
      padding-left: 13% !important;
      padding-right: 13% !important
    }

    #progressbar {
      margin-bottom: 30px;
      overflow: hidden;
      color: #455A64;
      padding-left: 0px;
      margin-top: 30px
    }

    #progressbar li {
      list-style-type: none;
      font-size: 13px;
      width: 20%;
      float: left;
      position: relative;
      font-weight: 400
    }

    #progressbar .step0:before {
      font-family: FontAwesome;
      content: "\f10c";
      color: #fff
    }

    #progressbar li:before {
      width: 40px;
      height: 40px;
      line-height: 45px;
      display: block;
      font-size: 20px;
      background: #C5CAE9;
      border-radius: 50%;
      margin: auto;
      padding: 0px
    }

    #progressbar li:after {
      content: '';
      width: 100%;
      height: 12px;
      background: #C5CAE9;
      position: absolute;
      left: 0;
      top: 16px;
      z-index: -1
    }

    #progressbar li:last-child:after {
      border-top-right-radius: 10px;
      border-bottom-right-radius: 10px;
      position: absolute;
      left: -50%
    }

    #progressbar li:nth-child(2):after,
    #progressbar li:nth-child(3):after,
    #progressbar li:nth-child(4):after {
      left: -50%
    }

    #progressbar li:first-child:after {
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
      position: absolute;
      left: 50%
    }

    #progressbar li:last-child:after {
      border-top-right-radius: 10px;
      border-bottom-right-radius: 10px
    }

    #progressbar li:first-child:after {
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px
    }

    #progressbar li.active:before,
    #progressbar li.active:after {
      background: #13ad43
    }

    #progressbar li.active:before {
      font-family: FontAwesome;
      content: "\f00c"
    }

    .icon {
      width: 60px;
      height: 60px;
      margin-right: 15px
    }

    .icon-content {
      padding-bottom: 20px
    }

    @media screen and (max-width: 992px) {
      .icon-content {
        width: 50%
      }
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">