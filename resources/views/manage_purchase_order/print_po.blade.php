<div>
    <button class="btn btn-warning print_btn"><i class="fa fa-print"></i> Print</button>
</div>
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

        form {
            color: black;
        }
    }
</style>