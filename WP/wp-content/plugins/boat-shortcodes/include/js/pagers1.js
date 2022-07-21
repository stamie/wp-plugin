
var x = document.getElementsByClassName("boatsearch");
function run_waitMe(el, num, effect){
    text = 'Please wait...';
    fontSize = '';
    switch (num) {
        case 1:
        maxSize = '';
        textPos = 'vertical';
        break;
        case 2:
        text = '';
        maxSize = 30;
        textPos = 'vertical';
        break;
        case 3:
        maxSize = 30;
        textPos = 'horizontal';
        fontSize = '18px';
        break;
    }
    el.waitMe({
        effect: 'win8',
        text: text,
        bg: 'rgba(255,255,255,0.8)',
        color: '#000',
        maxSize: maxSize,
        waitTime: -1,
        source: '',
        textPos: textPos,
        fontSize: fontSize,
        onClose: function(el) {}
    });
}
function pageres()
 {
    //alert("hello");
    jQuery(".href-page").on("click", function(){

        var pageNum = jQuery(this).attr('attr-page'); 
        var firstIndex = jQuery(this).attr('attr-first-index');
        var boatList = jQuery('#boat-lists').val();
        var datas = jQuery('#datas').val();


        jQuery.ajax({
            type: "POST",
            url: "/wp-content/plugins/boat-shortcodes/include/ajaxGetPageBoatList.php",
            // The key needs to match your method's input parameter (case-sensitive).
            data: { 
                'firstIndex': firstIndex,
                'boatList': JSON.parse(boatList),
                'datas': JSON.parse(datas),
                'page_num' : pageNum,
               
            },
            beforeSend:function(data){
                jQuery(".loader").addClass("waitMeTo_Container");
                jQuery('.waitContainer').css({'height':'50px'});
                run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');
            },
            success:function(data){
                jQuery("#boats-lists").html(data);
                jQuery(".page").pagerRefresh1(pageNum);
                load_info_bouble();
                jQuery(".loader").removeClass("waitMeTo_Container");
                jQuery('.waitContainer').css({'height':'0px'});
                jQuery('.waitContainer').waitMe("hide");
                pageres();
                var date_from = $("#date_from").val();
                $(".fromto").each(function(){
                    if($(this).attr('attr-from')!==date_from){
                        $(this).addClass('flex');
                    }
                });
            }
        });
    });
    load_info_bouble();
}
function convertDate(date){
    var day   = date.slice(0,2);
    var month = date.slice(3,5);
    var year  = date.slice(6,10);
    var return_  = year+'-'+month+'-'+day;
    return return_;
}
function load_info_bouble(prevMessage = 'Previously available period', nextMessage = 'Next booking period'){
    var id = []
    var date_from = [];
    var date_to   = [];
    jQuery(".inf_bouble_state").each(function(){
        id.push(jQuery(this).attr('attr-id'));
        date_from.push(jQuery(this).attr('attr-from'));
        date_to.push(jQuery(this).attr('attr-to'));
    });
    if (id.length>0){
        jQuery.ajax({
            type: "POST",
            url: "/wp-content/plugins/boat-shortcodes/include/ajaxGetInformations.php",
            // The key needs to match your method's input parameter (case-sensitive).
            dataType: 'json',
            data: {
                'id': id,
                'date_from': date_from,
                'date_to': date_to
            }
        }).done(function(datas){
            var datas_ = datas.returns;
            var size = Object.keys(datas_).length;

            for(var index = 0; index < size; index++) {

                var id_ = id[index];
                jQuery("#bouble_"+id_).addClass('inf_bouble');
                var next_period = JSON.parse(datas_[index]).nextFreePeriod;
                var prev_period = JSON.parse(datas_[index]).prevFreePeriod;
                var msg1 = 'not available';
                var msg2 = 'not available';
                
                if (next_period){
                    msg1 = convertDate(next_period.freeYachts[0].periodFrom)+' - '+convertDate(next_period.freeYachts[0].periodTo);

                }
                if (prev_period){
                    msg2 = convertDate(prev_period.freeYachts[0].periodFrom)+' - '+convertDate(prev_period.freeYachts[0].periodTo);
                }
                
                jQuery("#bouble_"+id_).tips({
                    msg:  nextMessage+':<br>'+msg1+'<br>'+prevMessage+':<br>'+msg2,
                });
            }
        });
    }
}
jQuery.fn.extend({
    pagerRefresh1 : function(pagenum) {
        var hrefs = jQuery(this).parent("li").parent("ul").children("li");
        var nextLi = jQuery(this).parent("li").next();
        var prevLi = jQuery(this).parent("li").prev();

        if (1 < parseInt(pagenum)){
            jQuery(".page-first").css('display', 'inherit');
            jQuery(".page-prev").css('display', 'inherit');
        } else {
            jQuery(".page-first").css('display', 'none');
            jQuery(".page-prev").css('display', 'none');
        }
        var firstIndexPrev = 0;
        var firstIndexNext = -1;
        hrefs.each(function() {
            var page = jQuery(this).children(".page").first();
            page.removeClass("current");
            if (parseInt(page.children(".href-page").first().attr("attr-page")) == (parseInt(pagenum)-1)) {
                firstIndexPrev = page.children(".href-page").first().attr("attr-first-index");
            } else if (page.children(".href-page").first().attr("attr-page") == pagenum){
                page.addClass("current");
            } else if (parseInt(page.children(".href-page").first().attr("attr-page")) == (parseInt(pagenum)+1)) {
                firstIndexNext = page.children(".href-page").first().attr("attr-first-index");
            }
        });

        if (hrefs.size()-4 > parseInt(pagenum)){
            jQuery(".page-last").css('display', 'inherit');
            jQuery(".page-next").css('display', 'inherit');
        } else  {
            jQuery(".page-last").css('display', 'none');
            jQuery(".page-next").css('display', 'none');
        }
        var pageNum = parseInt(pagenum)-1;
        jQuery(".page-prev").attr("attr-page", pageNum);
        jQuery(".page-prev").children(".href-page" ).first().attr("attr-page", pageNum);
        jQuery(".page-prev").children(".href-page" ).first().attr("attr-first-index", firstIndexPrev);

        pageNum = pageNum+2;
        jQuery(".page-next").attr("attr-page", pageNum);
        jQuery(".page-next").attr("attr-first-index", );
        jQuery(".page-next").children(".href-page" ).attr("attr-page", pageNum);
        jQuery(".page-next").children(".href-page" ).attr("attr-first-index", firstIndexNext);
        
        if (hrefs.size()-4 == 1){
            jQuery(".page-first").css('display', 'none');
            jQuery(".page-prev").css('display', 'none');
            hrefs.css('display', 'none');
            
        }

        jQuery('html, body')
        .stop()
        .animate({
            scrollTop: jQuery("#boatlists-prev").offset().top
        }, 150);
 
    }
});
/*
if (x){
    
}
*/