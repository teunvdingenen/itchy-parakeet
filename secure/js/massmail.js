function insertValue(value) {
    var cursorPos = $('#content').prop('selectionStart');
    var v = $('#content').val();
    var textBefore = v.substring(0,  cursorPos);
    var textAfter  = v.substring(cursorPos, v.length);

    $('#content').val(textBefore + value + textAfter);
    return false;
}
function confirmSubmit() {
    if(confirm('Weet je zeker dat je al deze mensen wilt mailen?')) {
        $("#submitbutton").html("<span class='glyphicon glyphicon-refresh spinning'></span>");
        return true;
    } else {
        return false;
    }
}

$(document).ready(function() {
    $("#submitbutton").text("Verzenden");
});