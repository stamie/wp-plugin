var $ = jQuery.noConflict();
$("#date_from_felso").dcalendarpicker({"show": "mycalendar2"});
$("#date_from").dcalendarpicker({"show": "mycalendar"});
$("#check_in").dcalendarpicker({"show": "mycalendar"});


$("#date_from_felso").dcalendarpicker().show(function(){

    $("#mycalendar2").find('.calendar-curr-month').tips({
        skin: "top",
        msg: 'Év hónap választó'    
    });
});
$("#date_from").dcalendarpicker().show(function(){

    $("#mycalendar").find('.calendar-curr-month').tips({
        skin: "top",
        msg: 'Év hónap választó'    
    });
});
$("#check_in").dcalendarpicker().show(function(){
    $("#mycalendar").find('.calendar-curr-month').tips({
        skin: "top",
        msg: 'Év hónap választó'    
    });
});

/*
$("#mycalendar").find('.calendar-curr-month').tips({
    skin: "top",
    msg: 'Év hónap választó'
});
*/
