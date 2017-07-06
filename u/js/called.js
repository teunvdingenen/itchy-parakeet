function get_email(element) {
	return element.closest("tr").find(".email").text();
}

$(document).ready(function() {
	$(".called").click(function() {
		var tr = $(this).closest('tr');
		$.post("saveCall.php", {"email":get_email($(this)), "called":1}, function(response) {
			if( response == 0 ) {
				tr.find('td').fadeOut(1000, function(){ 
		            $(this).parents('tr:first').remove();                    
		        });
		        location.reload(true);
			} else {
				alert("Wijziging niet opgeslagen");
			}
		});
	});
	$(".nocall").click(function() {
		var tr = $(this).closest('tr');
		$.post("saveCall.php", {"email":get_email($(this)), "called":2}, function(response) {
			if( response == 0 ) {
				tr.find('td').fadeOut(1000, function(){ 
		            $(this).parents('tr:first').remove();                    
		        });
		        location.reload(true);
			} else {
				alert("Wijziging niet opgeslagen");
			}
		});
	});
	$(".notcalled").click(function() {
		var tr = $(this).closest('tr');
		$.post("saveCall.php", {"email":get_email($(this)), "called":0}, function(response) {
			if( response == 0 ) {
				tr.find('td').fadeOut(1000, function(){ 
		            $(this).parents('tr:first').remove();                    
		        });  
		        location.reload(true);
			} else {
				alert("Wijziging niet opgeslagen");
			}
		});
	});
});