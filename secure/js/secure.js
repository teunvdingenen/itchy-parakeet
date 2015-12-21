
function setContent(response) {
	$(".secure_content").html(response.find('.secure_content').html());
};

function setStatistics(response) {
	var json = JSON.parse(response);
	var ctx = $("#testchart").get(0).getContext("2d");
	var genderInfo = json['gender'];
	var data = [
		{
			value: genderInfo["male"],
			color: "#0000FF",
			label:"male"
		},{
			value: genderInfo["female"],
			color:"#FF0000",
			label:"female"
		}]
	var mytestchart = new Chart(ctx).Pie(data,{segmentShowStroke : true, animationSteps : 200 });
}


$(document).ready(function() {
	$("#displaysignup").click(function() {
		$.get("signups.php", function(response) {
			setContent($('<div>').html(response));
		});
		$.post("statistics.php",{"type":"signup"}, function(response){
			setStatistics(response);
		});
	});
	$("#displaybuyers").click(function() {
		$.get("buyers.php", function(response) {
			setContent($(response));
		});
	});
	$("#displayraffle").click(function() {
		$.get("raffle.php", function(response) {
			setContent($(response));
		});
	});
	$("#raffle").click(function() {
		$.get("signups.php", function(response) {
			setContent($(response));
		});
	});
	$("#editsignup").click(function() {
		$.get("editsignups.php", function(response) {
			setContent($(response));
		});
	});
	$("#removesignup").click(function() {
		$.get("removesignups.php", function(response) {
			setContent($(response));
		});
	});
	$("#usermanage").click(function() {
		$.get("users.php", function(response) {
			setContent($(response));
		});
	});
});