
var winners = [];

var genders = {};
var cities = {};
var ages = {};
var visits = {};

var genderchart;
var citieschart;
var ageschart;
var visitschart;

function splitlabelsandvalues(data) {
	var labels = [];
	var values = [];
	$.each(data, function(key, val) {
		values.push(val);
		labels.push(key);
	});
	return [{'labels': labels}, {'values':values}];
}

function setupCharts() {
	genderchart = createGenderChart(genders, "#genderchart");
	citieschart = createCityChart(cities, "#citieschart");
	ageschart = createAgeChart(ages, "#ageschart");
	visitschart = createVisitsChart(visits, "#visitschart");
}

function updateCharts() {
	genderchart.segments[0].value = genders['male'];
	genderchart.segments[1].value = genders['female'];
	genderchart.update();

	var citieschartinfo = splitlabelsandvalues(cities);
	citieschart.datasets[0].data = citieschartinfo['values'];
	citieschart.labels = citieschartinfo['labels'];
	citieschart.update();

	var ageschartinfo = splitlabelsandvalues(ages);
	ageschart.datasets[0].data = ageschartinfo['values'];
	ageschart.labels = ageschartinfo['labels'];
	ageschart.update();

	var visitschartinfo = splitlabelsandvalues(visits);
	visitschart.datasets[0].data = visitschartinfo['values'];
	visitschart.labels = visitschartinfo['labels'];
	visitschart.update();
}

function addStat(row) {
	var gender = row.children('.gender').text();
	var city = row.children('.city').text();
	var age = parseInt(row.children('.age').text());
	var visit = parseInt(row.children('.visits'));
	genders[gender] += 1;
	if( city in cities ) {
		cities[city] += 1;
	} else {
		cities[city] = 1;
	}
	if( age in ages ) {
		ages[age] += 1;
	} else {
		ages[age] = 1;
	}
	if( visit in visits ) {
		visits[visit] += 1;
	} else {
		visits[visit] = 1;
	}
}

function removeStat(row) {
	var gender = row.children('.gender').text();
	var city = row.children('.city').text();
	var age = parseInt(row.children('.age').text());
	var visit = parseInt(row.children('.visits'));
	genders[gender] -= 1;
	if( city in cities ) {
		cities[city] -= 1;
	} else {
		//error
	}
	if( age in ages ) {
		ages[age] -= 1;
	} else {
		//error
	}
	if( visit in visits ) {
		visits[visit] -= 1;
	} else {
		//error
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
	updateCharts();
});

$('.ok').on('click', function(e){
	//submit selected
});