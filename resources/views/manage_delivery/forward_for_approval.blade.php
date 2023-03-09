@extends('manage_forwarded.view_forwarded')
@section('print')
<div class="modal-footer" id="printPageButton">
    @if(!empty($isApproved[0]['isApproved']))
    <button class="btn btn-warning print_btn"><i class="fa fa-print"></i> Print</button>
</div>
@else
@endif
<script>
    $('.print_btn').click(function() {
        window.print();
        return false;
    });
</script>
<style>
    @media print {
        #printPageButton {
            display: none;
        }

        #body_color {
            color: black;
        }

        hr {
            border-color: black;
        }
    }
</style>

@endsection