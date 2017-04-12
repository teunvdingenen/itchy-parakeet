$(document).ready(function() {
	//$('#swapmodal').modal() 
	$(".buyticket").click(function() {
		$.post("reserveticket.php", function(response) {
			if( response == 0 ) {
				window.location.replace("deelname");
			} else {
				$('#swapmodal').show();
			}
		});
	});

	$(".undo").click(function() {
		$('.undo').attr('disabled','disabled');
		$.post("undoSwap.php",function(response) {
			if( response == 0 ) {
				$('.undosale').html("Gelukt! Je ticket staat niet langer te koop");
			} else {
				$('.undosale').html("Het lijkt erop dat iemand ondertussen je ticket al gekocht heeft. Het kan zijn dat diegene zich bedenkt en het later dus weer mogelijk wordt om je ticket terug te trekken.");
			}
		});
	})
	
});