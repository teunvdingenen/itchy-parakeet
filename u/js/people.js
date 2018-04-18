$(document).ready(function() {
	$(".signup").click(function() {
		if($(this).attr("disabled")) return;
		var button = $(this);
	    var item = $(this).closest("tr").find(".email").text();
	    $.post("dosignup.php", {"email":item}, function(response) {
	    	if( response == 0 ) {
	    		button.attr("disabled", true);
	    	}
	    });
	});
});