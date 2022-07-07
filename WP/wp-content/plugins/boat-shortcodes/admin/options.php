<style>
    <?php include __DIR__ . '/admin.css' ?>
</style>

<div class="wrap">

    <h1>Boat Shortcodes</h1> 

<h2 id="nav-tab-wrapper" class="wp-tab-bar nav-tab-wrapper">
	<a href="#tabs-1" id="general-tab" class="nav-tab">Elérhető shortcode-ok</a>
	<a href="#tabs-2" id="general-tab" class="nav-tab">Shortcode minták</a>
	<!--a href="#tabs-3" id="general-tab" class="nav-tab">Extrák</a-->
</h2>

<div class="wp-tab-panel shortcode" id="tabs-1">
  <h2>Elérhető shortcode-ok</h2>
<div class="alert"><h4 style="font-size:120%;">Az itt megjelenő azonosítók(id) nem a WP boat Id(Boats menüpontban kilistázott hajók WP azonosítója), hanem az adatbázisban szereplő Yacht id-t jelenti!</h4><br /><b>Kattintás után a shortcode vágólapra kerül.</b></div>
<hr />
<div style="width:49%; float:left; border-right: 1px solid #eee;">
<div style="padding: 10px; background: #C4FFE9;">
<h3 style="text-decoration: underline;">Hajó lista desztináció végpont gyorskód<h3>
<p><span id="pwd_spn" class="copy"><code>[boats-list dest-id="7595"]</code></span> <span class="dashicons dashicons-info"></span> Hajó kereső a desztináció végponton. Az ID a cikk saját ID azonosítója!</p>
<p><span id="pwd_spn" class="copy"><code>[boats-list-felso]</code></span> <span class="dashicons dashicons-info"></span> Hajó felső kereső, és részletes kereső. </p>
</div>
<h3 style="text-decoration: underline;">Hajó lista egyedi gyorskódok<h3>
<p><span id="pwd_spn" class="copy"><code>[boat-title id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajó neve gyártási évszámmal együtt</p>
<p><span id="pwd_spn" class="copy"><code>[boat-price id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó foglalási ár</p>
<p><span id="pwd_spn" class="copy"><code>[boat-pictures id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó képgaléria</p>
<p><span id="pwd_spn" class="copy"><code>[boat-property-table id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó alap adatok lista (property tábla)</p>
<p><span id="pwd_spn" class="copy"><code>[boat-on-the-map id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajó megjelenítése térképen</p>
<p><span id="pwd_spn" class="copy"><code>[boat-equipmentlist id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó felszerelési lista (Equipments tábla)</p>
<p><span id="pwd_spn" class="copy"><code>[boat-charterprices id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó kiegészítő árak táblája (Included in the Price, Obligatory, Optional)</p>
<p><span id="pwd_spn" class="copy"><code>[boat-beds id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó ágyak száma <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-rooms id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó kabinok száma <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-persons id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó személyek száma <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-deep id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó merülési mélység adat <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-width id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó hosszúsági adat <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-general id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó motor teljesítmény <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-equipments id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó ??? <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-page id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó link <span class="ikon">
href="/boat/athena-38-2003"</span></p>
<p><span id="pwd_spn" class="copy"><code>[boat-prices-table id="2"]</code></span> <span class="dashicons dashicons-info"></span> Hajóhoz tartozó árak és kedvezmények listája</p>
<hr />
<h3 style="text-decoration: underline;">Opcio E-mail gyorskódok<h3>
<p><span id="pwd_spn" class="copy"><code>[first-name]</code></span> <span class="dashicons dashicons-info"></span> Első név</p>
<p><span id="pwd_spn" class="copy"><code>[last-name]</code></span> <span class="dashicons dashicons-info"></span> Második név</p>
<p><span id="pwd_spn" class="copy"><code>[listaar]</code></span> <span class="dashicons dashicons-info"></span> Lista ár</p>
<p><span id="pwd_spn" class="copy"><code>[vegsoar]</code></span> <span class="dashicons dashicons-info"></span> Kedvezményes ár</p>
<p><span id="pwd_spn" class="copy"><code>[kezdo-datum]</code></span> <span class="dashicons dashicons-info"></span> Kezdő dátum</p>
<p><span id="pwd_spn" class="copy"><code>[veg-datum]</code></span> <span class="dashicons dashicons-info"></span> Vég dátum</p>
<p><span id="pwd_spn" class="copy"><code>[statusz]</code></span> <span class="dashicons dashicons-info"></span> Rendelés státusza</p>
<p><span id="pwd_spn" class="copy"><code>[bekuldese]</code></span> <span class="dashicons dashicons-info"></span> Beküldési dátum</p>
<p><span id="pwd_spn" class="copy"><code>[rendeles-szama]</code></span> <span class="dashicons dashicons-info"></span> Rendelés szám</p>
<p><span id="pwd_spn" class="copy"><code>[url][hajonev][/url]</code></span> <span class="dashicons dashicons-info"></span> Link az opciózott hajóhoz</p>



</div>

<div style="width:49%; float:right;">
<h3 style="text-decoration: underline;">Felső kereső gyorskódok</h3>
<p><span id="pwd_spn" class="copy"><code>[destinations-felso]</code></span> <span class="dashicons dashicons-info"></span> Destináció választó mező</p>
<p><span id="pwd_spn" class="copy"><code>[boat-date-from-felso]</code></span> <span class="dashicons dashicons-info"></span> Kezdő dátum választó mező</p>
<p><span id="pwd_spn" class="copy"><code>[boat-duration-felso]</code></span> <span class="dashicons dashicons-info"></span> Napok számának kiválasztása (duration)</p>
<p><span id="pwd_spn" class="copy"><code>[boat-flexibility-felso]</code></span> <span class="dashicons dashicons-info"></span> A kiválasztott dátumhoz tartozó flexibilitás választása</p>
<p><span id="pwd_spn" class="copy"><code>[boat-type-search-felso]</code></span> <span class="dashicons dashicons-info"></span> Hajó típus választó</p>
<hr />
<h3 style="text-decoration: underline;">Oldalsó kereső gyorskódok</h3>
<p><span id="pwd_spn" class="copy2"><code>[destination-modification dest-id="dest_id"]</code></span> <span class="dashicons dashicons-info"></span> Destináció választó mező</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-distance-search]</code></span> <span class="dashicons dashicons-info"></span> Hajó keresés 100 km körzetben</p>
<p><span id="pwd_spn" class="copy2"><code>[is_sale]</code></span> <span class="dashicons dashicons-info"></span> Akciós hajók listázása</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-date-from]</code></span> <span class="dashicons dashicons-info"></span> Kezdő dátum választó mező</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-duration]</code></span> <span class="dashicons dashicons-info"></span> Napok számának kiválasztása (duration)</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-flexibility]</code></span> <span class="dashicons dashicons-info"></span> A kiválasztott dátumhoz tartozó flexibilitás választása</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-cabins-search dest-id="dest_id"]</code></span> <span class="dashicons dashicons-info"></span> Hajó kabin szám szűrő</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-berths-search dest-id="dest_id"]</code></span> <span class="dashicons dashicons-info"></span> Hajó ágy szám szűrő</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-type-search dest-id="dest_id"]</code></span> <span class="dashicons dashicons-info"></span> Hajó típus választó</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-service-type-search]</code></span> <span class="dashicons dashicons-info"></span> Hajó szolgáltatás választó (Bareboat,Crewed stb...)</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-length-search dest-id="dest_id"]</code></span> <span class="dashicons dashicons-info"></span> Hajó hosszúság szűrő</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-feauters-search]</code></span> <span class="dashicons dashicons-info"></span> Hajó kiemelt extrák szűrő <span class="ikon">(IKON)</span></p>
<p><span id="pwd_spn" class="copy2"><code>[boat-length-search dest-id="dest_id"]</code></span> <span class="dashicons dashicons-info"></span> Hajó hosszúság szűrő</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-model-search]</code></span> <span class="dashicons dashicons-info"></span> Hajó modell választó</p>
<p><span id="pwd_spn" class="copy2"><code>[boat-service-type-equipment]</code></span> <span class="dashicons dashicons-info"></span> Hajó további választható extrák</p>



