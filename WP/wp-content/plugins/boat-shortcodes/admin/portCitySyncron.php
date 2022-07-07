<style>
    <?php

    use function PHPSTORM_META\type;
    use function YoastSEO_Vendor\GuzzleHttp\json_decode;

    include __DIR__ . '/admin.css';
    ?>
</style>
<script src="/wp-content/plugins/boat-shortcodes/admin/js/newDatas.js" type="text/javascript"></script>
<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/destCitySyncSelector.php';
global $wpdb;
$query = "SELECT * FROM xml";
$tabs = $wpdb->get_results($query, OBJECT);
$query = "SELECT id FROM user WHERE username like 'wpuser'";
$id = $wpdb->get_results($query, OBJECT);
// var_dump($id); exit;
$id = isset($id[0]) ? $id[0]->id : null;
?>


<div class="wrap">

    <h2>Boat Shortcodes</h2>

    <div>
        <div class="tabs">
            <?php if (is_array($tabs)) : ?>

                <?php $active = 0; ?>
                <?php foreach ($tabs as $tab) : ?>
                    <?php
                    $returnDiscount = selectNewDiscounts($tab->id);
                    if ($returnDiscount != '')
                        $returnDiscount = '<span class="red discountItem"> </span>';
                    $returnBoatType = selectNewYachCategories($tab->id);
                    if ($returnBoatType != '')
                        $returnBoatType = '<span class="red boatType"> </span>';
                    ?>
                    <div class="tab_header<?= (++$active == 1) ? ' active' : ' inactive'; ?>">
                        <?= $tab->class_name ?>
                    </div>
                <?php endforeach; ?>
                <?php $active = 0; ?>
                <?php foreach ($tabs as $tab) : ?>
                    <div class="tab_body<?= (++$active == 1) ? ' active' : ' hidden'; ?>">
                        <ul>
                            <li class="tab activate" attr-tab="1" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= __('WP kikötők szinkron', 'boat-shortcodes') ?></a></li>
                            <li class="tab" attr-tab="2" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= __('XML csat. kikötők - város párosítás', 'boat-shortcodes') ?></a></li>
                            <li class="tab" attr-tab="3" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= __('Dest.-ök és városok párosítása', 'boat-shortcodes') ?></a></li>
                            <li class="tab" attr-tab="4" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= $returnBoatType ?> <?= __('Dest.-ök és hajótípusok párosítása', 'boat-shortcodes') ?></a></li>
                            <li class="tab" attr-tab="5" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= $returnDiscount ?> <?= __('Dest.-ök és akciók párosítása', 'boat-shortcodes') ?></a></li>
                            <li class="tab" attr-tab="6" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= __('Dest.-ök és hajóhossz adatok megadása', 'boat-shortcodes') ?></a></li>
                            <li class="tab" attr-tab="7" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= __('Dest.-ök és ágyak megadása', 'boat-shortcodes') ?></a></li>
                            <li class="tab" attr-tab="8" attr="<?= $tab->class_name ?>"><a href="#" attr="<?= $tab->class_name ?>"><?= __('Dest.-ök és szolgáltatások', 'boat-shortcodes') ?></a></li>
                        </ul>
                        <div class="tables 1 <?= $tab->class_name ?> active">
                            <h1><?= __('WP kikötők szinkron', 'boat-shortcodes') ?></h1>
                            <button type="button" class="wp-sync-control" attr="<?= $tab->class_name ?>">WP Kikötők Ellenőrzése</button>

                            <div class="wp-sync <?= $tab->class_name ?>">

                                <div class="acordion-panel">
                                    <button class="help accordion"> </button>
                                    <div class="panel">
                                        <div class="helper-background">
                                            <h1><span>WP kik&ouml;t&#337;k szinkron</span></h1>
                                            <ol start="1">
                                                <li><span>A WordPress -ben l&eacute;trehozott kik&ouml;t&#337;h&ouml;z hozz&aacute; lett p&aacute;ros&iacute;tva az XML csatorna kik&ouml;t&#337;je.</span></li>
                                            </ol>
                                            <p class="c8"><span>Az <b>&ouml;sszek&ouml;t&eacute;st</b><span>&nbsp;a kik&ouml;t&#337;k oszlop </span><span>feh&eacute;r</span><span>&nbsp;h&aacute;ttere &eacute;s a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span>z&ouml;ld</span><span>&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 593.00px; height: 31.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image7.png" style="width: 593.00px; height: 31.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></p>
                                            <p class="c8 c10"></p>
                                            <ol class="c3 lst-kix_list_1-0" start="2">
                                                <li><span>A WordPress -ben l&eacute;trehozott kik&ouml;t&#337;h&ouml;z a rendszer aj&aacute;nl kik&ouml;t&#337;t, de m&eacute;g nem lett j&oacute;v&aacute;hagyva az &ouml;sszek&ouml;t&eacute;s az XML csatorna kik&ouml;t&#337;j&eacute;vel.</span></li>
                                            </ol>
                                            <p class="c11"><span>A </span><b>nem &ouml;sszek&ouml;t&eacute;st</b><span>&nbsp;a kik&ouml;t&#337;k oszlop </span><span>z&ouml;ld</span><span>&nbsp;h&aacute;ttere &eacute;s a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span>sz&uuml;rke</span><span>&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 592.00px; height: 30.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image9.png" style="width: 592.00px; height: 30.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></p>
                                            <ol class="c3 lst-kix_list_1-0" start="3">
                                                <li><span>A WordPress -ben l&eacute;trehozott kik&ouml;t&#337;h&ouml;z a rendszer nem tudott aj&aacute;nlani kik&ouml;t&#337;t, &eacute;s nem lett j&oacute;v&aacute;hagyva az &ouml;sszek&ouml;t&eacute;s az XML csatorna kik&ouml;t&#337;j&eacute;vel sem.</span></li>
                                            </ol>
                                            <p class="c11"><span>A </span><b>nem &ouml;sszek&ouml;t&eacute;st</b><span>&nbsp;a kik&ouml;t&#337;k oszlop </span><span>piros</span><span>&nbsp;h&aacute;ttere &eacute;s a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span>sz&uuml;rke</span><span>&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 592.00px; height: 31.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image8.png" style="width: 592.00px; height: 31.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></p>
                                            <p class="c13"><span>&nbsp;</span></p>
                                            <hr style="page-break-before:always;display:none;">

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tables 2 <?= $tab->class_name ?> hidden">
                            <h1><?= __('XML csat. kikötők - város párosítás', 'boat-shortcodes') ?></h1>
                            <button type="button" class="xml-sync-control" attr="<?= $tab->class_name ?>">XML csat. kikötők - város párosítás</button>

                            <div class="xml-sync <?= $tab->class_name ?>">

                                <div class="acordion-panel">
                                    <button class="help accordion"> </button>
                                    <div class="panel">
                                        <div class="helper-background">
                                            <h1><span>XML csat. kik&ouml;t&#337;k - v&aacute;ros p&aacute;ros&iacute;t&aacute;s </span></h1>
                                            <ol start="1">
                                                <li><span>XML csatorn&aacute;b&oacute;l bet&ouml;lt&ouml;tt kik&ouml;t&#337; neve. Saj&aacute;t linkkel van ell&aacute;tva ami &aacute;tvisz a Google t&eacute;rk&eacute;pen a poz&iacute;ci&oacute;j&aacute;ra. Megmutatja melyik v&aacute;rosban tal&aacute;lhat&oacute; &eacute;s mi a v&aacute;ros sz&eacute;less&eacute;gi &eacute;s hossz&uacute;s&aacute;gi foka.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 126.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image11.png" style="width: 126.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></li>
                                                <li><span>A XML csatorn&aacute;b&oacute;l bet&ouml;lt&ouml;tt kik&ouml;t&#337;h&ouml;z hozz&aacute; lett p&aacute;ros&iacute;tva megfelel&#337; v&aacute;ros (&bdquo;V&aacute;ros lista&rdquo; oszlop).<br>Az <b>&ouml;sszek&ouml;t&eacute;st</b><span>&nbsp;a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span>z&ouml;ld</span><span>&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 263.00px; height: 28.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image10.png" style="width: 263.00px; height: 28.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></li>
                                                <li><span>A XML csatorn&aacute;b&oacute;l bet&ouml;lt&ouml;tt kik&ouml;t&#337;h&ouml;z nem lett m&eacute;g hozz&aacute; p&aacute;ros&iacute;tva megfelel&#337; v&aacute;ros (&bdquo;V&aacute;ros lista&rdquo; oszlop).<br>Az </span><span>nem</span><span>&nbsp;</span><span>&ouml;sszek&ouml;t&eacute;st</span><span>&nbsp;a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span>sz&uuml;rke</span><span>&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 262.00px; height: 29.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image13.png" style="width: 262.00px; height: 29.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></li>
                                                <li><span>A kiv&aacute;lasztott kik&ouml;t&#337; </span><span>hozz&aacute;rendel&eacute;se</span><span>&nbsp;ut&aacute;ni ment&eacute;s.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 97.00px; height: 30.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image12.png" style="width: 97.00px; height: 30.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></li>
                                                <li><span>Ha nincs benne a v&aacute;ros a kiv&aacute;laszthat&oacute; list&aacute;ban, akkor azt az &bdquo;&Uacute;j v&aacute;ros hozz&aacute;rendel&eacute;se&rdquo; gombbal lehet felvinni.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 169.00px; height: 33.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image15.png" style="width: 169.00px; height: 33.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></li>
                                                <li><span>Az &bdquo;&Uacute;j v&aacute;ros hozz&aacute;rendel&eacute;se&rdquo; r&eacute;szleteiben meg kell adni a v&aacute;ros nev&eacute;t, sz&eacute;less&eacute;gi &eacute;s hossz&uacute;s&aacute;gi fokait. <br></span><span class="c9">(A kik&ouml;t&#337; linkj&eacute;n el&eacute;rhet&#337; a Google t&eacute;rk&eacute;pen.)<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 506.00px; height: 33.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image14.png" style="width: 506.00px; height: 33.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"></span></li>
                                            </ol>
                                            <p class="c8 c10"></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="tables 3 <?= $tab->class_name ?> hidden">
                            <h1><?= __('Dest.-ök és városok párosítása', 'boat-shortcodes') ?></h1>
                            <button type="button" class="dest-sync-control" attr="<?= $tab->class_name ?>">Dest.-ök és városok újratöltése</button>


                            <div class="dest-sync select <?= $tab->class_name ?>">
                                <div class="acordion-panel">
                                    <button class="help accordion"> </button>
                                    <div class="panel">
                                        <div class="helper-background">
                                            <h1><span>Dest.-ök &eacute;s v&aacute;rosok p&aacute;ros&iacute;t&aacute;sa </span></h1>
                                            <p><span></span></p>
                                            <ol start="1">
                                                <li><span>A desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz m&eacute;g nem lett(ek) kiv&aacute;lasztva v&aacute;ros(ok)<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 163.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image18.png" style="width: 163.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz lett(ek) kiv&aacute;lasztva v&aacute;ros(ok)<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 83.00px; height: 16.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image16.png" style="width: 83.00px; height: 16.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>V&aacute;ros(ok) hozz&aacute;ad&aacute;sa a desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 21.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image17.png" style="width: 21.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>V&aacute;ros kiv&aacute;laszt&aacute;sa a desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 263.00px; height: 51.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image19.png" style="width: 263.00px; height: 51.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li class="c7 li-bullet-0"><span>V&aacute;ros(ok) t&ouml;rl&eacute;se a desztin&aacute;ci&oacute; v&eacute;gpontr&oacute;l<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 21.00px; height: 22.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image20.png" style="width: 21.00px; height: 22.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <hr style="page-break-before:always;display:none;">
                                            <p><span></span></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="tables 4 <?= $tab->class_name ?> hidden">
                            <h1><?= __('Dest.-ök és hajótípusok párosítása', 'boat-shortcodes') ?></h1>
                            <button type="button" id="dest-boatType-sync-control" class="dest-boatType-sync-control" attr="<?= $tab->class_name ?>">Dest.-ök és hajótípusok újratöltése</button>


                            <div class="dest-boatType-sync select <?= $tab->class_name ?>">
                                <div class="acordion-panel">
                                    <button class="help accordion help-dest-boatType"> </button>
                                    <?php $return = selectNewYachCategories($tab->id);
                                    if ($return != '') :
                                    ?>
                                        <button class="new accordion new-dest-boatType"> </button>
                                    <?php else : ?>
                                        <button class="new accordion new-dest-boatType" style="display: none;"> </button>
                                    <?php endif; ?>
                                    <div class="panel">
                                        <div class="helper-background">
                                            <h1><span>Dest.-ök &eacute;s haj&oacute;t&iacute;pusok p&aacute;ros&iacute;t&aacute;sa</span></h1>
                                            <p><span></span></p>
                                            <ol start="1">
                                                <li><span>Haj&oacute;t&iacute;pus kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz t&ouml;megesen.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 24.00px; height: 25.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image5.png" style="width: 24.00px; height: 25.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <p class="c8 c10"><span></span></p>
                                            <ol class="c3 lst-kix_list_3-0" start="2">
                                                <li><span>Haj&oacute;t&iacute;pus(ok) kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a felugr&oacute; ablakban.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 377.00px; height: 252.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image21.png" style="width: 377.00px; height: 252.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz az &ouml;sszes haj&oacute;t&iacute;pus hozz&aacute; van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 17.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image2.png" style="width: 17.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute; egyik alkateg&oacute;ri&aacute;j&aacute;hoz egyedi haj&oacute;t&iacute;pus lista van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 15.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image3.png" style="width: 15.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li class="c7 li-bullet-0"><span>A desztin&aacute;ci&oacute;hoz egyedi haj&oacute;t&iacute;pus lista van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 16.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image4.png" style="width: 16.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <hr style="page-break-before:always;display:none;">
                                            <p><span></span></p>
                                        </div>
                                    </div>
                                    <div class="panel">
                                        <div class="helper-background boatType">
                                            <?= $return ?>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>


                        <div class="tables 5 <?= $tab->class_name ?> hidden">
                            <h1><?= __('Dest.-ök és akciók párosítása', 'boat-shortcodes') ?></h1>
                            <button type="button" class="dest-discounts-sync-control" id="dest-discounts-sync-control" attr="<?= $tab->class_name ?>">Dest.-ök és akciók újratöltése</button>


                            <div class="dest-discounts-sync select <?= $tab->class_name ?>">
                                <div class="acordion-panel">

                                    <button class="help-accordion help accordion help-dest-discountItem"> </button>
                                    <?php $return = selectNewDiscounts($tab->id);

                                    if ($return != '') :
                                    ?>
                                        <button class="new-accordion new accordion new-dest-discountItem"> </button>
                                    <?php else : ?>
                                        <button class="new-accordion new accordion new-dest-discountItem" style="display: none;"> </button>
                                    <?php endif; ?>
                                    <div class="panel help">
                                        <div class="helper-background">
                                            <h1><span>Dest.-ök &eacute;s akci&oacute;k p&aacute;ros&iacute;t&aacute;sa</span></h1>
                                            <p><span></span></p>
                                            <ol start="1">
                                                <li><span>Kedvezm&eacute;nyek kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz t&ouml;megesen.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 24.00px; height: 25.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image5.png" style="width: 24.00px; height: 25.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <p class="c8 c10"><span></span></p>
                                            <ol class="c3 lst-kix_list_4-0" start="2">
                                                <li><span>Kedvezm&eacute;ny(ek) kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a felugr&oacute; ablakban.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 382.00px; height: 248.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image1.png" style="width: 382.00px; height: 248.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz az &ouml;sszes kedvezm&eacute;ny hozz&aacute; van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 17.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image2.png" style="width: 17.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute; egyik alkateg&oacute;ri&aacute;j&aacute;hoz egyedi kedvezm&eacute;ny lista van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 15.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image3.png" style="width: 15.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li class="c7 li-bullet-0"><span>A desztin&aacute;ci&oacute;hoz egyedi kedvezm&eacute;ny lista van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 16.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image4.png" style="width: 16.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <hr style="page-break-before:always;display:none;">
                                            <p><span></span></p>
                                        </div>
                                    </div>



                                    <div class="panel new">
                                        <div class="helper-background discountItem">
                                            <?= $return ?>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>


                        <div class="tables 6 <?= $tab->class_name ?> hidden">
                            <h1><?= __('Dest.-ök és hajóhossz adatok megadása', 'boat-shortcodes') ?></h1>
                            <button type="button" id='dest-length-sync-control' class="dest-length-sync-control" attr="<?= $tab->class_name ?>">Dest.-ök és hajóhossz-ok újratöltése</button>


                            <div class="dest-length-sync select <?= $tab->class_name ?>">
                                <div class="acordion-panel">
                                    <button class="help accordion"> </button>
                                    <div class="panel">
                                        <div class="helper-background">
                                            <h1><span>Dest.-ök &eacute;s haj&oacute;hossz adatok megad&aacute;sa</span></h1>
                                            <p><span></span></p>
                                            <ol start="1">
                                                <li><span>Haj&oacute;hossz kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz t&ouml;megesen.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 24.00px; height: 25.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image5.png" style="width: 24.00px; height: 25.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <p class="c8 c10"><span></span></p>
                                            <ol class="c3 lst-kix_list_5-0" start="2">
                                                <li><span>Haj&oacute;hossz(ok) kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a felugr&oacute; ablakban.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 484.00px; height: 29.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image6.png" style="width: 484.00px; height: 29.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz az &ouml;sszes haj&oacute;hossz &eacute;rt&eacute;k (minimumt&oacute;l a maximumig) hozz&aacute; van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 17.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image2.png" style="width: 17.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute; egyik alkateg&oacute;ri&aacute;j&aacute;hoz egyedi haj&oacute;hossz &eacute;rt&eacute;k van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 15.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image3.png" style="width: 15.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li class="c7 li-bullet-0"><span>A desztin&aacute;ci&oacute;hoz egyedi haj&oacute;hossz &eacute;rt&eacute;k van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 16.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image4.png" style="width: 16.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <hr style="page-break-before:always;display:none;">
                                            <p><span></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tables 7 <?= $tab->class_name ?> hidden">
                            <h1><?= __('Dest.-ök és ágyak megadása', 'boat-shortcodes') ?></h1>
                            <button type="button" id='dest-berth-sync-control' class="dest-berth-sync-control" attr="<?= $tab->class_name ?>">Dest.-ök és ágyak újratöltése</button>
                            <div class="dest-berth-sync select <?= $tab->class_name ?>">
                                <div class="acordion-panel">
                                    <button class="help accordion"> </button>
                                    <div class="panel">
                                        <div class="helper-background">
                                            <h1><span>Dest.-ök &eacute;s &aacute;gyak megad&aacute;sa</span></h1>
                                            <p><span></span></p>
                                            <ol start="1">
                                                <li><span>&Aacute;gyak kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz t&ouml;megesen.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 24.00px; height: 25.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image5.png" style="width: 24.00px; height: 25.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <p class="c8 c10"><span></span></p>
                                            <ol class="c3 lst-kix_list_6-0" start="2">
                                                <li><span>&Aacute;gyak kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a felugr&oacute; ablakban.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 484.00px; height: 29.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image6.png" style="width: 484.00px; height: 29.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz az &ouml;sszes &aacute;gysz&aacute;m &eacute;rt&eacute;k (minimumt&oacute;l a maximumig) hozz&aacute; van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 17.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image2.png" style="width: 17.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute; egyik alkateg&oacute;ri&aacute;j&aacute;hoz egyedi &aacute;gysz&aacute;m &eacute;rt&eacute;k van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 15.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image3.png" style="width: 15.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li class="c7 li-bullet-0"><span>A desztin&aacute;ci&oacute;hoz egyedi &aacute;gysz&aacute;m &eacute;rt&eacute;k van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 16.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image4.png" style="width: 16.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <p><span></span></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="tables 8 <?= $tab->class_name ?> hidden">
                            <h1><?= __('Dest.-ök és szolgáltatások', 'boat-shortcodes') ?></h1>
                            <button type="button" id='dest-service-types-control' class="dest-service-types-control" attr="<?= $tab->class_name ?>">Dest.-ök és szolgáltatások újratöltése</button>
                            <div class="dest-service-types select <?= $tab->class_name ?>">
                                <div class="acordion-panel">
                                    <button class="help accordion"> </button>
                                    <div class="panel">
                                        <div class="helper-background">
                                            <h1><span>Dest.-ök &eacute;s &aacute;gyak megad&aacute;sa</span></h1>
                                            <p><span></span></p>
                                            <ol start="1">
                                                <li><span>&Aacute;gyak kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz t&ouml;megesen.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 24.00px; height: 25.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image5.png" style="width: 24.00px; height: 25.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <p class="c8 c10"><span></span></p>
                                            <ol class="c3 lst-kix_list_6-0" start="2">
                                                <li><span>&Aacute;gyak kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a felugr&oacute; ablakban.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 484.00px; height: 29.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image6.png" style="width: 484.00px; height: 29.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz az &ouml;sszes &aacute;gysz&aacute;m &eacute;rt&eacute;k (minimumt&oacute;l a maximumig) hozz&aacute; van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 17.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image2.png" style="width: 17.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li><span>A desztin&aacute;ci&oacute; egyik alkateg&oacute;ri&aacute;j&aacute;hoz egyedi &aacute;gysz&aacute;m &eacute;rt&eacute;k van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 15.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image3.png" style="width: 15.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                                <li class="c7 li-bullet-0"><span>A desztin&aacute;ci&oacute;hoz egyedi &aacute;gysz&aacute;m &eacute;rt&eacute;k van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 16.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image4.png" style="width: 16.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                                            </ol>
                                            <p><span></span></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php

    //Itt listázzuk ki azt, hogy melyek azok a portok, melyek benne vannak az xml_csatban, de nincsenek benne a ports_i_cities táblában.


    ?>

</div>
<script>
    var acc = document.getElementsByClassName("new-dest-discountItem");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");

            this.nextElementSibling.style.maxHeight = null;
            var panel = this.nextElementSibling.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
    var acc = document.getElementsByClassName("help-dest-discountItem");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");

            var panel = this.nextElementSibling.nextElementSibling;
            panel.nextElementSibling.style.maxHeight = null;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
    var acc = document.getElementsByClassName("new-dest-boatType");
    var i;

    for (i = 0; i < acc.length; i++) {

        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");

            this.nextElementSibling.style.maxHeight = null;
            var panel = this.nextElementSibling.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
    var acc = document.getElementsByClassName("help-dest-boatType");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {

            this.classList.toggle("active");

            var panel = this.nextElementSibling.nextElementSibling;
            panel.nextElementSibling.style.maxHeight = null;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }

    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        if (acc[i].className.search('new-dest-discountItem') == -1 && acc[i].className.search('new-dest-boatType') == -1) {
            acc[i].addEventListener("click", function() {

                var panel = this.nextElementSibling;
                this.classList.toggle("active");
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    }

    jQuery("li.tab").on('click', function() {
        jQuery("li.tab").removeClass('activate');
        jQuery(this).addClass('activate');
        var tabNum = jQuery(this).attr('attr-tab');
        var attr = jQuery(this).attr('attr');

        jQuery(".tables").removeClass('active');
        jQuery(".tables").addClass('hidden');
        jQuery(".tables." + tabNum + "." + attr).removeClass('hidden');
        jQuery(".tables." + tabNum + "." + attr).addClass('active');


    });

    jQuery(".wp-sync-control").on('click', function() {
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxWPPortSync.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }
        ).done(function(msg) {
            jQuery(".wp-sync." + slug).html(msg);

        });
    });

    jQuery(".xml-sync-control").on('click', function() {
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxXmlPortSync.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }
        ).done(function(msg) {
            jQuery(".xml-sync." + slug).html(msg);

        });
    });

    jQuery(".dest-sync-control").on('click', function() { // egyből be kell tölteni
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxDestCitySync.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }
        ).done(function(msg) {
            jQuery(".dest-sync." + slug).html(msg);

        });
    });
    // jQuery(".dest-sync-control").trigger('click');
    jQuery(".dest-boatType-sync-control").on('click', function() { // egyből be kell tölteni
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxDestBoatTypesSync.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }
        ).done(function(msg) {
            jQuery(".dest-boatType-sync." + slug).html(msg);

        });
    });

    jQuery(".dest-discounts-sync-control").on('click', function() { // egyből be kell tölteni
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxDestDiscountItemSync.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }

        ).done(function(msg) {
            jQuery(".dest-discounts-sync." + slug).html(msg);

        });
    });

    jQuery(".dest-length-sync-control").on('click', function() { // egyből be kell tölteni
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxDestLenghtSetting.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }

        ).done(function(msg) {
            jQuery(".dest-length-sync." + slug).html(msg);

        });
    });

    jQuery(".dest-berth-sync-control").on('click', function() { // egyből be kell tölteni
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxDestBerthSetting.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }

        ).done(function(msg) {
            jQuery(".dest-berth-sync." + slug).html(msg);
        });
    });
    jQuery(".dest-service-types-control").on('click', function() { // egyből be kell tölteni
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajaxDestServiceTypes.php';
        var slug = jQuery(this).attr('attr');
        jQuery.ajax({
                url: send_url,
                method: "POST",
                cache: false,
                data: {
                    'slug': slug
                },
            }
        ).done(function(msg) {
            jQuery(".dest-service-types." + slug).html(msg);

        });
    });
</script>