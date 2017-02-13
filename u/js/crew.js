function get_email(element) {
	return element.closest("tr").find(".email").text();
}

$(document).ready(function() {
	$(".note").change(function() {
		var p = $(this).closest("tr").find(".working");
		p.html("<i class='glyphicon glyphicon-refresh spinning'></i>");
		$.post("saveSignupChange.php", {"email":get_email($(this)), "note":$(this).closest("tr").find(".note").val()}, function(response) {
			if( response == 0 ) {
				p.html("<i class='glyphicon glyphicon-ok'></i>");
			} else {
				p.html("<i class='glyphicon glyphicon-remove'></i>");
			}
		});
	});
	$(".share").change(function() {
		var p = $(this).closest("tr").find(".working");
		p.html("<i class='glyphicon glyphicon-refresh spinning'></i>");
		$.post("saveSignupChange.php", {"email":get_email($(this)), "share":$(this).closest("tr").find(".share").val()}, function(response) {
			if( response == 0 ) {
				p.html("<i class='glyphicon glyphicon-ok'></i>");
			} else {
				p.html("<i class='glyphicon glyphicon-remove'></i>");
			}
		});
	});
	$(".permission").change(function() {
		var p = $(this).closest("tr").find(".working");
		p.html("<i class='glyphicon glyphicon-refresh spinning'></i>");
		if( $(this).is(":checked") ) {
			$.post("saveUserChange.php", {"email":get_email($(this)), "permission_add":$(this).val()}, function(response) {
				if( response == 0 ) {
					p.html("<i class='glyphicon glyphicon-ok'></i>");
				} else {
					p.html("<i class='glyphicon glyphicon-remove'></i>");
				}
			});
		} else {
			$.post("saveUserChange.php", {"email":get_email($(this)), "permission_remove":$(this).val()}, function(response) {
				if( response == 0 ) {
					p.html("<i class='glyphicon glyphicon-ok'></i>");
				} else {
					p.html("<i class='glyphicon glyphicon-remove'></i>");
				}
			});
		}
	});
	$(".removecrew").click(function() {
		$.post("saveSignupChange.php", {"email":get_email($(this)), "task":""}, function(response) {
			$.post("saveUserChange.php", {"email":get_email($(this)), "permission_set":1}, function(response) {
				if( response == 0 ) {
					location.reload();
				}
			});
		});
	});
	$(".addcrew").click(function() {
		$.post("saveSignupChange.php", {"email":get_email($(this)), "task":"crew"}, function(response) {
			if( response == 0 ) {
				location.reload();
			}
		});
	});
});