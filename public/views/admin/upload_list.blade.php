@extends('layouts.admin')
@section('upload-list-active','side-active')
@section('title','Upload Lists')
@section('content')
<div class="content-wrapper">
    <list-component></list-component>
</div>
@endsection
@section('scripts')
<script>
    //DaterangePicker
    $(document).ready(function(){
        $('.date').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('.date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        });

        $('.date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });    
    })
</script>
@endsection