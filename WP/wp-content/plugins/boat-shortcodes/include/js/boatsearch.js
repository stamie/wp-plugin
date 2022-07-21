var x = jQuery("#boats-lists").html();
var dateFrom = $("#date_from").val();
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
    function boatsearch(){
        
        if (x !== undefined && x !== null) {
            var minLength = parseInt($('#length-handle1').html().replace('ft / ', '/').split('/')[0].trim());
            var maxLength = parseInt($('#length-handle2').html().replace('ft / ', '/').split('/')[0].trim());

            var feauteres = [];
            $(".features.selected").each(function () {
                feauteres.push($(this).parent().attr('id'));
            });

            if (!minLength ){
                minLength = 0;
            }
            if (!maxLength ){
                maxLength = 0;
            }
            
            minLength = minLength / 3.2808;
            maxLength = maxLength / 3.2808;

            var cabinSelector = $(".cabin-selector");
            var cabins = [];
            cabinSelector.each(function(){
                if ($(this).prop("checked")){
                    cabins.push($(this).val());
                }
            });
            var minBerths = parseInt($('#berths-handle1').html().split('/')[0].trim());
            var maxBerths = parseInt($('#berths-handle2').html().split('/')[0].trim());


            if (!minBerths ){
                minBerths = 0;
            } 
            if (!maxBerths ){
                maxBerths = 0;
            } 
            var maxDistance = parseInt($('#distance-handle2').html().replace(' km', '').trim());


            if (!maxDistance ){
                maxDistance = 0;
            } 
            
            var $categories = $('.category-selector');
            var allCategories = [];
            var selectedCategories = [];

            $categories.each(function() {
                allCategories.push($(this).val());
                if ($(this).prop('checked')==true){ 
                    selectedCategories.push($(this).val());
                }
            });

            if (selectedCategories.length == 0){
                selectedCategories = allCategories;
            }

            var $service_name = $('.service_name-selector');
            var allServiceNames = [];
            var selectedServiceNames = [];

            $service_name.each(function() {
                allServiceNames.push($(this).val());
                if ($(this).prop('checked')==true){ 
                    selectedServiceNames.push($(this).val());
                    if($(this).val()=='Skipper'){
                        selectedServiceNames.push('Sailor');
                    }
                } else if($(this).val()=='Skipper'){
                    var index = selectedServiceNames.indexOf('Sailor');
                    if (index > -1) {
                        selectedServiceNames.splice(index);
                    }
                }
            });
            
            var $service_types = $('.service_types-selector');
            var selectedServiceTypes = 0;
            $service_types.each(function() {
                if ($(this).prop('checked')==true){ 
                    selectedServiceTypes = $(this).val();
                }
            });
            if (selectedServiceNames.length == 0){
                selectedServiceNames = null;
            }
            var $destinations = $("#oldalso").children("option");
            var selectedDestionations = [];
            $destinations.each(function () {
                if ($(this).prop('selected')==true)
                    selectedDestionations.push(parseInt($(this).val())); 
            });
            
            var date_from = $("#date_from").val();
            
            
            var duration = parseInt($("#duration").val());
            var flexibility = (date_from!=undefined && date_from!='')?$("#flexibility").val():null;
            var desc = 0;
            var order_by = 2;
            var short_select = parseInt($('#sort_select').val());

            /*
            2 - price 3 - yacht length
            4 - yacht cabins 5 - yacht build year
            */
            switch (short_select) {
                case 1: // price
                    desc = 0;
                    order_by = 2;
                    break;
            
                case 2: // price
                    desc = 1;
                    order_by = 2;
                
                    break;
                case 3: // length
                    desc = 0;
                    order_by = 3;

                
                    break;
                case 4: // length
                    desc = 1;
                    order_by = 3;
                
                    break;
                case 5: // berths
                    desc = 0;
                    order_by = 6;
                    break;
            
                case 6: // berths
                    desc = 1;
                    order_by = 6;
                    
                    break;
            
                case 7: // Capacity
                    desc = 0;
                    order_by = 7;
                    break;
            
                case 8: // Capacity
                    desc = 1;
                    order_by = 7;
                    break;
            
                case 9: // cabins
                    desc = 0;
                    order_by = 4;
                    break;
            
                case 10: // cabins
                    desc = 1;
                    order_by = 4;

                case 11: // yacht builder year
                    desc = 0;
                    order_by = 5;
                    break;
            
                case 12: // yacht builder year
                    desc = 1;
                    order_by = 5;
                    break;
                default:
                    break;
            }
            
            var models = $('.models').first().val();
            var have_skipper = 0;
            if($('#skipper-selector').prop( "checked" ))
                have_skipper = 1;
            var is_sale = 0
            if($('#is_sale').prop( "checked" ))
                is_sale = 1;
            var ignoreOptions = 0;

            if($('#ignoreOptions').prop( "checked" ))
                ignoreOptions = 1;
            if (dateFrom == date_from){    
            
                $.ajax({
                    type: "POST",
                    url: "/wp-content/plugins/boat-shortcodes/include/ajaxGetBoatList.php",
                    // The key needs to match your method's input parameter (case-sensitive).
                    data: { 
                        'minLength': minLength, 
                        'maxLength': maxLength, 
                        'minBerth': minBerths, 
                        'maxBerth': maxBerths, 
                        //'minCabins': minCabins, 
                        //'maxCabins': maxCabins, 
                        'cabins': cabins, 
                        'maxDistance': maxDistance,
                        'ignoreOptions': ignoreOptions,
                        'dest_ids': selectedDestionations,
                        'selectedCategories' : selectedCategories,
                        'selectedServiceNames' : selectedServiceNames,
                        'selectedServiceTypes' : selectedServiceTypes,
                        'models' : models,
                        'have_skipper' : have_skipper,
                        'if_search' : 1,
                        'date_from'   : (date_from!=undefined && date_from!='')?date_from:null,
                        'duration'    : duration,
                        'flexibility' : flexibility,
                        'feauteres'   : feauteres,
                        'is_sale'     : is_sale,
                        'order_by'    : order_by,
                        'desc'        : desc
                        
                        },
                        beforeSend:function(){
                            jQuery(".loader").addClass("waitMeTo_Container");
                            jQuery('.waitContainer').css({'height':'50px'});
                            run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');
                        //  $("#count_of_boats").mouseenter();

                        },
                        success:function(data){
                        
                            $("#boats-lists").html(data);
                            $("#oldalso").select2('close');//$("#count_of_boats").mouseenter();
                            jQuery(".loader").removeClass("waitMeTo_Container");
                            jQuery('.waitContainer').css({'height':'0px'});
                            $('.waitContainer').waitMe("hide");
                            
                            load_info_bouble();

                            $(".fromto").each(function(){
                                if($(this).attr('attr-from')!==date_from){
                                    $(this).addClass('flex');
                                }
                            });
                                
                        }
                });
            }
        }
    }
    function boatsearchDateFrom(){
        //if (x !== undefined && x !== null) {
            var minLength = parseInt($('#length-handle1').html().replace('ft / ', '/').split('/')[0].trim());
            var maxLength = parseInt($('#length-handle2').html().replace('ft / ', '/').split('/')[0].trim());

            var feauteres = [];
            $(".features.selected").each(function () {
                feauteres.push($(this).parent().attr('id'));
            });

            if (!minLength ){
                minLength = 0;
            }
            if (!maxLength ){
                maxLength = 0;
            }
            
            minLength = minLength / 3.2808;
            maxLength = maxLength / 3.2808;

            var cabinSelector = $(".cabin-selector");
            var cabins = [];
            cabinSelector.each(function(){
                if ($(this).prop("checked")){
                    cabins.push($(this).val());
                }
            });
            var minBerths = parseInt($('#berths-handle1').html().split('/')[0].trim());
            var maxBerths = parseInt($('#berths-handle2').html().split('/')[0].trim());


            if (!minBerths ){
                minBerths = 0;
            } 
            if (!maxBerths ){
                maxBerths = 0;
            } 
            var maxDistance = parseInt($('#distance-handle2').html().replace(' km', '').trim());


            if (!maxDistance ){
                maxDistance = 0;
            } 
            
            var $categories = $('.category-selector');
            var allCategories = [];
            var selectedCategories = [];

            $categories.each(function() {
                allCategories.push($(this).val());
                if ($(this).prop('checked')==true){ 
                    selectedCategories.push($(this).val());
                }
            });

            if (selectedCategories.length == 0){
                selectedCategories = allCategories;
            }

            var $service_name = $('.service_name-selector');
            var allServiceNames = [];
            var selectedServiceNames = [];

            $service_name.each(function() {
                allServiceNames.push($(this).val());
                if ($(this).prop('checked')==true){ 
                    selectedServiceNames.push($(this).val());
                    if($(this).val()=='Skipper'){
                        selectedServiceNames.push('Sailor');
                    }
                } else if($(this).val()=='Skipper'){
                    var index = selectedServiceNames.indexOf('Sailor');
                    if (index > -1) {
                        selectedServiceNames.splice(index);
                    }
                }
            });
            
            var $service_types = $('.service_types-selector');
            var selectedServiceTypes = 0;
            $service_types.each(function() {
                if ($(this).prop('checked')==true){ 
                    selectedServiceTypes = $(this).val();
                }
            });
            if (selectedServiceNames.length == 0){
                selectedServiceNames = null;
            }
            var $destinations = $("#oldalso").children("option");
            var selectedDestionations = [];
            $destinations.each(function () {
                if ($(this).prop('selected')==true)
                    selectedDestionations.push(parseInt($(this).val())); 
            });
            
            var date_from = $("#date_from").val();
            
            var duration = parseInt($("#duration").val());
            var flexibility = (date_from!=undefined && date_from!='')?$("#flexibility").val():null;
            var desc = 0;
            var order_by = 2;
            var short_select = parseInt($('#sort_select').val());

            /*
            2 - price 3 - yacht length
            4 - yacht cabins 5 - yacht build year
            */
            switch (short_select) {
                case 1: // price
                    desc = 0;
                    order_by = 2;
                    break;
            
                case 2: // price
                    desc = 1;
                    order_by = 2;
                
                    break;
                case 3: // length
                    desc = 0;
                    order_by = 3;

                
                    break;
                case 4: // length
                    desc = 1;
                    order_by = 3;
                
                    break;
                case 5: // berths
                    desc = 0;
                    order_by = 6;
                    break;
            
                case 6: // berths
                    desc = 1;
                    order_by = 6;
                    
                    break;
            
                case 7: // Capacity
                    desc = 0;
                    order_by = 7;
                    break;
            
                case 8: // Capacity
                    desc = 1;
                    order_by = 7;
                    break;
            
                case 9: // cabins
                    desc = 0;
                    order_by = 4;
                    break;
            
                case 10: // cabins
                    desc = 1;
                    order_by = 4;

                case 11: // yacht builder year
                    desc = 0;
                    order_by = 5;
                    break;
            
                case 12: // yacht builder year
                    desc = 1;
                    order_by = 5;
                    break;
                default:
                    break;
            }
            
            var models = $('.models').first().val();
            var have_skipper = 0;
            var ignoreOptions = 0;

            if($('#skipper-selector').prop( "checked" ))
                have_skipper = 1;
            var is_sale = 0
            if($('#is_sale').prop( "checked" ))
                is_sale = 1;
            if($('#ignoreOptions').prop( "checked" ))
                ignoreOptions = 1;
                
            if (dateFrom != $("#date_from").val() && $("#date_from").val() != undefined && $("#date_from").val() != ''){    

                $.ajax({
                    type: "POST",
                    url: "/wp-content/plugins/boat-shortcodes/include/ajaxGetBoatList.php",
                    // The key needs to match your method's input parameter (case-sensitive).
                    data: { 
                        'minLength': minLength, 
                        'maxLength': maxLength, 
                        'minBerth': minBerths, 
                        'maxBerth': maxBerths, 
                        //'minCabins': minCabins, 
                        //'maxCabins': maxCabins, 
                        'cabins': cabins, 
                        'maxDistance': maxDistance,

                        'dest_ids': selectedDestionations,
                        'selectedCategories' : selectedCategories,
                        'selectedServiceNames' : selectedServiceNames,
                        'selectedServiceTypes' : selectedServiceTypes,
                        'models' : models,
                        'have_skipper' : have_skipper,
                        'if_search' : 1,
                        'date_from'   : (date_from!=undefined && date_from!='')?date_from:null,
                        'duration'    : duration,
                        'flexibility' : flexibility,
                        'feauteres'   : feauteres,
                        'is_sale'     : is_sale,
                        'order_by'    : order_by,
                        'desc'        : desc,
                        'ignoreOptions': 1
                        },
                        beforeSend:function(){
                            jQuery(".loader").addClass("waitMeTo_Container");
                            jQuery('.waitContainer').css({'height':'50px'});
                            run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');
                        //  $("#count_of_boats").mouseenter();

                        },
                        success:function(data){
                            
                            $("#boats-lists").html(data);
                            $("#oldalso").select2('close');//$("#count_of_boats").mouseenter();
                            jQuery(".loader").removeClass("waitMeTo_Container");
                            jQuery('.waitContainer').css({'height':'0px'});
                            //$('.waitContainer').waitMe("hide");
                            
                            load_info_bouble();

                            $(".fromto").each(function(){
                                if($(this).attr('attr-from')!==date_from){
                                    $(this).addClass('flex');
                                }
                            });
                                
                        }
                }).done(function () {
                    $('.waitContainer').waitMe("hide");
                });
                dateFrom = date_from;
            }
        //}
    }
        $('.boatsearch').on('click', function(){ 
            boatsearch();
        });
   
    $("#duration, .models, .service_name-selector, .service_types-selector").on('change', function(){ 
        boatsearch();
    });    

    $("#flexibility").on('change', function(){ 
        boatsearch();
    });
    $(".category-selector, .skipper-selector, .cabins, #is_sale, #ignoreOptions").on('click', function(){ 
        boatsearch();
    });

    $("#berths-search, #length-search, #distance-search").on('mouseup', function(){ 
        boatsearch();
    });
    
    $(".features").on('click', function () {
        if ($( this ).hasClass("selected")){
            $( this ).removeClass("selected");
        } else {
            $( this ).addClass("selected");
        }
        boatsearch();
    });

    $("#oldalso").on('change', function () {
        
       boatsearch(); 
    });
    function boatsearch_felso(){
        var categorie = $('.category-selector-felso').val();
        var allCategories = [];
        var selectedCategories = [];
        
        selectedCategories.push(categorie);

        if (selectedCategories.length == 0){
            selectedCategories = allCategories;
        }
        var $destinations = $("#felso").children("option");
        var selectedDestionations = [];
        $destinations.each(function () {
            if ($(this).prop('selected')==true)
                selectedDestionations.push($(this).val()); 
        });

        var date_from = $("#date_from_felso").val();
        
        var duration = (date_from && date_from!='')?$("#duration_felso").val():null;
        var flexibility = (date_from && date_from!='')?$("#flexibility_felso").val():null;

        var myArrayQry1 = selectedCategories.map(function(el, idx) {
            return '&selectedCategories[' + idx + ']=' + el;
        });
        
        
        var myArrayQry2 = selectedDestionations.map(function(el, idx) {
            return 'selectedDestionations[' + idx + ']=' + el;
        }).join('&');

        if (selectedDestionations.length == 0) {
            myArrayQry2 = '';
        } 
        var get = myArrayQry2 + myArrayQry1 + '&date_from=' + encodeURI(date_from) + '&duration=' + duration + '&flexibility=' + encodeURI(flexibility);
        if (get.charAt(0) == '&'){
            get = get.substring(1);
        }
        window.open('/boat-search?'+get);
 
    }

    $(".boatsearch-felso").on('click', function (params) {
        boatsearch_felso(); 
    });

    $('#sort_select').on('change', function (params) {
        boatsearch(); 
    });

    $('#date_from').on("dateselected", function(){ //alert("cica");
        boatsearchDateFrom();
    });
