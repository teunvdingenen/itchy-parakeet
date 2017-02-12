function get_email(element) {
	return element.closest("tr").find(".email").text();
}

$(document).ready(function() {
	$(".note").change(function() {
		$.post("savechange.php", {"email":get_email($(this)), "note":$(this).closest("tr").find(".note").val()}, function(response) {
			//show saved
		});
	});
	$(".share").change(function() {
		$.post("savechange.php", {"email":get_email($(this)), "share":$(this).closest("tr").find(".share").val()}, function(response) {
			//show saved
		});
	});
	$(".permission").change(function() {
		if( $(this).is(":checked") ) {
			console.log("add: "+$(this).val());
			$.post("savechange.php", {"email":get_email($(this)), "permission_add":$(this).val()}, function(response) {
				//show saved
			});
		} else {
			console.log("Remove: "+$(this).val());
			$.post("savechange.php", {"email":get_email($(this)), "permission_remove":$(this).val()}, function(response) {
				//show saved
			});
		}
	});
	$(".removecrew").change(function() {
		$.post("savechange.php", {"email":get_email($(this)), "task":""}, function(response) {
			//show saved
		});
	});
	$(".addcrew").change(function() {
		$.post("savechange.php", {"email":get_email($(this)), "task":"crew"}, function(response) {
			//show saved
		});
	});
});