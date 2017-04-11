

$(document).ready(function() {
	//$('#swapmodal').modal() 
	$(".buyticket").click(function() {
		$.post("reserveticket.php", {"code":$(this).closest('tr').find('.code').html()}, function(response) {
			console.log(response);
			if( response == 0 ) {
				window.location.replace("/u/deelname");
			} else {
				$('#swapmodal').show();
			}
		});
	});
});