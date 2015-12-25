
var winners = [];

var men = 0;
var women = 0;
var cities = [];
var ages = [];
var visits = [];

function setupCharts() {
	
}

function updateCharts() {

}

function addStat(row) {
	var gender = row.children('.gender').text();
	var city = row.children('.city').text();
	var age = parseInt(row.children('.age').text());
	var visit = parseInt(row.children('.visits'));
	if(gender == 'male') {
		men += 1;
	} else if(gender == 'female') {
		women += 1;
	} else {
		console.log("Unknown gender: " + gender);
	}
	//cities
	//ages

	//visits
}

function removeStat(row) {
	var gender = row.children('.gender').text();
	var city = row.children('.city').text();
	var age = parseInt(row.children('.age').text());
	var visit = parseInt(row.children('.visits'));
	if(gender == 'male') {
		men -= 1;
	} else if(gender == 'female') {
		women -= 1;
	} else {
		console.log("Unknown gender: " + gender);
	}
}

$(".raffle-table tr").on('click', function() {
	var email = $(this).children('.email').text();
	if( $(this).hasClass('selected')) {
		$(this).removeClass('selected');
		var index = winners.indexOf(email);
		if (index >= 0) {
		  winners.splice( index, 1 );
		}
		removeStat($(this));
	} else {
		$(this).addClass('selected');
		winners.push(email);
		addStat($(this));
	}
});

$('.ok').on('click', function(e){
	//submit selected
});