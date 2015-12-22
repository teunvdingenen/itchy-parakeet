
function setContent(response) {
	$(".secure_content").html(response.find('.secure_content').html());
};

function addCanvas(element, name) {
	$(element).append("<canvas id='"+name+"'' class='graphcanvas'></canvas>");
}

function setStatisticsTitle(response) {
	var json = JSON.parse(response);

	var genderInfo = json['gender'];
	addCanvas('.statsbar', 'gender');
	var ctx = $("#gender").get(0).getContext("2d");
	var data = [
		{
			value: genderInfo["male"],
			color: "#0000FF",
			label:"male"
		},{
			value: genderInfo["female"],
			color:"#FF0000",
			label:"female"
		}];
	var mytestchart = new Chart(ctx).Pie(data,{segmentShowStroke : true, animationSteps : 200 });
}

function addStatsBar() {
	$("#menu").after("<div id='statsbar' class='statsbar'></div>");
	$("#content").css("height", "80%");
	$("#content").css("bottom", "0");
}

$(document).ready(function() {
	$("#showstats").click(function() {
		$.get("stats.php", function(response) {
			setContent($('<div>').html(response));
		});
	});
	$("#displaysignup").click(function() {
		$.get("signups.php", function(response) {
			setContent($('<div>').html(response));
		});
		$.post("statistics.php",{"type":"signup"}, function(response){
			addStatsBar();
			setStatisticsTitle(response);
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