$(document).ready(function() {
	$(".inloten").click(function() {
	    var item = $(this).closest("tr").find(".email").text();
	    $.post("storeRaffle.php", {"email":item}, function(response){
			console.log(response);
		});
	    $(this).attr("disabled", true);
	});
	$(".uitloten").click(function() {
	    var item = $(this).closest("tr").find(".email").text();
	    $.post("removeRaffle.php", {"email":item}, function(response){
			console.log(response);
		});
	    $(this).attr("disabled", true);
	});
});