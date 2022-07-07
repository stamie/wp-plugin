
function setSliderTicks(el) {
    var $slider =  $(el);
    var max =  $slider.slider("option", "max");    
    var min =  $slider.slider("option", "min");    
    var spacing =  100 / (max - min);

    $slider.find('.ui-slider-tick-mark').remove();
    for (var i = 0; i < max-min ; i++) {
        $('<span class="ui-slider-tick-mark"></span>').css('left', (spacing * i) +  '%').appendTo($slider); 
     }
}

function setSliderTicksOnLength(el) {
    var $slider =  $(el);
    var max =  $slider.slider("option", "max");    
    var min =  $slider.slider("option", "min");    
    var spacing =  100 / (max - min) * 2;

    $slider.find('.ui-slider-tick-mark').remove();
    for (var i = 0; i < ((max-min)/2) ; i++) {
        $('<span class="ui-slider-tick-mark"></span>').css('left', (spacing * i) +  '%').appendTo($slider); 
    }
}

function setSliderTicksDistance(el) {
    var $slider =  $(el);
    var max =  $slider.slider("option", "max");    
    var min =  $slider.slider("option", "min");    
    var spacing =  100 / (max - min)*5;

    $slider.find('.ui-slider-tick-mark').remove();
    for (var i = 0; i < ((max-min))/5 ; i++) {
        $('<span class="ui-slider-tick-mark"></span>').css('left', (spacing * i) +  '%').appendTo($slider); 
     }
}
$( function() {
    //Destination-tól való Távolság alap beállítása
    var handle = $( "#distance-handle2" );
    $( "#distance-search" ).slider({
      orientation: "horizontal",
      range: "min",
      max: 100,
      step: 5,
      value:  0,
      create: function( event, ui ) {
        setSliderTicksDistance(event.target);
        handle.html($(this).slider( "value") + ' km');
      },
      slide: function( event, ui ) {
        handle.html( ui.value + ' km');
      }
    });
    
});

//Kabin szám alap beállítása
$( function() {

    var max_value = cabinMax;
    var min_value = cabinMin;
    var handle1 = $( "#cabins-handle1" );
    var handle2 = $( "#cabins-handle2" );

            
    $( "#cabins-search" ).slider({
        range: true,
        min: min_value,
        max: max_value,
        step: 1,
        values: [ min_value, max_value ],
        create: function( event, ui ) {
            setSliderTicks(event.target);
            handle1.html($(this).slider( "values", 0 ));
            handle2.html($(this).slider( "values", 1 ));
        },
        slide: function( event, ui ) {
            handle1.html(ui.values[ 0 ]);
            handle2.html(ui.values[ 1 ]);
        }
    }); 

});

//Ágy szám alap beállítása
$( function() {
    var max_value = bedMax;
    var min_value = bedMin;
    var handle1 = $( "#berths-handle1" );
    var handle2 = $( "#berths-handle2" );

    var max = parseInt($('#berths-search').attr('attr-max'))==-1?max_value:parseInt($('#berths-search').attr('attr-max'));
    var min = min_value;
    if ($('#berths-search').attr('attr-min') && parseInt($('#berths-search').attr('attr-min'))>min_value)
        min = parseInt($('#berths-search').attr('attr-min'));
    min = min;

    $( "#berths-search" ).slider({
        range: true,
        min: min_value,
        max: max_value,
        step: 1,
        values: [ min, max ],
        create: function( event, ui ) {
            setSliderTicks(event.target);
            handle1.html($(this).slider( "values", 0 ));
            handle2.html($(this).slider( "values", 1 ));
        },
        slide: function( event, ui ) {
            handle1.html(ui.values[ 0 ]);
            handle2.html(ui.values[ 1 ]);
        }
    });
});


//Hajóhossz alap beállítása
jQuery( function() {
  
    var max_value = Math.round(loaMax * 3.2808);
    var min_value = Math.round(loaMin * 3.2808);
    var handle1 = jQuery( "#length-handle1" );
    var handle2 = jQuery( "#length-handle2" );
    
    var max = max_value;
    if ($('#length-search').attr('attr-max') && parseInt($('#length-search').attr('attr-max')) * 3.2808 <max)
        max = parseInt($('#length-search').attr('attr-max')) * 3.2808;
    max = Math.round(max);
    if (max < 0)
        max = max_value;

    
    var min = min_value;
    if ($('#length-search').attr('attr-min') && parseInt($('#length-search').attr('attr-min')) * 3.2808>min)
   
        min = parseInt($('#length-search').attr('attr-min')) * 3.2808;
    min = Math.round(min);
   
    jQuery( "#length-search" ).slider({
        range: true,
        min: min_value,
        max: max_value,
        step: 1,
        values: [ min, max ],
        create: function( event, ui ) {
            setSliderTicksOnLength(event.target);
            handle1.html(jQuery(this).slider( "values", 0 ) + ' ft / ' + Math.round(jQuery(this).slider( "values", 0 )/3.2808) + ' m');
            handle2.html(jQuery(this).slider( "values", 1 ) + ' ft / ' + Math.round(jQuery(this).slider( "values", 1 )/3.2808) + ' m');
        },
        slide: function( event, ui ) {
            handle1.html(ui.values[ 0 ] + ' ft / ' + Math.round(ui.values[ 0 ]/3.2808) + ' m');
            handle2.html(ui.values[ 1 ] + ' ft / ' + Math.round(ui.values[ 1 ]/3.2808) + ' m');
        }
    });
});

//Types beállítása 
jQuery( function() {
    if (categories && Array.isArray(categories)){
        var iterator = categories.keys();

        for(var key of iterator){
            jQuery(".category-label").each(function(){
                var label = jQuery(this).children("span").first().html();
                var checkbox = jQuery(this).parents("span").first().children("input").first();
console.log(label);
                if (label == categories[key]){
                    jQuery(this).addClass("checked");
                    checkbox.prop('checked', true);
                } else {
                    jQuery(this).removeClass("checked");
                    checkbox.prop('checked', false);
                }


            });
        }

    }



});
