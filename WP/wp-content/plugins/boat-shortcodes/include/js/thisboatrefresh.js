var x = jQuery("#boats-list").html();
function dateToYMD(date) {
    var d = date.getDate();
    var m = date.getMonth() + 1; //Month from 0 to 11
    var y = date.getFullYear();
    return '' + y + '-' + (m<=9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
}

function format(numberInt){

    var number = numeral(numberInt);
    var string = number.format('0,0');
    string = string.replace(',', ' ');
    return string;
}

function floatFormat(numberFloat){
    var number = numeral(numberFloat);
    var string = number.format('0,0.00');
    string = string.replace(',', ' ');
    string = string.replace('.', ',');
    return string;
}
function thisboatrefresh(){
    //console.log(x);
    if (x === undefined || x === null) {
        var boat_id   = jQuery(".title").first().attr("attr-id");
        var date_from = jQuery("#check_in").val();
        var duration  = jQuery("#duration").val();

        var dateFrom = new Date(date_from);
        var dateTo = new Date(dateFrom.getFullYear(), dateFrom.getMonth(), dateFrom.getDate()+parseInt(duration));
       
        $.ajax({
            type: "POST",
            url: "/wp-content/plugins/boat-shortcodes/include/ajaxGetThisBoat.php",
            // The key needs to match your method's input parameter (case-sensitive).
            dataType: "json",
            data: { 
                'boat_id': boat_id,
                'date_from'   : (date_from && date_from!='')?date_from:null,
                'duration'    : duration,
            },
            beforeSend:function(){
                jQuery(".loader").addClass("waitMeTo_Container");
                jQuery('.waitContainer').css({'height':'50px'});
                run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');
              //  $("#count_of_boats").mouseenter();

            },
            success:function(data){
                // alert('cica');
                var myArray = data;
                if (myArray["currency"] !== undefined) { //alert("cica");
                    var price1 = format(parseFloat(myArray["listPrice"]));
                    var price2 = format(parseFloat(myArray["priceForUser"]));
                    var listPrice    = parseFloat(myArray["listPrice"]);
                    var priceForUser = parseFloat(myArray["priceForUser"]);

                    $(".rentalPrice").find("td").end().html(floatFormat(myArray["priceForUser"])+' '+myArray["currency"]);
                    $(".rentalTotal").find("td").end().html(format(myArray["priceForUser"])+' '+myArray["currency"]);
                    $(".deposit").find("td").end().html(format(myArray["deposit"])+' '+myArray["currency"]);

                   
                    
                    
                    $(".list-original-price").addClass("del");
                    $(".list-original-price").addClass("list-original-price-single");
                    if (price1==price2){
                        $(".list-original-price").removeClass("del");
                        $(".list-original-price-single").removeClass("list-original-price");
                        $(".list-original-price-single").html(price1   + ' ' +myArray["currency"]);
    
                    
                    } else {
                        $(".list-original-price").addClass("del");
                        $(".list-original-price").html(price1   + ' ' +myArray["currency"]);
                        $(".list-original-price").removeClass("list-original-price-single");
                        $(".list-discounted-price").html(price2 + ' ' +myArray["currency"]);
                    }
                   
                    $("span.port").html(myArray["cityFrom"]);
                    var month1 = (dateFrom.getMonth()+1)<10?('0'+(dateFrom.getMonth()+1)):''+(dateFrom.getMonth()+1);
                    var month2 = (dateTo.getMonth()+1)<10?('0'+(dateTo.getMonth()+1)):''+(dateTo.getMonth()+1);
                    var day1   = (dateFrom.getDate()<10)?('0'+dateFrom.getDate()):''+dateFrom.getDate();
                    var day2   = (dateTo.getDate()<10)?('0'+dateTo.getDate()):''+dateTo.getDate();
                    var htmlDateFromTo = dateFrom.getFullYear()+'-'+month1+'-'+day1+' to '+dateTo.getFullYear()+'-'+month2+'-'+day2;

                    jQuery(".dateFromTo").html(htmlDateFromTo);
                    if (myArray['discounts'] && Array.isArray(myArray['discounts'])){
                        myArray['discounts'].forEach(function(value){
                            var amount = myArray["currency"];
                            if (value['type'] == 'PERCENTAGE')
                                amount = '%';
                            var discountItem = '.discount_'+value['discountItemId'];
                            var html = '<td>'+htmlDateFromTo+'</td>'+'<td>'+value['discountItemName']+'</td>'+'<td>'+value['type']+'</td>'+'<td>'+value['amount']+' '+amount+'</td>';

                            if (jQuery(discountItem).html() !== undefined){
                                jQuery(discountItem).html(html);
                            } else {
                                discountItem = 'discount_'+value['discountItemId'];
                                html = '<tr class="'+discountItem+'">'+html+'</tr>';
                                var table = jQuery(".property-table-right").first();
                                table.append(html);
                            }
                        });
                    }

                    
                    var dateFrom_   = htmlDateFromTo.substring(0,11);
                    var dateTo_     = htmlDateFromTo.substring(14);

                    if (jQuery("a.button.make_option").first().attr("href") !== undefined){ 
                        var href = jQuery("a.button.make_option").first().attr("href");console.log(href);
                        if (href !== undefined && href.indexOf("&")>-1){
                            var index = href.indexOf("&")+1;
                            if(href.indexOf("&", index)==-1)
                                index = 0;
                                console.log(href);
                            href = href.substring(0,href.indexOf("&", index));
                        }
                        jQuery("a.button.make_option").attr("href",href+'&date_from='+dateFrom_+'&date_to='+dateTo_);
                    }
                    // discounts and prices
                    var returnHtml ='';
                    var discount = 0;
                    if(listPrice && priceForUser && myArray["currency"]){

                        returnHtml = returnHtml +  '<tr class="listPrice">';
                        returnHtml = returnHtml +  '<td>Charter Prices';
                        returnHtml = returnHtml +  '</td>';
                        returnHtml = returnHtml +  '<td>'+floatFormat(listPrice)+' <span class="cur">'+myArray["currency"]+'</span>';
                        returnHtml = returnHtml +  '</td>';
                        returnHtml = returnHtml +  '</tr>';
                        discount = parseFloat(listPrice)-parseFloat(priceForUser); ;
                    }
                    if(myArray['discounts'] && myArray['discounts'].length >0 && myArray["currency"]){
                        var returnString = '';
                        for(var index = 0; index < myArray["discounts"].length; index++) {
                            var discountItem = myArray["discounts"][index];
                            var amount = ' '+myArray["currency"]+' ';
                            if (discountItem.type == 'PERCENTAGE')
                                amount = ' % ';
                            returnString = returnString + discountItem.amount+amount;
                            if (index < (myArray["discounts"].length-1)) {
                                returnString = returnString + '+ ';
                            }
                        }
                        returnHtml = returnHtml +  '<tr class="discounts">';
                        returnHtml = returnHtml +  '<td>Discounts'+' ( '+returnString+')</td>';
                        returnHtml = returnHtml +  '<td> - '+floatFormat(discount) +' <span class="cur">'+myArray["currency"]+'</span></td></tr>';
                    } 
                    if(listPrice && priceForUser && myArray["currency"]){
                
                        returnHtml = returnHtml +  '<tr class="priceForUser">';
                        returnHtml = returnHtml +  '<td>Rental Price';
                        returnHtml = returnHtml +  '</td>';
                        returnHtml = returnHtml +  '<td>'+floatFormat(priceForUser) +' <span class="cur">'+myArray["currency"]+'</span>';
                        returnHtml = returnHtml +  '</td>';
                        returnHtml = returnHtml +  '</tr>';
                        returnHtml = returnHtml +  '<tr class="priceForUserTotal">';
                        returnHtml = returnHtml +  '<td>Rental Total';
                        returnHtml = returnHtml +  '</td>';
                        returnHtml = returnHtml +  '<td>'+format(priceForUser) +' <span class="cur">'+myArray["currency"]+'</span>';
                        returnHtml = returnHtml +  '</td>';
                        returnHtml = returnHtml +  '</tr>';
                    }

                    jQuery(".pricesAndDiscounts").html(returnHtml);

                    var table = jQuery(".property-table-right").first();
                    var trS   = table.find("tr");
                    trS.each(function(){
                        var classes = jQuery(this).attr('class');
                        if (classes && classes.indexOf('discount_') > -1){ 
                            var discountId = parseInt(classes.replace('discount_',''));
                            for (var index = 0; index < (myArray['discounts'].length + 1); index++){
                                if (index == myArray['discounts'].length){
                                    jQuery(this).hide();
                                    break;
                                } else if (myArray['discounts'][index]['discountItemId'] == discountId){
                                    jQuery(this).show();
                                    break;

                                }

                            } 
                        }

                    });
                } else {
                    $(".list-original-price-single").html('Foglalt');
                    $(".list-original-price").html('Foglalt');
                    $(".list-original-price").removeClass("del");
                    $(".list-discounted-price").html('');
                }
                jQuery(".loader").removeClass("waitMeTo_Container");
                jQuery('.waitContainer').css({'height':'0px'});
                $('.waitContainer').waitMe("hide"); 
            }
        });
    }
}
var dateFromTo = jQuery(".dateFromTo").html();
if (dateFromTo !== undefined){
    var dateFrom_   = dateFromTo.substring(0,11);
    var dateTo_    = dateFromTo.substring(14);
    var href = jQuery("a.button.make_option").first().attr("href"); console.log(href);
    if (href !== undefined && href.indexOf('&')>-1){ //alert(href);
        var index = href.indexOf("&")+1;
        if(href.indexOf("&", index)==-1)
            index = 0;
        console.log(href);
        href = href.substring(0,href.indexOf("&", index));
    }
    if (jQuery("a.button.make_option").first().attr("href") !== undefined){
        jQuery("a.button.make_option").attr("href",href+'&date_from='+dateFrom_+'&date_to='+dateTo_);
    }
}
if ( jQuery("#boats-list").html() === undefined ) {
    
    $("#duration").on('change', function(){ 
        thisboatrefresh();
    });

   
    $("#check_in").on('dateselected', function(){ 
        thisboatrefresh();
    });


}

