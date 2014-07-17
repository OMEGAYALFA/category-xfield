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
 Dosya: category.xfield.class.php
=====================================================
*/

class CatXField {
	
	private $XF = array();
	public $QU = array ("\x27", "\x22", "\x60");
	private $_file = "";
	public $Parser = false;
	
	public function CatXField( ) {
		$this->_file = ENGINE_DIR . "/data/catxfields.txt";
	    $_temp0 = fopen( $this->_file, "r" );
	    $_temp1 = fread( $_temp0, filesize( $this->_file ) );
	    fclose( $_temp0 );
		if ( strlen( $_temp1 ) > 0 ) {
			$_temp2 = explode( "\n", $_temp1 );
			foreach( $_temp2 as $_temp3 ) {
				$_temp4 = explode( "~||~", trim( $_temp3 ) );
				$this->XF[ $_temp4[0] ] = $_temp4;
			}
			unset( $_file, $_temp0, $_temp1, $_temp2, $_temp3, $_temp4 );
		} else {
			$this->XF = array();
			unset( $_file, $_temp0, $_temp1 );
		}
	}

	public function load( $array ) {
		$_result = array();
		$_temp0 = explode( "~||~", $array );
		foreach( $_temp0 as $_temp1 ) {
			$_temp2 = explode( "|", $_temp1 );
			$_result[ $_temp2[0] ] = $_temp2[1];
		}
		unset( $array, $_temp0, $_temp1, $_temp2 );
		return $_result;
	}

	public function get( ) {
		return $this->XF;
	}
	
	public function parse( $array, &$db ) {
		$_temp0 = array();
		foreach( $array as $name => $value ) {
			$value = str_replace( "\r\n", "__NEWL__", $value );
			$_val = $value;
			$_temp0[] = "{$name}|{$_val}";
		}
		unset( $array );
		return implode( "~||~", $_temp0 );
	}

	public function save( $array, &$db ) {
		$_temp0 = "";
		foreach( $array as $field ) { $_temp0 .= $db->safesql( $field[0] ) . "~||~" . $db->safesql( $field[1] ) . "~||~" . $db->safesql( $field[2] ) . "~||~" . $db->safesql( $field[3] ) . "~||~" . $db->safesql( $field[4] ) . "\n"; }
		unset( $array ); $_temp0 = trim( str_replace("\\r", "", $_temp0 ) );
	    $_temp1 = fopen( $this->_file, "w" );
	    $result = fwrite( $_temp1, $_temp0 );
	    fclose( $_temp1 );
		return $result;
	}

	public function printer( &$xfields, &$xfielddata = array() ) {
		$this->_formatter( $xfields, $xfielddata, "echo" );
	}

	public function resulter( &$xfields, &$xfielddata = array() ) {
		return $this->_formatter( $xfields, $xfielddata, "return" );
	}





	private function _formatter( $xfields, $xfielddata = array(), $_type ) {
		$_result = "";
		foreach( $xfields as $xfname => $xfattr ) {
			if ( $xfattr[1] == "text" ) {
				$_val = ( empty( $xfielddata[ $xfname ] ) ) ? "" : " value=\"{$xfielddata[ $xfname ]}\"";
				$_result .= "<div class=\"form-group\"><label class=\"control-label col-lg-2\">{$xfattr[2]} ({$xfattr[0]})</label><div class=\"col-lg-10\"><input class=\"xft\"{$_val} type=\"text\" size=\"{$xfattr[4]}\" maxlength=\"{$xfattr[3]}\" name=\"catxf[{$xfname}]\"></div></div>";
			} else if ( $xfattr[1] == "radio" ) {
				$_opt = array( explode( "|", $xfattr[3] ), explode( "|", $xfattr[4] ) );
				$_options = "";
				for ( $x = 0; $x < count( $_opt[0] ); $x++ ) {
					$_val = ( trim( $xfielddata[ $xfname ] ) == trim( $_opt[1][$x] ) ) ? " checked" : "";
					$_options .= "<input class=\"xfr\" type=\"radio\"{$_val} value=\"{$_opt[1][$x]}\" name=\"catxf[{$xfname}]\">&nbsp;{$_opt[0][$x]}&nbsp;&nbsp;";
				}
				$_result .= "<div class=\"form-group\"><label class=\"control-label col-lg-2\">{$xfattr[2]} ({$xfattr[0]})</label><div class=\"col-lg-10\">{$_options}</div></div>";
			} else if ( $xfattr[1] == "select" ) {
				$_opt = array( explode( "|", $xfattr[3] ), explode( "|", $xfattr[4] ) );
				$_options = "<select name=\"catxf[{$xfname}]\" class=\"xfs\">";
				for ( $x = 0; $x < count( $_opt[0] ); $x++ ) {
					$_options .= "<option value=\"{$_opt[1][$x]}\">{$_opt[0][$x]}</option>";
				}
				$_options .= "</select>";
				$_result .= "<div class=\"form-group\"><label class=\"control-label col-lg-2\">{$xfattr[2]} ({$xfattr[0]})</label><div class=\"col-lg-10\">{$_options}</div></div>";
			} else if ( $xfattr[1] == "textarea" ) {
				$_val = ( empty( $xfielddata[ $xfname ] ) ) ? "" : $xfielddata[ $xfname ];
				if ( $xfattr[3] == "yes" AND $this->Parser ) {
					$_val = $this->Parser->decodeBBCodes( $_val, true );
					$_val = str_replace("&lt;br /&gt;", "\r\n", $_val );
				}
				$_val = str_replace( "__NEWL__", "\r\n", $_val );
				$_result .= "<div class=\"form-group\"><label class=\"control-label col-lg-2\">{$xfattr[2]} ({$xfattr[0]})</label><div class=\"col-lg-10\"><textarea name=\"catxf[{$xfname}]\" style=\"width:351px;height:100px;\" class=\"bk xfta\">{$_val}</textarea></div></div>";
			}
		}
		if ( $_type == "echo" ) { echo $_result; }
		else { return $_result; }
	}
}

$xf = new CatXField( );

?>