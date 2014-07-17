<?php
/*
=====================================================
 Category XField v1.0.1
-----------------------------------------------------
 http://dle.net.tr
-----------------------------------------------------
 Copyright (c) 2014 Mehmet Hanoğlu
-----------------------------------------------------
 Lisans : GPL License
=====================================================
 Dosya: category.xfield.module.php
=====================================================
*/

if ( $dle_module == "showfull" OR $dle_module == "main" ) {
	if ( strpos( $tpl->copy_template, "{if cxfield=" ) ) {
		require_once ENGINE_DIR . "/classes/category.xfield.class.php";
		$xfdata = $xf->load( $cat_info[ $category_id ]['xfields'] );
		$cxfields = $xf->get( );
		if ( preg_match_all( "#\{if cxfield=['\"](.+?)['\"]\}[\s\n\t]*(.+?)[\s\n\t]*\{\/if\}#ies", $tpl->copy_template, $matches1 ) ) {
			$length = count( $matches1[0] );
			for ( $x = 0; $x <= $length; $x++ ) {
				if ( in_array( $matches1[1][$x], array_keys( $cxfields ) ) ) {
					if ( $cxfields[ $matches1[1][$x] ][1] == "textarea" ) {
						$xfdata[ $matches1[1][$x] ] = str_replace( "__NEWL__", "<br />", $xfdata[ $matches1[1][$x] ] );
					} else if ( $cxfields[ $matches1[1][$x] ][1] == "radio" || $cxfields[ $matches1[1][$x] ][1] == "select" ) {
						$_keys = explode( "|", $cxfields[ $matches1[1][$x] ][4] );
						$_vals = explode( "|", $cxfields[ $matches1[1][$x] ][3] );
						$_indx = array_search( $xfdata[ $matches1[1][$x] ], $_keys );
						$xfdata[ $matches1[1][$x] ] = $_vals[ $_indx ];
					}
					if ( in_array( $matches1[1][$x], array_keys( $xfdata ) ) && ! empty( $xfdata[ $matches1[1][$x] ] ) ) {
						$matches1[2][$x] = str_replace( "{name}", $matches1[1][$x], $matches1[2][$x] );
						$matches1[2][$x] = str_replace( "{key}", $xfdata[ $matches1[1][$x] ], $matches1[2][$x] );
						if ( $cxfields[ $matches1[1][$x] ][1] == "radio" || $cxfields[ $matches1[1][$x] ][1] == "select" ) {
							$matches1[2][$x] = str_replace( "{value}", $_keys[ $_indx ], $matches1[2][$x] );
						}
						$tpl->copy_template = str_replace( $matches1[0][$x], $matches1[2][$x], $tpl->copy_template );
					} else {
						$tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template );
					}
					 unset( $_keys, $_vals, $_indx );
				} else {
					$tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template );
				}
			}
		}
		unset( $xfdata, $matches1 );
	}
}
?>