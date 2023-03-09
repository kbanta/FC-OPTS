<div class="row">
    <div class="col-xs-2 col-sm-6 col-md-4 col-lg-4 col-xl-4">
        <div class="" style="text-align: center;">
            @if(!empty($preparedBy[0]['preparedBy']))
            <h5><i class="" style="color: green;"></i> {{$preparedBy[0]['fname']}} {{$preparedBy[0]['mname']}}. {{$preparedBy[0]['lname']}}</h5>
            <p>{{$preparedBy[0]['position']}}</p>
            @else
            <h5>{{$pb[0]['fname']}} {{$pb[0]['mname']}}. {{$pb[0]['lname']}}</h5>
            <i>
                <p>waiting for approval</p>
            </i>
            @endif
        </div>
    </div>
    <div class="col-xs-2 col-sm-6 col-md-4 col-lg-4 col-xl-4">
        <div class="" style="text-align: center;">
            @if(!empty($verifiedBy[0]['verifiedBy']))
            <h5><i class="" style="color: green;"></i> {{$verifiedBy[0]['fname']}} {{$verifiedBy[0]['mname']}}. {{$verifiedBy[0]['lname']}}</h5>
            <p>{{$verifiedBy[0]['position']}}</p>
            @else
            <h5>Finance Manager</h5>
            <i>
                <p>waiting for approval</p>
            </i>
            @endif
        </div>
    </div>
    <div class="col-xs-2 col-sm-6 col-md-4 col-lg-4 col-xl-4">
        <div class="" style="text-align: center;">
            @if(!empty($approvedBy[0]['approvedBy']))
            <h5><i class="" style="color: green;"></i> {{$approvedBy[0]['fname']}} {{$approvedBy[0]['mname']}}. {{$approvedBy[0]['lname']}}</h5>
            <p>{{$approvedBy[0]['position']}}</p>
            @else
            <h5>ASSD Manager</h5>
            <i>
                <p>waiting for approval</p>
            </i>
            @endif
        </div>
    </div>
</div>