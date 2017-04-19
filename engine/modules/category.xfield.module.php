<?php
/*
=============================================
 Name      : MWS Category XField v1.1
 Author    : Mehmet Hanoğlu ( MaRZoCHi )
 Site      : http://dle.net.tr/
 License   : MIT License
 Date      : 19.04.2017
=============================================
*/

if ( ! empty( $category_id ) ) {
	if ( strpos( $tpl->copy_template, "{if cxfield=" ) ) {
		require_once ENGINE_DIR . "/classes/category.xfield.class.php";
		$xfdata = $xf->load( $cat_info[ $category_id ]['xfields'] );
		$cxfields = $xf->get();
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
						$field_name = $matches1[1][$x];
						$field_value = $xfdata[ $field_name ];
						$field_attr = $cxfields[ $field_name ];
						if ( $field_attr[1] == "text" || $field_attr[1] == "textarea" ) {
							$matches1[2][$x] = str_replace( "{name}", $field_name, $matches1[2][$x] );
							$matches1[2][$x] = str_replace( "{key}", "", $matches1[2][$x] );
							$matches1[2][$x] = str_replace( "{value}", $field_value, $matches1[2][$x] );
						}
						else if ( $field_attr[1] == "select" || $field_attr[1] == "radio" ) {
							$field_keys = explode( '|', $field_attr[3] );
							$field_vals = explode( '|', $field_attr[4] );
							$field_key = $field_value;
							$field_value = $field_vals[ array_search( $field_key, $field_keys ) ];
							$matches1[2][$x] = str_replace( "{name}", $field_name, $matches1[2][$x] );
							$matches1[2][$x] = str_replace( "{key}", $field_key, $matches1[2][$x] );
							$matches1[2][$x] = str_replace( "{value}", $field_value, $matches1[2][$x] );
						}
						$matches1[2][$x] = str_replace( "{type}", $field_attr[1], $matches1[2][$x] );
						$tpl->copy_template = str_replace( $matches1[0][$x], $matches1[2][$x], $tpl->copy_template );
					} else {
						$tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template );
					}
				} else {
					$tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template );
				}
			}
		}
	} else {
		$tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template );
	}
} else {
	if ( preg_match_all( "#\{if cxfield=['\"](.+?)['\"]\}[\s\n\t]*(.+?)[\s\n\t]*\{\/if\}#ies", $tpl->copy_template, $matches1 ) ) {
		for ( $x = 0; $x <= count( $matches1[0] ); $x++ ) {
			$tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template );
		}
	}
}
?>