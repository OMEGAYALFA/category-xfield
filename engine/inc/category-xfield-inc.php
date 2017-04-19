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

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) { die( "Hacking attempt!" ); }
if( $member_id['user_group'] != 1 ) { msg( "error", $lang['index_denied'], $lang['index_denied'] ); }

require_once ENGINE_DIR . "/classes/category.xfield.class.php";
$fields = $xf->get();

function showRow( $title = "", $description = "", $field = "", $hide = false, $id = "" ) {
	$hide = ($hide) ? " style=\"display:none;\"" : "";
	$id = ($id != "") ? " id=\"{$id}\"" : "";
	echo "<tr{$hide}{$id}>
		<td class=\"col-xs-10 col-sm-6 col-md-7\"><h6>{$title}</h6><span class=\"note large\">{$description}</span></td>
		<td class=\"col-xs-2 col-md-5 settingstd\">{$field}</td>
	</tr>";
}

function makeDropDown($options, $name, $selected, $id = false) {
	$id = ( $id == false ) ? "" : " id=\"" . $id . "\"";
	$output = "<select{$id} class=\"uniform\" name=\"{$name}\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"{$value}\"";
		if ( $selected == $value ) { $output .= " selected "; }
		$output .= ">{$description}</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function mainTable_head( $title, $right = "", $id = false ) {
	if ( $id ) {
		$id = " id=\"{$id}\"";
		$style = " style=\"display:none\"";
	} else { $style = ""; }
	echo <<< HTML
	<div class="box">
		<div class="box-header"{$id}{$style}>
			<div style="float: left; margin-top: 5px;">
				<div class="title"><div class="box-nav"><font size="2">{$title}</font></div></div>
			</div>
			<div style="float: right;">
				<div class="title"><div class="box-nav">{$right}</div></div>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div class="box-content">
			<table class="table table-normal">
HTML;
}

function mainTable_foot() {
	echo <<< HTML
			</table>
		</div>
	</div>
HTML;
}

$field_names = array("radio" => "Radio Buton", "text" => "Tek Satırlık Yazı", "textarea" => "Çok Satırlı Yazı", "select" => "Seçim Listesi");
$back_button = "<input onclick=\"window.location='{$PHP_SELF}?mod=category-xfield-inc'\" type=\"button\" class=\"btn btm-sm btn-red\" value=\"Geri Dön\" />";


