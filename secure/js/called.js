function called(code) {
	setCalledVariable(code, 1);
}

function notAnswered(code) {
	setCalledVariable(code, 3);
}

function setCalledVariable(code, value) {
	$.post("setCalled.php", {"code":code, "value":value}, function(response){
		console.log(response);
	});
	location.reload();
}