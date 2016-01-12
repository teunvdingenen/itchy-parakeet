
function setContent(response) {
	$(".secure_content").html(response.find('.secure_content').html());
};

function setStatsContent(response) {
	$("#statsbar").html(response.find('#statsbar').html());
}

function addCanvas(element, name) {
	$(element).append("<canvas id='"+name+"'' class='graphcanvas'></canvas>");
}

function addStatsBar() {
	if( $("#statsbar").length ) {
		$("#statsbar").remove();
	}
	$("#menu").after("<div id='statsbar' class='statsbar'></div>");
	$("#content").css("height", "80%");
	$("#content").css("bottom", "0");
}

function removeStatsBar() {
	if( $("#statsbar").length ) {
		$("#statsbar").remove();
	}
	$("#content").css("height", "100%");
}

$(document).ready(function() {
	$("#showstats").click(function() {
		$.get("statspage.php", function(response) {
			setContent($('<div>').html(response));
			$.post("signupstats.php", {"type":"signup"}, function(response){
                var json = JSON.parse(response);
                createGenderChart(json['gender'], "#genderchart");
                createSignDateChart(json['signupdates'], "#signupschart");
                createVisitsChart(json['visits'], "#visitschart");
                createContribChart(json['contrib0'], "#contrib0chart");
                createContribChart(json['contrib1'], "#contrib1chart");
                createCityChart(json['city'], "#citychart");
                createAgeChart(json['ages'], "#agechart");
            });
			removeStatsBar();
		});
	});
	$("#displaysignup").click(function() {
		$.get("signups.php", function(response) {
			setContent($('<div>').html(response));
		});
		$.post("signupstats.php",{"type":"signup"}, function(response){
			addStatsBar();
			setStatsContent($('<div>').html(response));
		});
	});
	$("#displaybuyers").click(function() {
		$.get("displaybuyers.php", function(response) {
			setContent($(response));
		});
	});
	$("#displayraffle").click(function() { 
		$.get("displayraffle.php", function(response) {
			setContent($('<div>').html(response));
			removeStatsBar();
			$.getScript("js/called.js", function() {

			});
		});
		/**
		$.post("signupstats.php",{"type":"raffle"}, function(response){
			var json = JSON.parse(response);
			genderchart = createGenderChart(json['gender'], "#genderchart");
			citieschart = createCityChart(json['city'], "#citieschart");
			ageschart = createAgeChart(json['ages'], "#ageschart");
			visitschart = createVisitsChart(json['visits'], "#visitschart");
		});
		**/
	});
	$("#raffle").click(function() {
		$.get("raffle.php", function(response) {
			setContent($('<div>').html(response));
			addStatsBar();
			setStatsContent($('<div>').html(response));
			$.getScript("js/raffle.js", function() {
				$(document).ready(function() {setupCharts();});
			});
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