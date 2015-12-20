
function setContent(response) {
	$(".secure_content").html(response.find('.secure_content').html());
};

$(document).ready(function() {
	$("#displaysignup").click(function() {
		$.get("securesignups.php", function(response) {
			setContent($('<div>').html(response));
		});
	});
	$("#displaybuyers").click(function() {
		$.get("securesignups.php", function(response) {
			setContent($(response));
		});
	});
	$("#displayraffle").click(function() {
		$.get("securesignups.php", function(response) {
			setContent($(response));
		});
	});
	$("#raffle").click(function() {
		$.get("securesignups.php", function(response) {
			setContent($(response));
		});
	});
	$("#editsignup").click(function() {
		$.get("securesignups.php", function(response) {
			setContent($(response));
		});
	});
	$("#removesignup").click(function() {
		$.get("securesignups.php", function(response) {
			setContent($(response));
		});
	});
	$("#usermanage").click(function() {
		$.get("securesignups.php", function(response) {
			setContent($(response));
		});
	});
});