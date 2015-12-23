function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function HSVtoRGB(h, s, v) {
    var r, g, b, i, f, p, q, t;
    if (arguments.length === 1) {
        s = h.s, v = h.v, h = h.h;
    }
    i = Math.floor(h * 6);
    f = h * 6 - i;
    p = v * (1 - s);
    q = v * (1 - f * s);
    t = v * (1 - (1 - f) * s);
    switch (i % 6) {
        case 0: r = v, g = t, b = p; break;
        case 1: r = q, g = v, b = p; break;
        case 2: r = p, g = v, b = t; break;
        case 3: r = p, g = q, b = v; break;
        case 4: r = t, g = p, b = v; break;
        case 5: r = v, g = p, b = q; break;
    }
    return {
        r: Math.round(r * 255),
        g: Math.round(g * 255),
        b: Math.round(b * 255)
    };
}

function createVisitsChart(visits) {
	var ctx = $("#visitschart").get(0).getContext("2d");
	var colorParts = 1 / 10;
	var i = 0;
	var data = [];
	$.each(visits, function(key, val) {
		if( i == 0 ) { //total
		} else {
			var rgbcolor = HSVtoRGB(0.5, 0.2, i*colorParts);
			var colorhex = rgbToHex(rgbcolor.r, rgbcolor.g, rgbcolor.b);
			data.push({value: val, color: colorhex, label:key});
		}
		i++;
		
	});
	return new Chart(ctx).Pie(data,{segmentShowStroke : true, animationSteps : 200 });
}

function createSignDateChart(signupdates) {
	var ctx = $("#signupschart").get(0).getContext("2d");
	var labeldata = [];
	var dates = [];
	$.each(signupdates, function(key, val) {
		dates.push(val);
		labeldata.push(key);
	});
	var linedata = {
		labels : labeldata,
		datasets : [{
			label: "Inschrijvingen",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: dates
		}]
	};
	return new Chart(ctx).Line(linedata);
}

function createContribChart(contrib, isZero) {
	var ctx;
	if( isZero ) {
		ctx = $("#contrib0chart").get(0).getContext("2d");
	} else {
		ctx = $("#contrib1chart").get(0).getContext("2d");
	}
	var elements = 0; 
	$.each(contrib, function(key) {
		elements++;
	});
	var colorParts = 1 / (elements + 4);
	var data = [];
	var i = 2;
	$.each(contrib, function(key, val) {
		var rgbcolor = HSVtoRGB(0.5, 0.2, i*colorParts);
		var colorhex = rgbToHex(rgbcolor.r, rgbcolor.g, rgbcolor.b);
		data.push({value: val, color: colorhex, label:key});
		i++;
	});
	return new Chart(ctx).Pie(data,{segmentShowStroke : true, animationSteps : 200 });
}

function createGenderChart(genderInfo) {
	var ctx = $("#genderchart").get(0).getContext("2d");
	var colorMale = HSVtoRGB(0.5, 0.2, 0.3);
	colorMale = rgbToHex(colorMale.r, colorMale.g, colorMale.b);
	var colorFemale = HSVtoRGB(0.5, 0.2, 0.6);
	colorFemale = rgbToHex(colorFemale.r, colorFemale.g, colorFemale.b);
	var data = [
		{
			value: genderInfo["male"],
			color: colorMale,
			label:"male"
		},{
			value: genderInfo["female"],
			color:colorFemale,
			label:"female"
		}];
	return new Chart(ctx).Pie(data,{segmentShowStroke : true, animationSteps : 200 });
}

function createCityChart(cities) {
	var ctx = $("#citychart").get(0).getContext("2d");
	var elements = 0; 
	$.each(cities, function(key) {elements++;});
	var data = [];
	var colorParts = 1 / (elements + 4);
	var i = 0;
	$.each(cities, function(key, val) {
		var rgbcolor = HSVtoRGB(0.5, 0.2, i*colorParts);
		var colorhex = rgbToHex(rgbcolor.r, rgbcolor.g, rgbcolor.b);
		data.push({value: val, color: colorhex, label:key});
		i++;
	});
	return new Chart(ctx).Pie(data,{segmentShowStroke : true, animationSteps : 200 });
}

function createAgeChart(age) {
	var ctx = $("#agechart").get(0).getContext("2d");
	var labeldata = [];
	var dates = [];
	$.each(age, function(key, val) {
		dates.push(val);
		labeldata.push(key);
	});
	var linedata = {
		labels : labeldata,
		datasets : [{
			label: "Leeftijden",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: dates
		}]
	};
	return new Chart(ctx).Line(linedata);
}

function createCharts(response) {
	var json = JSON.parse(response);
	createGenderChart(json['gender']);
	createSignDateChart(json['signupdates']);
	createVisitsChart(json['visits']);
	createContribChart(json['contrib0'], true);
	createContribChart(json['contrib1'], false);
	createCityChart(json['city']);
	createAgeChart(json['ages']);
}


$.post("signupstats.php",{"type":"signup"}, function(response){
	console.log(response);
	createCharts(response);	
});