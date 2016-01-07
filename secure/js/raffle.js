
var winners = [];

var genders = [];
var cities = {};
var ages = {};
var visits = {};

var genderchart;
var citieschart;
var ageschart;
var visitschart;

function lookup( arr, name ) {
    $.each(arr, function(key, value) {
        if( name === key )
            return true;
    });
    return false;
}

function splitlabelsandvalues(data) {
	var labels = [];
	var values = [];
	$.each(data, function(key, val) {
		if( val === null ) {
		} else {
			values.push(val);
			labels.push(key);
		}
	});
	return [{'labels': labels}, {'values':values}];
}

function setupCharts() {
	genders = {'male':0,'female':0};
	$.post("signupstats.php", {"type":"raffle"}, function(response){
		var json = JSON.parse(response);
		genderchart = createGenderChart(json['gender'], "#genderchart");
		citieschart = createCityChart(json['city'], "#citieschart");
		ageschart = createAgeChart(json['ages'], "#ageschart");
		visitschart = createVisitsChart(json['visits'], "#visitschart");
	});
}

function updateCharts() {
	if(genderchart.segments[0].value == 0 && genderchart.segments[1].value == 0) {
		genderchart = createGenderChart(genders, "#genderchart");
	} else {
		genderchart.segments[0].value = genders['male'];
		genderchart.segments[1].value = genders['female'];
		genderchart.update();
	}
	citieschart = createCityChart(cities, "#citieschart");
	ageschart = createAgeChart(ages, "#ageschart");
	visitschart = createVisitsChart(visits, "#visitschart");
}

function addStat(gender, city, age, visit) {
	genders[gender] = genders[gender]+1;
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
	updateCharts();
}

function removeStat(gender, city, age, visit) {
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
	updateCharts();
}

$(".raffle-table tr").on('click', function() {
	var email = $(this).children().children('#email').text();
	var gender = $(this).children().children('#gender').text();
	var city = $(this).children().children('#city').text();
	var age = calculateAge(new Date($(this).children().children('#birthdate').text()));
	var visit = parseInt($(this).children().children('#visits').text());
	if( $(this).hasClass('selected')) {
		$(this).removeClass('selected');
		var index = winners.indexOf(email);
		if (index >= 0) {
		  winners.splice( index, 1 );
		}
		removeStat(gender, city, age, visit);
	} else {
		$(this).addClass('selected');
		winners.push(email);
		addStat(gender, city, age, visit);
	}
});

function storeWinners() {
	$.post("storeRaffle.php", {"winners":winners}, function(response){
		//var json = JSON.parse(response);
		console.log(response);
	});
}

//$('.confirm').on('click', function(e){
	
//});

$(document).ready(function() {setupCharts();});