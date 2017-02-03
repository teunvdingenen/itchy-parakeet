
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
	if( $("#togglestats").length ) {
		$("#togglestats").remove();
	}
	$("#menu").after("<div id='statsbar' class='statsbar'></div><div id='togglestats' class='togglestats'><a class='togglestatslink' id ='togglestatslink' href='#'>X</a></div>");
	$("#content").css("height", "80%");
	$("#content").css("bottom", "0");
	$(".togglestatslink").click(function() {
		if( $("#statsbar").length ) {
			removeStatsBar();
			$("#togglestatslink").text("^");
		} else {
			addStatsBar();
		}
	});
}

function removeStatsBar() {
	if( $("#statsbar").length ) {
		$("#statsbar").remove();
	}
	$("#content").css("height", "98%");
}

$(document).ready(function() {
  $('[data-toggle=offcanvas]').click(function() {
    $('.row-offcanvas').toggleClass('active');
  });
  setInterval(function() {
  	$.ajax({
       url: 'checklogin.php',
       cache: false,
    });
  }, 600000);
});
