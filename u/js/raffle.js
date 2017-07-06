$(document).ready(function() {
	$(".inloten").click(function() {
		if($(this).attr("disabled")) return;
		var button = $(this);
	    var item = $(this).closest("tr").find(".email").text();
	    $.post("checkpartner.php", {"email":item}, function(response) {
	    	obj = JSON.parse(response);
	    	if( obj.success ) {
	    		$('#raffle-modal-content').text("Lieveling wordt ook ingelood: "+obj.firstname+" "+obj.lastname);
	    		$('#emailA').text(item);
	    		$('#emailB').text(obj.email);
	    		$('#rafflemodal').modal('show');
	    	} else {
	    		$.post("storeRaffle.php", {"email":[item]}, function(response){
				});
				button.attr("disabled", true);
	    	}
	    });
	});
	$(".uitloten").click(function() {
		if($(this).attr("disabled")) return;
		var button = $(this);
	    var item = $(this).closest("tr").find(".email").text();
	    $.post("checkpartner.php", {"email":item}, function(response) {
	    	obj = JSON.parse(response);
	    	if( obj.success ) {
	    		$('#raffle-modal-content').text("Lieveling wordt ook uitgelood: "+obj.firstname+" "+obj.lastname);
	    		$('#emailA').text(item);
	    		$('#emailB').text(obj.email);
	    		$('#rafflemodal').modal('show');
	    	} else {
	    		$.post("removeRaffle.php", {"email":item}, function(response){
				});
				button.attr("disabled", true);
	    	}
	    });
	});
	$("#lieveling-inloten").click(function() {
		var email = $('#emailA').text();
		var partner = $('#emailB').text();
		$.post("storeRaffle.php", {"email":[email]}, function(response){
			$.post("storeRaffle.php", {"email":[partner]}, function(response){
				$('#rafflemodal').modal('hide');
				location.reload();
			});
		});
	});
	$("#lieveling-uitloten").click(function() {
		var email = $('#emailA').text();
		var partner = $('#emailB').text();
		$.post("removeRaffle.php", {"email":email}, function(response){
			$.post("removeRaffle.php", {"email":partner}, function(response){
				$('#rafflemodal').modal('hide');
				location.reload();
			});
		});
	});
	$(".raffle_all").click(function() {
		if( $(this).attr('disabled')) return;
		var items = [];
		$('.table tbody tr').each( function(){
		   items.push($(this).find(".email").text());
		});
		$.post("storeRaffle.php", {"email":items, "auto_partner":1}, function(response){
			console.log(response);
			location.reload();
		});
	});
});