if ( $_REQUEST['action'] == "save" ) {

	$post = $_POST['save'];

	if ( $post['old'] == "__new__" ) {
		$post['old'] = $post['id'];
	} else if ( ! in_array( $post['old'], array_keys( $fields ) ) ) {
		msg("error", "Hata", "Bilinmeyen alan ID'si. Lütfen tekrar deneyin.", "?mod=category-xfield-inc");
	}

	if ( $post['type'] == "textarea" ) {
		$post[ 'textarea_f1'] = intval( $post[ 'textarea_f1'] );
		$post[ 'textarea_f2'] = intval( $post[ 'textarea_f2'] );
	}

	if ( empty( $post['id'] ) || empty( $post['type'] ) || empty( $post['desc'] ) ) {
		msg("error", "Hata", "Tüm alanları doldurmalısınız.", "?mod=category-xfield-inc");
		if ( $post['type'] != "textarea" AND ( empty( $post[ $post['type'] . '_f1'] ) || empty( $post[ $post['type'] . '_f2'] ) ) ) {
			msg("error", "Hata", "Tüm alanları doldurmalısınız.", "?mod=category-xfield-inc");
		}
	}

	$fields[ $post['old'] ] = array( 0 => $post['id'], 1 => $post['type'], 2 => $post['desc'], 3 => $post[ $post['type'] . '_f1'], 4 => $post[ $post['type'] . '_f2'] );

	if ( $xf->save( $fields, $db ) ) {
		msg("info", "Uyarı", "İlave alan kaydedildi", "?mod=category-xfield-inc");
	} else {
		msg("error", "Hata", "İlave alan kaydedilemedi", "?mod=category-xfield-inc");
	}


} else if ( $_REQUEST['action'] == "newfield" ) {

	echoheader("<i class=\"icon-edit\"></i>MWS Category XField", "Alan ekleme" );
echo <<< HTML
<script type="text/javascript">
function alt_name () {
	$("input[name='save[id]']").bind('keyup', function() {
		var val = $(this).val();
		$.post(
			"engine/ajax/alt_name.php", {text: val}, function( data ) {
				$("input[name='save[id]']").val( data );
			}
		);
	});
}
$(document).ready(function() {
	$("#field_type option[value='radio']").attr('selected', 'selected');
	$("input[name='save[type]']").val( "radio" );
	$(".data").hide(); $("#radio").show();
	$(".iCheck-helper").click( function() {
		var inp = $(this).parent().find("input");
		if ( inp.attr('name') == "save[textarea_f1c]" ) {
			if ( inp.is(':checked') ) {
				$("input[name='save[textarea_f2c]']").parent().removeClass("checked");
				$("input[name='save[textarea_f1]']").val("1");
				$("input[name='save[textarea_f2]']").val("0");
			} else {
				$("input[name='save[textarea_f2c]']").parent().addClass("checked");
				$("input[name='save[textarea_f1]']").val("0");
				$("input[name='save[textarea_f2]']").val("1");
			}
		} else if ( inp.attr('name') == "save[textarea_f2c]" ) {
			if ( inp.is(':checked') ) {
				$("input[name='save[textarea_f1c]']").parent().removeClass("checked");
				$("input[name='save[textarea_f1]']").val("0");
				$("input[name='save[textarea_f2]']").val("1");
			} else {
				$("input[name='save[textarea_f1c]']").parent().addClass("checked");
				$("input[name='save[textarea_f1]']").val("1");
				$("input[name='save[textarea_f2]']").val("0");
			}
		}
	});
	$("#field_type").change(function() {
		var selected = $(this).val();
		if ( selected == "radio" ) {
			$(".data").hide();
			$("#radio").show();
		} else if ( selected == "text" ) {
			$(".data").hide();
			$("#text").show();
		} else if ( selected == "textarea" ) {
			$(".data").hide();
			$("#textarea").hide();
		} else if ( selected == "select" ) {
			$(".data").hide();
			$("#select").show();
		}
		$("#field_type option[value!='" + selected + "']").removeAttr('selected');
		$("#field_type option[value='" + selected + "']").attr('selected', 'selected');
		$("input[name='save[type]']").val( selected );
	});
});
</script>
HTML;
	mainTable_head("Alan Ekleme", "");

	echo <<< HTML
	<form action="{$PHP_SELF}?mod=category-xfield-inc&action=save" onsubmit="ShowLoading('');" name="conf" id="conf" method="post">
HTML;
		showRow("Alan Adı", "Eğer alan adını değiştirirseniz, şablon dosyasını tekrar düzenlemelisiniz", "<input onFocus=\"alt_name();\" name='save[id]' value=\"{$data[0]}\" size=\"20\" type=\"text\" style=\"text-align: left;\" />&nbsp;&nbsp;<a data-original-title=\"Alan adını girerken otomatik olarak kabul edilebilir karakterlere dönüştürülecektir.\" href=\"#\" class=\"tip\" title=\"\"><i class=\"icon-info-sign\"></i></a>");
		showRow("Alan Açıklaması", "Alan hakkında açıklama giriniz", "<input name='save[desc]' value=\"{$data[2]}\" size=\"50\" type=\"text\" style=\"text-align: left;\" />");
		showRow("Alan Tipi", "Alan tipini seçiniz", makeDropDown( array("radio" => "Radio Buton", "text" => "Tek Satırlık Yazı", "textarea" => "Çok Satırlı Yazı", "select" => "Seçim Listesi"), "save[types]", $data[1], "field_type" ) );

echo <<< HTML
<tr id="text" class="data">
	<td valign="top"><b>Tek Satırlık Yazı Alanı</b><br /><span class="small">Alanın özelliklerini girin</span>
	<td align="middle">
		Max. Uzunluk : <input name="save[text_f1]" type="text" size="10" value="{$data[3]}" />&nbsp;&nbsp;
		<input name="save[text_f2]" size="10" type="text" value="{$data[4]}" /> : Alan Genişliği
	</td>
</tr>
<tr id="radio" class="data">
	<td valign="top"><b>Radio Buton</b><br /><span class="small">Her iki alan için aynı sıralamayı kullanarak <b>|</b> ile ayırın. Boşluklar silinecektir!<br /><b>Örnek:</b><br />key1<b>|</b>key2<b>|</b>key3<br />val<b>|</b>val2<b>|</b>val3</span>
	<td align="middle">
		Kelimeler&nbsp;:&nbsp;&nbsp;<input type="text" name="save[radio_f1]" style="width: 400px;" value="{$data[3]}" />
		<br /><br />
		Değerleri&nbsp;:&nbsp;<input type="text" name="save[radio_f2]" style="width: 400px;" value="{$data[4]}" />
	</td>
</tr>
<tr id="select" class="data">
	<td valign="top"><b>Seçim Listesi</b><br /><span class="small">Her iki alan için aynı sıralamayı kullanarak <b>|</b> ile ayırın. Boşluklar silinecektir!<br /><b>Örnek:</b><br />key1<b>|</b>key2<b>|</b>key3<br />val<b>|</b>val2<b>|</b>val3</span>
	<td align="middle">
		Kelimeler&nbsp;:&nbsp;&nbsp;<input type="text" name="save[select_f1]" style="width: 400px;" value="{$data[3]}" />
		<br /><br />
		Değerleri&nbsp;:&nbsp;<input type="text" name="save[select_f2]" style="width: 400px;" value="{$data[4]}" />
	</td>
</tr>
<tr id="textarea" class="data">
	<td valign="top"><b>Çok Satırlı Yazı</b><br />Her iki alan için aynı sıralamayı kullanarak <b>|</b> ile ayırın. Boşluklar silinecektir!<br /><b>Örnek:</b><br />key1<b>|</b>key2<b>|</b>key3<br />val<b>|</b>val2<b>|</b>val3
	<td align="middle">
		&nbsp;&nbsp;
	</td>
</tr>
HTML;

echo <<< HTML
		<tr>
			<td colspan="2"><input type="submit" class="btn btm-sm btn-green" value="Kaydet" />&nbsp;&nbsp;{$back_button}</td>
		</tr>
		<input type="hidden" name="save[old]" value="__new__" />
		<input type="hidden" name="save[type]" value="" />
		<input type="hidden" name="save[textarea_f1]" value="" />
		<input type="hidden" name="save[textarea_f2]" value="" />
	</form>
HTML;
	mainTable_foot();
	echofooter();

} else if ( $_REQUEST['action'] == "edit" ) {
	if ( ! isset( $_REQUEST['id'] ) || $member_id['user_group'] != 1 ) msg("error", $lang['opt_denied'], $lang['opt_denied'], "?mod=category-xfield-inc");
	$field = $db->safesql( $_REQUEST['id'] );
	$data = $fields[ $field ];

	echoheader("<i class=\"icon-edit\"></i>MWS Category XField", "Alan düzenleme" );
echo <<< HTML

<script type="text/javascript">
function alt_name () {
	$("input[name='save[id]']").bind('keyup', function() {
		var val = $(this).val();
		$.post(
			"engine/ajax/alt_name.php", {text: val}, function( data ) {
				$("input[name='save[id]']").val( data );
			}
		);
	});
}
$(document).ready(function() {
	$(".data").hide();
	$(".iCheck-helper").click( function() {
		var inp = $(this).parent().find("input");
		if ( inp.attr('name') == "save[textarea_f1c]" ) {
			if ( inp.is(':checked') ) {
				$("input[name='save[textarea_f2c]']").parent().removeClass("checked");
				$("input[name='save[textarea_f1]']").val("1");
				$("input[name='save[textarea_f2]']").val("0");
			} else {
				$("input[name='save[textarea_f2c]']").parent().addClass("checked");
				$("input[name='save[textarea_f1]']").val("0");
				$("input[name='save[textarea_f2]']").val("1");
			}
		} else if ( inp.attr('name') == "save[textarea_f2c]" ) {
			if ( inp.is(':checked') ) {
				$("input[name='save[textarea_f1c]']").parent().removeClass("checked");
				$("input[name='save[textarea_f1]']").val("0");
				$("input[name='save[textarea_f2]']").val("1");
			} else {
				$("input[name='save[textarea_f1c]']").parent().addClass("checked");
				$("input[name='save[textarea_f1]']").val("1");
				$("input[name='save[textarea_f2]']").val("0");
			}
		}
	});
	$("#{$data[1]}").show();
	$("#textarea").hide();
	$("#field_type").change(function() {
		$(".data").hide();
		var selected = $(this).val();
		if ( selected == "radio" ) {
			$("#radio").show();
		} else if ( selected == "text" ) {
			$("#text").show();
		} else if ( selected == "textarea" ) {
			$("#textarea").hide();
		} else if ( selected == "select" ) {
			$("#select").show();
		}
		$("#field_type option[value!='" + selected + "']").removeAttr('selected');
		$("#field_type option[value='" + selected + "']").attr('selected', 'selected');
		$("input[name='save[type]']").val( selected );
	});
});
</script>
HTML;
	mainTable_head("Alan Düzenleme", "Alan ID : {$field}");

	echo <<< HTML
	<form action="{$PHP_SELF}?mod=category-xfield-inc&action=save" onsubmit="ShowLoading('');" name="conf" id="conf" method="post">
HTML;

		showRow("Alan Adı", "Eğer alan adını değiştirirseniz, şablon dosyasını tekrar düzenlemelisiniz", "<input onFocus=\"alt_name();\" name='save[id]' value=\"{$data[0]}\" size=\"20\" type=\"text\" style=\"text-align: left;\" />&nbsp;&nbsp;<a data-original-title=\"Alan adını girerken otomatik olarak kabul edilebilir karakterlere dönüştürülecektir.\" href=\"#\" class=\"tip\" title=\"\"><i class=\"icon-info-sign\"></i></a>");
		showRow("Alan Açıklaması", "Alan hakkında açıklama giriniz", "<input name='save[desc]' value=\"{$data[2]}\" size=\"50\" type=\"text\" style=\"text-align: left;\" />");
		showRow("Alan Tipi", "Alan tipini seçiniz", makeDropDown( $field_names, "save[type]", $data[1], "field_type" ) );

$check1 = ( $data[3] == "1" ) ? " checked=\"checked\"" : "";
$check2 = ( $data[4] == "1" ) ? " checked=\"checked\"" : "";

echo <<< HTML
<tr id="text" class="data">
	<td valign="top"><b>Tek Satırlık Yazı Alanı</b><br /><span class="small">Alanın özelliklerini girin</span>
	<td align="middle">
		Max. Uzunluk : <input name="save[text_f1]" type="text" size="10" value="{$data[3]}" />&nbsp;&nbsp;
		<input name="save[text_f2]" type="text" size="10" value="{$data[4]}" /> : Alan Genişliği
	</td>
</tr>
<tr id="radio" class="data">
	<td valign="top"><b>Radio Buton</b><br /><span class="small">Her iki alan için aynı sıralamayı kullanarak <b>|</b> ile ayırın. Boşluklar silinecektir!<br /><b>Örnek:</b><br />key1<b>|</b>key2<b>|</b>key3<br />val<b>|</b>val2<b>|</b>val3</span>
	<td align="middle">
		Kelimeler&nbsp;:&nbsp;&nbsp;<input type="text" name="save[radio_f1]" style="width: 400px;" value="{$data[3]}" />
		<br /><br />
		Değerleri&nbsp;:&nbsp;<input type="text" name="save[radio_f2]" style="width: 400px;" value="{$data[4]}" />
	</td>
</tr>
<tr id="select" class="data">
	<td valign="top"><b>Seçim Listesi</b><br /><span class="small">Her iki alan için aynı sıralamayı kullanarak <b>|</b> ile ayırın. Boşluklar silinecektir!<br /><b>Örnek:</b><br />key1<b>|</b>key2<b>|</b>key3<br />val<b>|</b>val2<b>|</b>val3</span>
	<td align="middle">
		Kelimeler&nbsp;:&nbsp;&nbsp;<input type="text" name="save[select_f1]" style="width: 400px;" value="{$data[3]}" />
		<br /><br />
		Değerleri&nbsp;:&nbsp;<input type="text" name="save[select_f2]" style="width: 400px;" value="{$data[4]}" />
	</td>
</tr>
<tr id="textarea" class="data">
	<td valign="top"><b>Çok Satırlı Yazı</b><br /><span class="small">Her iki alan için aynı sıralamayı kullanarak <b>|</b> ile ayırın. Boşluklar silinecektir!<br /><b>Örnek:</b><br />key1<b>|</b>key2<b>|</b>key3<br />val<b>|</b>val2<b>|</b>val3</span>
	<td align="middle">
		&nbsp;&nbsp;
	</td>
</tr>
HTML;

echo <<< HTML
		<tr>
			<td colspan="2"><input type="submit" class="btn btm-sm btn-green" value="Kaydet" />&nbsp;&nbsp;{$back_button}</td>
		</tr>
		<input type="hidden" name="save[old]" value="{$field}" />
		<input type="hidden" name="save[type]" value="{$data[1]}" />
		<input type="hidden" name="save[textarea_f1]" value="" />
		<input type="hidden" name="save[textarea_f2]" value="" />
	</form>
HTML;
	mainTable_foot();
	echofooter();

} else if ( $_REQUEST['action'] == "del" ) {
	if ( ! isset( $_REQUEST['id'] ) || $member_id['user_group'] != 1 ) msg("error", $lang['opt_denied'], $lang['opt_denied'], "?mod=category-xfield-inc");
	if ( !empty( $_REQUEST['id'] ) ) {
		$field = $db->safesql( $_REQUEST['id'] );
		unset( $fields[ $field ] );

		if ( $xf->save( $fields, $db ) ) {
			msg("info", "Uyarı", "İlave alan silindi", "?mod=category-xfield-inc");
		} else {
			msg("error", "Hata", "İlave alan silinemedi", "?mod=category-xfield-inc");
		}
	} else {
		msg("error", "Hata", "ID bilgisi okunamıyor !", "?mod=category-xfield-inc");
	}

} else {
	echoheader("<i class=\"icon-edit\"></i>MWS Category XField", "Kategori İlave alanları" );
	mainTable_head("Kategoriler için İlave Alanlar", "");

	if ( count( $fields ) > 0 ) {
		echo <<< HTML
		<tr class="head"><td>Alan Adı</td><td>Açıklaması</td><td>Tipi</td><td>Düzenle</td><td>Sil</td></tr>
HTML;
		foreach( $fields as $field ) {
			echo <<< HTML
			<tr id="{$field[0]}">
				<td>{$field[0]}</td>
				<td>{$field[2]}</td>
				<td width="250">{$field_names[ $field[1] ]} ({$field[1]})</td>
				<td width="80"><input onclick="window.location='{$PHP_SELF}?mod=category-xfield-inc&action=edit&id={$field[0]}'" type="button" class="btn btm-sm btn-red" value="Düzenle" /></td>
				<td width="80"><input onclick="window.location='{$PHP_SELF}?mod=category-xfield-inc&action=del&id={$field[0]}'" type="button" class="btn btm-sm btn-danger" value="Sil" /></td>
			</tr>
HTML;
		}
	} else {
	echo <<< HTML
		<tr>
			<td colspan="5">
				Henüz hiç alan eklenmemiş. Aşağıdaki butona tıklayarak yeni alan ekleyebilirsiniz.
			</td>
		</tr>
HTML;
	}
	echo <<< HTML
		<tr>
			<td colspan="5">
				<input onclick="window.location='{$PHP_SELF}?mod=category-xfield-inc&action=newfield'" type="button" class="btn btm-sm btn-green" value="Alan Ekle" />&nbsp;&nbsp;
				<input onclick="window.location='{$PHP_SELF}?mod=categories'" type="button" class="btn btm-sm btn-black" value="Kategorilere Git" />
			</td>
		</tr>
HTML;
	mainTable_foot();
	echofooter();
}


?>