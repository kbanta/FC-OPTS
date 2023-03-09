<div class="row">
    <div class="col-xs-2 col-sm-4 col-md-4 col-lg-4 col-xl-4">
        <div class="" style="text-align: center;">
            <p>Transmitted By:</p>
            <br>
            @if(!empty($transmittedby[0]['fname']))
            <h5> {{$transmittedby[0]['fname']}} {{$transmittedby[0]['mname']}}. {{$transmittedby[0]['lname']}}</i></h5>
            <p>{{$transmittedby[0]['position']}}</p>
            @else
            <h5> Processor</i></h5>
            <p>waiting for approval</p>
            @endif
        </div>
    </div>
    <div class="col-xs-2 col-sm-4 col-md-4 col-lg-4 col-xl-4">
        <div class="" style="text-align: center;">
            <p>Noted By:</p>
            <br>
            @if(!empty($isApproved[0]['isApproved']))
            <h5> {{$isApproved[0]['fname']}} {{$isApproved[0]['mname']}}. {{$isApproved[0]['lname']}}</i></h5>
            <p>{{$isApproved[0]['position']}}</p>
            @else
            <h5> Approver</i></h5>
            <p>waiting for approval</p>
            @endif
        </div>
    </div>
    <div class="col-xs-2 col-sm-4 col-md-4 col-lg-4 col-xl-4">
        <div class="" style="text-align: center;">
            <p>Received By:</p>
            <br>
            @if(!empty($isApproved[0]['isReqReceived']))
            <h5> {{$delivery[0]['fname']}} {{$delivery[0]['mname']}} {{$delivery[0]['lname']}}</i></h5>
            <p>{{$delivery[0]['position']}}</p>
            @else
            <h5> {{$delivery[0]['fname']}} {{$delivery[0]['mname']}} {{$delivery[0]['lname']}}</i></h5>
            <p>{{$delivery[0]['position']}}</p>
            @endif
        </div>
    </div>
</div>