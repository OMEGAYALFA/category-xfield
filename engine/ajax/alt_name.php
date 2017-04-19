<?php
/*
=====================================================
 Category XField v1.0
-----------------------------------------------------
 http://dle.net.tr
-----------------------------------------------------
 Copyright (c) 2014 Mehmet Hanoğlu
-----------------------------------------------------
 Lisans : GPL License
=====================================================
 Dosya: alt_name.php
=====================================================
*/

define( 'DATALIFEENGINE', true);
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -12 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

include ENGINE_DIR . '/data/config.php';

date_default_timezone_set ( $config['date_adjust'] );

require_once ENGINE_DIR.'/modules/functions.php';
@include_once ROOT_DIR . '/language/' . $config['langs'] . '/website.lng';

if ( isset( $_POST['text'] ) ) {
	echo totranslit( $_POST['text'], true, false );
}
?>