<script type="text/javascript" src="{{ asset('/js/datePicker01.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{ assetV('/js/backend/booking.js')}}"></script>
<script type="text/javascript">
   window["csrf_token"] = "{{ csrf_token() }}";
   window["uRole"] = "<?php echo isset($uRole) ?  $uRole : null; ?>";
   window["update"] = "{{ $update ?? 0 }}";
</script>
<script src="/js/backend/booking_script.js" type="text/javascript"></script>