</div>

</div>


<div class="wp-tab-panel shortcode" id="tabs-2" style="display: none;">
  <h2>Shortcode minták</h2>
</div>

<!--div class="wp-tab-panel shortcode" id="tabs-3" style="display: none;">
  <h2>Extrák</h2>
</div -->

	
	
<script>
jQuery(document).ready( function($) {
	$('.wp-tab-bar a').click(function(event){
		event.preventDefault();
		
		// Limit effect to the container element.
		var context = $(this).closest('.wp-tab-bar').parent();
		$('.wp-tab-bar a', context).removeClass('nav-tab-active');
		$(this).closest('a').addClass('nav-tab-active');
		$('.wp-tab-panel', context).hide();
		$( $(this).attr('href'), context ).show();
	});

	// Make setting wp-tab-active optional.
	$('.wp-tab-bar').each(function(){
		if ( $('.nav-tab-active', this).length )
			$('.nav-tab-active', this).click();
		else
			$('a', this).first().click();
	});
});


var copy = document.querySelectorAll(".copy"); 
for (const copied of copy) { 
  copied.onclick = function() { 
    document.execCommand("copy"); 
  };  
  copied.addEventListener("copy", function(event) { 
    event.preventDefault(); 
    if (event.clipboardData) { 
      event.clipboardData.setData("text/plain", copied.textContent);
      console.log(event.clipboardData.getData("text"))
    };
  });
};
</script>
</div>
