$(document).ready(function() {
    $('#formtoggle').on('click', function(){
        $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
    });
});