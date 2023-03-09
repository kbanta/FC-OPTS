<br>
<br>
<br>
<div class="row">
    <div class="col-xs-2 col-sm-6 col-md-3 col-lg-3 col-xl-3">
        <div class="" style="text-align: center;">
            @if(!empty($isverifyBy[0]['isVerified']))
            <h5><i class="" style="color: green;"></i> {{$isverifyBy[0]['fname']}} {{$isverifyBy[0]['mname']}}. {{$isverifyBy[0]['lname']}}</h5>
            <p>{{$isverifyBy[0]['position']}}</p>
            @else
            <h5>ASSD Manager</h5>
            <i>
                <p>waiting for verification</p>
            </i>
            @endif
        </div>
    </div>
    <div class="col-xs-2 col-sm-6 col-md-3 col-lg-3 col-xl-3">
        <div class="" style="text-align: center;">
            @if(!empty($ischeckfundBy[0]['isCheckfund']))
            <h5><i class="" style="color: green;"></i> {{$ischeckfundBy[0]['fname']}} {{$ischeckfundBy[0]['mname']}}. {{$ischeckfundBy[0]['lname']}}</h5>
            <p>{{$ischeckfundBy[0]['position']}}</p>
            @else
            <h5>FSD Manager</h5>
            <i>
                <p>waiting for verification</p>
            </i>
            @endif
        </div>
    </div>
    <div class="col-xs-2 col-sm-6 col-md-3 col-lg-3 col-xl-3">
        <div class="" style="text-align: center;">
            @if(!empty($isapprovedBy[0]['isApproved']))
            <h5><i class="" style="color: green;"></i> {{$isapprovedBy[0]['fname']}} {{$isapprovedBy[0]['mname']}}. {{$isapprovedBy[0]['lname']}}</h5>
            <p>{{$isapprovedBy[0]['position']}}</p>
            @else
            <h5>Corporate Treasurer</h5>
            <i>
                <p>waiting for verification</p>
            </i>
            @endif
        </div>
    </div>
    <div class="col-xs-2 col-sm-6 col-md-3 col-lg-3 col-xl-3">
        <div class="" style="text-align: center;">
            @if(!empty($isapproved2By[0]['isApproved2']))
            <h5><i class="" style="color: green;"></i> {{$isapproved2By[0]['fname']}} {{$isapproved2By[0]['mname']}}. {{$isapproved2By[0]['lname']}}</h5>
            <p>{{$isapproved2By[0]['position']}}</p>
            @else
            <h5>Chief Executive Officer</h5>
            <i>
                <p>waiting for verification</p>
            </i>
            @endif
        </div>
    </div>
</div>