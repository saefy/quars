<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
// use \App\Models\configSysModel;
function fk_theme($pTheme=null){
	if($pTheme !== null){
		$theme = $pTheme;
	}else{
		$theme  = (defined('theme'))? @constant('theme') : @constant('default_theme');
	}

	return $theme;
}

function fk_sanitize($v){

	$sea = array('<?','?>','<','>','\'','"');
	$rep = array('&lt;?','?&gt;','&lt;','&gt;','&apos;','&quot;');
	$v = str_replace($sea, $rep, $v);


	return $v;

}
function fk_theme_url($theme=null){
	$theme_url = HTTP.'frontend/themes/'.fk_theme($theme);
	return $theme_url;

}
function fk_loading_img(){
	return '<img src="'.fk_theme_url().'/../../img/ajax-loader.gif">';
}
function fk_no_display_header(){
	$GLOBALS['QRS']['display_header_footer']=FALSE;
}
function fk_header_blank(){
	$GLOBALS['QRS']['blank_header_footer']=TRUE;
}
function display_header_footer(){

	if(@$GLOBALS['QRS']['display_header_footer']===FALSE){
		return FALSE;
	}else{ return TRUE;}

}
function display_blank_header(){
	if(@$GLOBALS['QRS']['blank_header_footer']===TRUE){
		return TRUE;
	}else{ return FALSE;}
}

function fk_header($theme = NULL){

	if($theme==NULL){
		$theme=fk_theme();

	}else{
		define('theme',$theme);
	}

	if(display_header_footer()){
		if(display_blank_header()){
			// Display Blank Header
			fk::blank_header();
		}else{
			// Header normal
			\Quars\Quars::_use('public/frontend/themes/'.$theme.'/header.php');
		}

	}

}
function fk_footer($theme = NULL){

	if($theme==NULL){
		$theme=fk_theme();
	}
	if(display_header_footer()){

		if(display_blank_header()){
			// Display Blank Header
			fk::blank_footer();
		}else{
			// Header normal
			\Quars\Quars::_use('public/frontend/themes/'.$theme.'/footer.php');
		}
	}
}

function fk_get_path(){
	if(app_running_is('fix_path',1) ){
		
		$php_self_dir = str_replace('public/index.php','',$_SERVER['SCRIPT_NAME']);
		
		$path =  str_replace($php_self_dir,'',$_SERVER['REQUEST_URI']);
		if(strpos($_SERVER['REQUEST_URI'], 'public/index.php')){
			$path =  str_replace('public/index.php','',$path);
		}else{
			$path =  str_replace('public','',$path);
		}
		
		$path =  trim($path,'/');

		$pathxp = explode('?',$path);
		if(isset($pathxp[1])){
			$path = $pathxp[0];
		}
		unset($php_self_dir);
		

	}else{
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$path =  trim($path,'/');
	}

	return $path;

}

function fk_link($p_lnk = ''){




	if($GLOBALS['QRS']['config']['APP']['mod_rewrite']){
		//mod rewrite
		$link = HTTP.$p_lnk;
		/*if($p_lnk=='' || fk_post('ajax')==1){
			$link = $GLOBALS['QRS']['RUNNING']['app']['www_server'].$p_lnk;
		}else{
			$link = HTTP.$p_lnk;
		}*/


	}else{

		//example.com/index.php//news/article/my_article/
		//$p_lnk = "/news/article/my_article/&v2=test";

		// no mod rewrite




		$url_vars = explode('?',$p_lnk);

		$url = isset($url_vars[0])?$url_vars[0]:'';
		$url .= isset($url_vars[1])?'?'.$url_vars[1]:'';



		//$url = str_replace('/','|',$url);
		// 'Account::Producto?{x=1;y=2}';

		$link = HTTP."index.php/".$url;



	}

	return $link;

}



function fk_money_format($amount,$mode=''){

	$amount = (double)$amount;

	//if(app_running_is('server_os', 'linux')){
	if(app_running_is('server_os', 'linux')){
		//For linux
		setlocale(LC_MONETARY, 'es_MX');
		$mon_val = money_format('%(#10n', $amount);
	}else{

		if(app_running_is('server_os', 'windows')){
			// Only for windows
			setlocale(LC_ALL, ''); // Locale will be different on each system.
			$locale = localeconv();
			$mon_val =  $locale['currency_symbol'].number_format($amount, 2, $locale['decimal_point'], $locale['thousands_sep']);
		}else{

			$mon_val = '$'.number_format($amount, 2, '.', ',');

		}


	}

	if($mode=='zero_empty'){
		if($amount==0){ $mon_val='';}
	}
	if($mode=='zero_dash'){
		if($amount==0){ $mon_val='$ - - - ';}
	}

	return $mon_val;
}

function encodedArray($StrEncoded){
	/*
	 * Generar array de caracteres especiales
	 * ej: base64_encode("�,�,�,�,�,�,�,�,�,�,�,�")
	 * */
	$StrDecoded = base64_decode($StrEncoded);
	eval("\$strByComas = \"$StrDecoded\";");
	$Arr=explode(',', $strByComas);

	return $Arr;
}

function fk_str_format($txt,$f = 'html:no-tags',$f_2 = ''){
	// Para:  'a', 'e', 'i', 'o', 'u', 'n','A', 'E', 'I', 'O', 'U', 'N'
	$find = encodedArray('4SzpLO0s8yz6LPEswSzJLM0s0yzaLNE=');
	// Para:  'a', 'e', 'i', 'o', 'u', 'n','A', 'E', 'I', 'O', 'U', 'N','<','>','"'
	$arr1 = encodedArray('4SzpLO0s8yz6LPEswSzJLM0s0yzaLNEsPCw+');
	$findHtml = array_merge($arr1,array('"')); // Add quot

	switch($f){
		//---------HTML---------------
		case "html":

			$repl = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&ntilde;'
			,'&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;','&lt;','&gt;','&quot;');
			$txt = str_replace ($findHtml, $repl, $txt);
			$txtrs = $txt;
			break;
			//---------HTML respetando los <>---------------
		case "html:no-tags":

			$repl = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&ntilde;'
			,'&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;');
			$txt = str_replace ($find, $repl, $txt);
			$txtrs = $txt;
			break;
			//---------Texto plano---------------
		case "txt":

			$repl = array('a', 'e', 'i', 'o', 'u', 'n','A', 'E', 'I', 'O', 'U', 'N');
			$txt = str_replace ($find, $repl, $txt);
			$txtrs = $txt;
			break;
			//---------For Url link---------------
		case "url":
			//Rememplazamos caracteres especiales latinos

			$repl = array('a', 'e', 'i', 'o', 'u', 'n','A', 'E', 'I', 'O', 'U', 'N');
			$txt = str_replace ($find, $repl, $txt);
			// A�aadimos los guiones
			$find2 = array(' ', '&', '\r\n', '\n', '+');
			$txt = str_replace ($find2, '-', $txt);
			// Eliminamos y Reemplazamos dem�s caracteres especiales
			$find3 = array('/[^A-Za-z0-9\-<>.$]/', '/[\-]+/', '/<[^>]*>/');
			$repl = array('', '-', '');
			$txt = preg_replace ($find3, $repl, $txt);
			$txtrs = $txt;
			break;
		case "php_var":
			//Rememplazamos caracteres especiales latinos
			$repl = array('a', 'e', 'i', 'o', 'u', 'n','A', 'E', 'I', 'O', 'U', 'N');
			$txt = str_replace ($find, $repl, trim($txt));
			// Anadimos los guiones
			$find2 = array(' ', '&', '\r\n', '\n', '+');
			$txt = str_replace ($find2, '_', $txt);
			// Eliminamos y Reemplazamos demas caracteres especiales
			$find3 = array('/[^A-Za-z0-9\-<>.$]/', '/[\-]+/', '/<[^>]*>/');
			$repl = array('_', '_', '');
			$txt = preg_replace ($find3, $repl, $txt);
			$txtrs = $txt;
			break;
			//---------For CamelCase---------------
		case "camelcase":
			$txtrs =  camelcase($txt);
			break;

	 default:
	 	$txtrs = $txt;
	 	break;

	} // End Case

	// Second Format "CamelCase"

	if($f_2 == 'camelcase'){
		$txtrs = camelcase($txtrs);
	}

	return $txtrs;
} // End fk_str_format

function fk_file_exists($f){

	$rs = false;
	if(trim($f)!=NULL && trim($f)!=''){

		$rs = file_exists(SYSTEM_PATH.$f);

	}

	return $rs;
}

function fk_js(){
	$js_path = HTTP.'frontend/javascript/';

	?>
<script language="javascript" type="text/javascript"> var HTTP = "<?php echo HTTP?>"; var HTTP_FILE = "<?php if($GLOBALS['QRS']['config']['APP']['mod_rewrite']){echo HTTP;}else{echo HTTP.'index.php/';} ?>";</script>
<script
	type="text/javascript"
	src="<?php echo $js_path;?>CODE/jquery-ui-1.10.0.custom/js/jquery-1.9.0.js"></script>
	<?php /**/?>
<script
	type="text/javascript"
	src="<?php echo $js_path;?>CODE/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.min.js"></script>
<script
	type="text/javascript"
	src="<?php echo $js_path;?>CODE/validate/jquery.validate.js"></script>
<script
	type="text/javascript"
	src="<?php echo $js_path;?>CODE/validate/jquery.maskedinput-1.3.1.js"></script>
<script
	type="text/javascript"
	src="<?=$js_path;?>CODE/dataTables/media/js/jquery.dataTables.min.js"></script>
<script
	type="text/javascript"
	src="<?=$js_path;?>CODE/dataTables/media/js/jquery.dataTables.FkUtils.js"></script>
<script
	type="text/javascript" src="<?php echo $js_path;?>fk.js"></script>

<script
	type="text/javascript"
	src="<?php echo $js_path;?>jquery-ui-timepicker-addon.js"></script>

	<?php
	echo $GLOBALS['QRS']['js_links'];

}

function fk_js_addResource($url,$default_path = true){
	if($default_path){
		$url = str_replace('app/resources/', '', $url);
		$url = 'app/Resources/'.$url;
	}
	//$GLOBALS['QRS']['js_links'] .= '<script src="'.HTTP.'frontend/fk_utils/FkResource.php?r='.encode($url).'&t=js" type="text/javascript"></script>
	$GLOBALS['QRS']['js_links'] .= '<script src="'.fk_link('QrsGate/Resource/').'?r='.encode($url).'&t=js" type="text/javascript"></script>
';
}

function fk_css_addResource($url,$default_path = true){
	if($default_path){
		$url = str_replace('app/resources/', '', $url);
		$url = 'app/Resources/'.$url;
	}
	//$GLOBALS['QRS']['css_links'] .= '<link type="text/css" href="'.HTTP.'frontend/fk_utils/FkResource.php?r='.encode($url).'&t=css" rel="stylesheet" />
	$GLOBALS['QRS']['css_links'] .= '<link type="text/css" href="'.fk_link('QrsGate/Resource/').'?r='.encode($url).'&t=css" rel="stylesheet" />
';
}

function fk_js_addLink($url){
	$GLOBALS['QRS']['js_links'] .= '<script type="text/javascript" language="javascript" src="'.$url.'"></script>
';
}

function fk_css_addLink($url){
	$GLOBALS['QRS']['css_links'] .= '<link type="text/css" href="'.$url.'" rel="stylesheet" />
';
}
function fk_css_v3(){

	$js_path = HTTP.'frontend/javascript/';
	$css_path = HTTP.'frontend/css/';

	echo $GLOBALS['QRS']['css_links'];

	#print all css links
}
function fk_css_load(){
	#print all css links
	$js_path = HTTP.'frontend/javascript/';
	$css_path = HTTP.'frontend/css/';
	echo '<link rel="stylesheet" type="text/css" href="'.HTTP.'frontend/javascript/CODE/snackbar/snackbar.min.css"/ >';
	echo $GLOBALS['QRS']['css_links'];


}
function fk_js_load(){
	$js_path = HTTP.'frontend/javascript/';

	?>
<script language="javascript" type="text/javascript"> var HTTP = "<?php echo HTTP?>"; var HTTP_FILE = "<?php if($GLOBALS['QRS']['config']['APP']['mod_rewrite']){echo HTTP;}else{echo HTTP.'index.php/';} ?>";</script>
<script	type="text/javascript" src="<?php echo $js_path;?>fk.js"></script>
<script	type="text/javascript" src="<?php echo $js_path;?>kui.js"></script>
<script	type="text/javascript" src="<?php echo $js_path;?>custom.js"></script>
<script type="text/javascript" src="<?php echo $js_path;?>CODE/validate/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $js_path;?>CODE/snackbar/snackbar.min.js"></script>

<?php echo $GLOBALS['QRS']['js_links'];


}
function fk_js_v3(){
	$js_path = HTTP.'frontend/javascript/';

	?>
<script language="javascript" type="text/javascript"> var HTTP = "<?php echo HTTP?>"; var HTTP_FILE = "<?php if($GLOBALS['QRS']['config']['APP']['mod_rewrite']){echo HTTP;}else{echo HTTP.'index.php/';} ?>";</script>
<script
	type="text/javascript" src="<?php echo $js_path;?>fk.js"></script>
<script
	type="text/javascript" src="<?php echo $js_path;?>custom.js"></script>
<script
	type="text/javascript"
	src="<?php echo $js_path;?>CODE/validate/jquery.validate.js"></script>
	<?php
	echo $GLOBALS['QRS']['js_links'];

}
function fk_css_v2(){
	echo $GLOBALS['QRS']['css_links'];
	#print all css links
}
function fk_js_v2(){
	$js_path = HTTP.'frontend/javascript/';

	?>
<script language="javascript" type="text/javascript"> var HTTP = "<?php echo HTTP?>"; var HTTP_FILE = "<?php if($GLOBALS['QRS']['config']['APP']['mod_rewrite']){echo HTTP;}else{echo HTTP.'index.php/';} ?>";</script>
<script
	type="text/javascript" src="<?php echo $js_path;?>fk.js"></script>
<script
	type="text/javascript" src="<?php echo $js_path;?>custom.js"></script>
<script
	type="text/javascript"
	src="<?php echo $js_path;?>CODE/validate/jquery.validate.js"></script>
	<?php
	echo $GLOBALS['QRS']['js_links'];

}
function fk_css(){

	$js_path = HTTP.'frontend/javascript/';
	echo $GLOBALS['QRS']['css_links'];
	?>
<link type="text/css"
	href="<?php echo $js_path;?>CODE/jquery-ui-1.10.0.custom/css/smoothness/jquery-ui-1.10.0.custom.min.css"
	rel="stylesheet" />
<style type="text/css" title="currentStyle">
@import "<?php echo $js_path;?>CODE/dataTables/media/css/demo_page.css";

@import
	"<?php echo $js_path;?>CODE/dataTables/media/css/demo_table_jui.css";
</style>
	<?php
	#print all css links
}

function fk_menu($IdMenu,$SelectedItem){
	define('MENU_'.$IdMenu.'',$SelectedItem);
}

function fk_page($var = '',$val){
	define('FK_PAGE_'.$var.'',$val);
}

function fk_document_title(){
	$title = (defined('DEFAULT_DOCUMENT_TITLE')) ? constant('DEFAULT_DOCUMENT_TITLE') : 'Untitled Document &raquo; By Quars &reg; 2017 ';
	if(defined('FK_PAGE_TITLE')){ $title = constant('FK_PAGE_TITLE'); }
	return $title;
}

function fk_document_description(){
	$d = '';
	if(defined('FK_PAGE_DESCRIPTION')){ $d = constant('FK_PAGE_DESCRIPTION'); }
	return $d;
}

function fk_document_keywords(){
	$d = '';
	if(defined('FK_PAGE_KEYWORDS')){ $d = constant('FK_PAGE_KEYWORDS'); }
	return $d;
}

function fk_select_option_value($SQL){

	Global $db;

	$db -> query($SQL);
	$OPTION = array();

	while($OPT = $db->next()){

		$OPTION = $OPT;
	}
	return $OPTION;

}
function fk_select_options($SQL,$SELECTED = NULL){

	Global $db;

	$rows = $db -> result_array($SQL);

	$OPTION = '';

	$sel = $SELECTED;

	foreach ($rows as $OPT) {


		if(is_array($SELECTED)){
			if(in_array($OPT[0],$SELECTED)){
				$sel=$OPT[0];
			}else{ $sel=''; }
		}
		$code = '';
		if(isset($OPT[2])){ $code = 'code="'.utf8_encode($OPT[2]).'"'; }

		if($OPT[0]==$sel){ $IS_SELECTED='selected="selected"';}else{$IS_SELECTED='';}
		$OPTION .= '<option value="'.$OPT[0].'" '.$code.' '.$IS_SELECTED.'>'.fk_str_format($OPT[1], 'html').'</option>';
	}

	return $OPTION;

}
/**
 * @desc Search field Object
 * */
function fk_search_field($id,$name,$value,$text_value,$formcode,$sql,$onclick=null,$cssExtra=''){

	$table = 'sfield-'.$formcode;

	$_SESSION['FK']['appform'][$table]['auto_search_field'][$id]['sql'] = $sql;
	$_SESSION['FK']['appform'][$table]['auto_search_field'][$id]['onclick'] = $onclick;

	$slq_elements = fk_get_query_elements($sql);
	$options = fk_select_text($slq_elements['table'], $slq_elements['fields'], $value);

	//pa($options);

	if($options[1]!=''){$text_value = $options[1];}



	$html_fld = '';

	$html_fld .='

			<input id="'.$id.'" name="'.$name.'" type="hidden" value="'.$value.'"  />
			<div class="input-group search-field">
				<input id="'.$id.'-2" name="'.$name.'-2" type="text" value="'.str_replace('"', '&quot;', $text_value).'" class="txt form-control '.$cssExtra.' searchbox" onblur="appForm_PopupSrc({id:\''.$id.'\',tbl:\''.$table.'\'})" />
                    <div class="input-group-btn" style="padding-bottom: 4px;">
                        <button tabindex="-1" class="btn btn-white" type="button" onclick="appForm_PopupSrc({id:\''.$id.'\',tbl:\''.$table.'\',forceOpen:true})" ><i class="fa fa-clone"></i></button>
                        <button tabindex="-1" data-toggle="dropdown" class="btn btn-danger dropdown-toggle" type="button">
                            <span class="caret"></span>
                        </button>
                        <ul role="menu" class="dropdown-menu pull-right">
                            <li><a href="javascript:void(0);" onclick="appForm_ClearPopupSrc({id:\''.$id.'\',tbl:\''.$table.'\'});">Limpiar</a></li>
                        </ul>
                    </div>
                </div>';
	$html_fld .='<div id="srcfld-rs-'.$id.'"></div>';

	$field_id_html = $id;
	// Autocomplete
	$enc_field_id_html = encode($field_id_html);

		$html_fld .='
	<script type="text/javascript">
	$(function(){
		var source'.$enc_field_id_html.' = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace("value"),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
		  url: HTTP+"FkMaster/autocompleteAppForm/'.$table.'/'.$field_id_html.'/?q=%QUERY",
		  wildcard: "%QUERY"
		}
	});
	$("#'.$field_id_html.'-2").keydown(function(e){
						if(e.keyCode==13){ e.preventDefault(); return false; }
	});
	$("#'.$field_id_html.'-2").typeahead(null, {
	  name: "best-pictures",
	  display: "value",
	  limit:10,
	  source: source'.$enc_field_id_html.',
	});
	});
	$("#'.$field_id_html.'-2").bind("typeahead:select", function(ev, suggestion) {
	  if(suggestion.id!=undefined){$("#'.$field_id_html.'").val(suggestion.id);
	  		var form = $("#'.$field_id_html.'-2").parents("form:eq(0)");
		  	var focusable = form.find("input,select,textarea").filter(":visible");
		  	next = focusable.eq(focusable.index(this)+1);
		  	console.log(next);
	        if (next.length) {
	            next.focus();
	        }
	  }
	});
	$(".tt-hint").removeClass("required");
	  </script>';
	//Autocomplete

	/*
	 $html_fld ='<input id="'.$id.'" name="'.$name.'" type="hidden" value="'.$value.'"  />';
	 $html_fld .='<input id="'.$id.'-2" name="'.$name.'-2" type="text" value="'.$text_value.'" class="txt searchbox '.$cssExtra.'" onblur="appForm_PopupSrc({id:\''.$id.'\',tbl:\''.$table.'\'})" />
	 <input type="button" id="'.$id.'-btn" value="&nbsp;" class="btn search2" onclick="appForm_PopupSrc({id:\''.$id.'\',tbl:\''.$table.'\',forceOpen:true})">
	 <input type="button" id="'.$id.'-btn" class="btn empty" value="&nbsp;" onclick="appForm_ClearPopupSrc({id:\''.$id.'\',tbl:\''.$table.'\'});">
	 ';
	 $html_fld .='<div id="srcfld-rs-'.$id.'"></div>';
	 */

	return $html_fld;

}
/**
 * @desc File field Object
 * */
function fk_file_field($id,$name,$value,$onclick=null,$cssExtra='',$mode='edit',$ops=[]){

	$html_fld = '';

	if(isset($ops['allowed_files'])){
		$_SESSION['fk_file_field'][$id]['allowed_files'] = $ops['allowed_files'];
	}

	if(isset($ops['attributes'])){
		$_SESSION['fk_file_field'][$id]['attributes'] = $ops['attributes'];
	}

	if($mode=='edit'){
		$html_fld .='<input id="'.$id.'" name="'.$name.'" type="hidden" value="'.$value.'" class="'.$cssExtra.'" />';
		$html_fld .='<br><iframe src="'.fk_link().'FkMaster/upolader/'.$id.'/" name="ifrmupl-'.$id.'" style="width:95%;height:45px;" frameborder="0"></iframe>';
	}

	$file_data = '';
	$ArUpl = new ActiveRecord('uploads');
	$totUpl = $ArUpl->find($value);

	if($totUpl==1){
		$folder = ($ArUpl->fields['folder']!='')?$ArUpl->fields['folder'].'/':'';


		if(strrpos($ArUpl->fields['tipo'], 'image')>-1){
			//image
			$file_data = '<a href="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" target="_blank"><img src="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" ></a>';
		}else{
			//Other file
			$file_data = '<a href="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" target="_blank">'.$ArUpl->fields['titulo'].'</a>';
		}

	}

	$html_fld .='<div id="ico-'.$id.'"><span>'.$file_data.'</span> <a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="if(confirm(\'Remover?\')){$(\'#'.$id.'\').val(\'\');$(\'#ico-'.$id.' span\').html(\'\');$(\'#'.$id.'\').change();}"><i class="fa fa-trash-o"></i></a></div>';

	return $html_fld;
}
function fk_get_image($id){

	$file_data = '';
	$ArUpl = new ActiveRecord('uploads');
	$totUpl = $ArUpl->find($id);

	return http_uploads().$ArUpl->fields['archivo'];

}
/**
 * @desc File field Object
 * */
function fk_date_field($id,$name,$value,$onclick=null,$cssExtra='',$mode='edit'){

	$monts[0] = 'Mes:';
	$monts[1] = 'Enero';
	$monts[2] = 'Febrero';
	$monts[3] = 'Marzo';
	$monts[4] = 'Abril';
	$monts[5] = 'Mayo';
	$monts[6] = 'Junio';
	$monts[7] = 'Julio';
	$monts[8] = 'Agosto';
	$monts[9] = 'Septiembre';
	$monts[10] = 'Octubre';
	$monts[11] = 'Noviembre';
	$monts[12] = 'Diciembre';

	$y = 0; $m=0; $d=0;
	$set_date=false;
	if($value!=''){
		$y = substr($value, 0,4);
		$m = substr($value, 5,2);
		$d = substr($value, 8,2);
		if(checkdate($m, $d, $y)){ $set_date=true; }
	}

	$html_fld = '';

	$html_fld .= '<div style="width:56px;display:inline-block;">

	              <select name="'.$name.'-d" id="'.$id.'-d" onchange="setDate(\''.$name.'\')" class="'.$cssExtra.'">';
	$html_fld .= '<option value="00">D&iacute;a:</option>';
	for($i=1;$i<32;$i++){
		$selected = '';
		if($set_date && $d==$i){ $selected = 'selected="selected"';}
		$html_fld .= '<option value="'.zerofill($i, 2).'" '.$selected.'>'.$i.'</option>';
	}
	$html_fld .= '</select></div>';

	$html_fld .= '<div style="width:100px;display:inline-block;"><select name="'.$name.'-m" id="'.$id.'-m" onchange="setDate(\''.$name.'\')" class="'.$cssExtra.'">';
	foreach($monts as $k=>$mnt){
		$selected = '';
		if($set_date && $m==$k){ $selected = 'selected="selected"';}
		$html_fld .= '<option value="'.zerofill($k,2).'" '.$selected.'>'.$mnt.'</option>';
	}
	$html_fld .= '</select></div>';
	$html_fld .= '<div style="width:75px;display:inline-block;"><select name="'.$name.'-y" id="'.$id.'-y" onchange="setDate(\''.$name.'\')" class="'.$cssExtra.'">';

	$Yini = date('Y');
	$Yfin = date('Y')-108;
	$html_fld .= '<option value="0000">A&ntilde;o:</option>';
	for($i=$Yini;$i>$Yfin;$i--){
		$selected = '';
		if($set_date && $y==$i){ $selected = 'selected="selected"';}
		$html_fld .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	}
	$html_fld .='</select><input type="hidden" name="'.$name.'" id="'.$id.'" value="'.$value.'" class="'.$cssExtra.'"></div>';





	return $html_fld;

} // End fk_date_field
/**
 * @desc Autocomplete
 * */
function fk_autocomplete($id,$name,$value,$text_value,$table,$sql,$onclick=null,$cssExtra=''){

	$_SESSION['FK']['appform'][$table]['auto_search_field'][$id]['sql'] = $sql;
	$_SESSION['FK']['appform'][$table]['auto_search_field'][$id]['onclick'] = $onclick;



	$html_fld ='<input id="'.$id.'" name="'.$name.'" type="hidden" value="'.$value.'"  />';
	$html_fld .='<input id="'.$id.'-2" name="'.$name.'-2" type="text" value="'.$text_value.'" class="txt searchbox '.$cssExtra.'" onblur="appForm_PopupSrc({id:\''.$id.'\',tbl:\''.$table.'\'})" />
	<input type="button" id="'.$id.'-btn" value="&nbsp;" class="btn search" onclick="appForm_PopupSrc({id:\''.$id.'\',tbl:\''.$table.'\',forceOpen:true})">
	<input type="button" id="'.$id.'-btn" class="btn empty" value="&nbsp;" onclick="appForm_ClearPopupSrc({id:\''.$id.'\',tbl:\''.$table.'\'});">
	';
	$html_fld .='<div id="srcfld-rs-'.$id.'"></div>';

	return $html_fld;

}

/**
 * @desc Autocomplete
 * */
/**
 * @desc Autocomplete v2
 * */
function fk_autocomplete_v2($id,$name,$value,$text_value,$table,$sql,$onclick=null,$cssExtra=''){
	//$field, $from_table, $from_field

	$_SESSION['FK']['appform'][$table]['auto_search_field'][$id]['sql'] = $sql;
	$_SESSION['FK']['appform'][$table]['auto_search_field'][$id]['onclick'] = $onclick;

	$html_fld  ='<input id="'.$id.'" name="'.$name.'" type="hidden" value="'.$value.'"  />';
	$html_fld .='<input id="f2-'.$id.'" name="f2-'.$name.'" type="text" value="'.$text_value.'" class="txt searchbox '.$cssExtra.'"  />';

	/*
	$html_fld .=  '<script>$( "#f2-'.$id.'" ).autocomplete({source: HTTP+"FkMaster/autocomplete2/'.encode($table).'/'.encode($id).'/",
	           select: function( event, ui ) {
                $("#f2-'.$id.'").val( ui.item.label );
                $("#'.$id.'").val( ui.item.id );
                $("#f2-'.$id.',#'.$id.'").change();
                return false;
            }});
            $("#f2-'.$id.'").keyup(function(e){
       if(e.which==8){ $("#'.$id.'").val("");  }
	});</script>';
	*/


	return $html_fld;

}

function fk_autocompleteV2($id,$name,$value,$text_value,$uniqcode,$sql,$attributes=array()){

	$_SESSION['FK']['appform'][$uniqcode]['auto_search_field'][$id]['sql'] = $sql;

	$url_code = encode($uniqcode.':'.$id);

	$attrs = '';
	foreach ($attributes as $k=>$v){
		$attrs .= ' '.$k.'="'.$v.'"';
	}

	$html_fld ='1<input id="'.$id.'" name="'.$name.'" autocomplete="off" value="'.$value.'" class="typeahead" type="text" '.$attrs.'>
<input id="'.$id.'-id" name="id-'.$name.'"	type="hidden" >
<script type="text/javascript">
$(function(){
      $("#'.$id.'").typeahead({
  	    source: function (query, process) {
  	    	$.ajax({
  	    		  type: "POST",
  	    		  url: HTTP+"FkMaster/autocomplete3/'.$url_code.'/",
  	    		  data: "&query="+query,
  	    		  dataType: "JSON",
  	    		  async:true,
  	    		  success:function(data){
  	    			  process(data);
  		    		}
  	    		});
  	    }
  	});
});
  </script>';


	return $html_fld;

}

function fk_autocompleteV3($id,$name,$value,$text_value,$uniqcode,$sql,$attributes=array()){

	$_SESSION['FK']['appform'][$uniqcode]['auto_search_field'][$id]['sql'] = $sql;

	$url_code = encode($uniqcode.':'.$id);

	$attrs = '';
	$class = '';
	foreach ($attributes as $k=>$v){
		if($k=='class'){
			$class = ' '.$v.' ';
		}else{
			$attrs .= ' '.$k.'="'.$v.'"';
		}

	}

	$html_fld ='
    <input id="'.$id.'" name="'.$name.'" value="'.$value.'" class="typeahead form-control '.$class.'" type="text" '.$attrs.' >
    <input id="id-'.$id.'" name="id-'.$name.'"	type="hidden" >
<script type="text/javascript">
$(function(){
	var bestPictures = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace("value"),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
	  url: HTTP+"FkMaster/autocomplete3/'.$url_code.'/?q=%QUERY",
	  wildcard: "%QUERY"
	}
});

$("#'.$id.'").typeahead(null, {
  name: "best-pictures",
  display: "value",
  limit:10,
  source: bestPictures,
});
});

$("#'.$id.'").bind("typeahead:select", function(ev, suggestion) {
  if(suggestion.id!=undefined){$("#id-'.$id.'").val(suggestion.id);}
});
$(".tt-hint").removeClass("required");
  </script>';


	return $html_fld;

}

function fk_autocomplete_select($id,$name,$value,$text_value,$uniqcode,$sql,$attributes=array()){

	$_SESSION['FK']['appform'][$uniqcode]['auto_search_field'][$id]['sql'] = $sql;

	$url_code = encode($uniqcode.':'.$id);

	$attrs = '';
	$class = '';
	foreach ($attributes as $k=>$v){
		if($k=="class"){
			$class .= ' '.$v.' ';
		}else{
			$attrs .= ' '.$k.'="'.$v.'"';
		}

	}

	$html_fld ='
    <input id="txt-'.$id.'" name="txt-'.$name.'" value="'.$text_value.'" class="typeahead form-control '.$class.' " type="text" '.$attrs.' >
    <input id="'.$id.'" name="'.$name.'" value="'.$value.'" class="hidFld '.$class.'" type="hidden" >
<script type="text/javascript">
$(function(){
	var bestPictures = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace("value"),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
	  url: HTTP+"FkMaster/autocomplete3/'.$url_code.'/?q=%QUERY",
	  wildcard: "%QUERY"
	}
});

$("#txt-'.$id.'").typeahead(null, {
  name: "best-pictures",
  display: "value",
  limit:60,
  source: bestPictures,
});
$("#txt-'.$id.'").change(function(){
	if($(this).val()==""){$("#'.$id.'").val("");$("#'.$id.'").change();}
});
});
$("#txt-'.$id.'").bind("typeahead:select", function(ev, suggestion) {
  if(suggestion.id!=undefined){$("#'.$id.'").val(suggestion.id); $("#'.$id.'").change();}
});
  </script>';

	return $html_fld;

}


/**
 * @desc SELECT t1.campo1,t2.campo2 FROM table1 t1, table2 t2
 WHERE t1.campo1 = "x"
 AND t1.id = "{ID}"
 *
 * */
function fk_select_complex_query($sql,$arr_data = array()){
	Global $db;

	$OPTION[0] = '';
	$OPTION[1] = '';

	foreach ($arr_data as $k=>$v){
		$sql =   str_replace('{'.$k.'}', $v, $sql);
	}

	$rs = $db -> result_array($sql);

	if (isset($rs[0])) {
		$OPTION[0] = isset($rs[0][0]) ? ($rs[0][0]):'';
		$OPTION[1] = isset($rs[0][1]) ? ($rs[0][1]):'';
	}

	return $OPTION;
}
function fk_select_text($table,$fields,$id_selected){

	Global $db;

	$OPTION[0] = '';
	$OPTION[1] = '';

	$table_ar = trim($table);
	$table_ar = explode(' ', $table_ar);
	$table_ar = $table_ar[0];

	$rec = new ActiveRecord($table_ar);

	$WHERE = ' WHERE '.$rec-> id_field_name.' = "'.$id_selected.'" ';


	$SQL = 'SELECT '.$fields.' FROM '.$table.' '.$WHERE ;

	$opt = $db -> result_array($SQL);

	if(isset($opt[0])){
		$OPTION[0] = ($opt[0][0]);
		$OPTION[1] = ($opt[0][1]);
	}



	return $OPTION;

}
function fk_get_query_elements($sql_query){

	$sql_query = trim($sql_query);
	$sql_query = str_replace(' ', ':|:', $sql_query);
	$sql_query = str_replace(';', '', $sql_query);

	$xp = explode(':|:', $sql_query);

	$nw_sql='';
	foreach ($xp as $k => $v){
		$v =trim($v);
		if($v!=''){
			if($v=='select' || $v=='from'|| $v=='where'){ $v=strtoupper($v);}
			$nw_sql .= ' '.$v;
		}
	}
	$nw_sql = trim($nw_sql);

	//Table
	$ex_table = explode('FROM', $nw_sql);
	$table = trim($ex_table[1]);


	//fields
	$ex_fld = explode('SELECT', $ex_table[0]);
	$fields = trim($ex_fld[1]);

	//where
	$ex_whe = explode('WHERE', $nw_sql);
	$where = isset($ex_whe[1])?trim($ex_whe[1]):'';

	$table = str_replace(array($where,'WHERE'), ' ', $table);
	$table = trim($table);


	$ARR['table']=$table;
	$ARR['fields']=$fields;
	$ARR['where']=$where;

	return $ARR;
}

function fk_select_options_r($array,$SELECTED = NULL){

	$OPTION = '';
	foreach($array as $k=>$v){
		if($k==$SELECTED){ $IS_SELECTED='selected="selected"';}else{$IS_SELECTED='';}
		$OPTION .= '<option value="'.$k.'" '.$IS_SELECTED.'>'.$v.'</option>';
	}
	return $OPTION;

}


/**
 * @desc Creates a file
 * */
function fk_create_file($file_name,$file_path,$file_content){

	$fh = fopen($file_path.$file_name, 'w') or die("can't open file");
	fwrite($fh, $file_content);
	fclose($fh);

	return true;


}

/**
 * @desc Reads a file
 * */
function fk_read_file($file_path){

	// $file = fopen($file_path, "r") or die("Unable to open file!");
	// $file_content = fgets($file);
	// fclose($file);

	// return $file_content;

	return file_get_contents($file_path);


}
/**
 * Converts string to a timestamp, Format Required: Y-m-d H:i:s
 * @param string $fecha
 * @return int
 */
function fk_convert_to_timestamp($fecha){

	//defino fecha 1
	$fecha_xpl = explode(' ',$fecha);
	$fecha_tmp = $fecha_xpl[0];
	$hora_tmp = $fecha_xpl[1];
	$fecha_1 = explode('-',$fecha_tmp);
	$hora_1 = explode(':',$hora_tmp);

	$ano1 = isset($fecha_1[0])?$fecha_1[0]:0;
	$mes1 = isset($fecha_1[1])?$fecha_1[1]:0;
	$dia1 = isset($fecha_1[2])?$fecha_1[2]:0;
	$hor1 = isset($hora_1[0])?$hora_1[0]:0;
	$min1 = isset($hora_1[1])?$hora_1[1]:0;
	$sec1 = isset($hora_1[2])?$hora_1[2]:0;

	$timestamp = NULL;
	if($ano1>0){
	 $timestamp = mktime($hor1,$min1,$sec1,$mes1,$dia1,$ano1);
	}


	return $timestamp;

}

function fk_lapse_of_time($fecha1,$fecha2,$RETURN_JUST_TIME=false,$in_seconds=false,$abs=true){


	//calculo timestamp de las dos fechas
	$timestamp1 = fk_convert_to_timestamp($fecha1);
	$timestamp2 = fk_convert_to_timestamp($fecha2);

	//resto a una fecha la otra
	$segundos_diferencia = $timestamp2 - $timestamp1;
	if($abs==true){
		$segundos_diferencia = abs($segundos_diferencia);// Val Absoluto
	}


	//convierto segundos en minutos
	$minutos_diferencia = floor($segundos_diferencia / (60));

	//convierto segundos en horas
	$horas_diferencia = floor($segundos_diferencia / (60 * 60));

	//convierto segundos en dias
	$dias_diferencia = floor($segundos_diferencia / (60 * 60 * 24));

	//convierto segundos en meses
	$meses_diferencia = floor($segundos_diferencia / (60 * 60 * 24 * 30));

	//DEVOLVER RESULTADO
	if($segundos_diferencia < 60){
		//Segs
		$rs = $segundos_diferencia.' Segundo'.fk_plural($minutos_diferencia,'s','');
	}elseif($minutos_diferencia < 60){
		//Mins
		$rs = $minutos_diferencia.' Minuto'.fk_plural($minutos_diferencia,'s','');

	}elseif($horas_diferencia < 24){
		//Horas
		$rs = $horas_diferencia.' Hora'.fk_plural($horas_diferencia,'s','');
	}elseif($dias_diferencia < 30){
		//Dias
		$rs = $dias_diferencia.' Dia'.fk_plural($dias_diferencia,'s','');
	}else{
		//Meses
		$rs = $meses_diferencia.' Mes'.fk_plural($meses_diferencia,'es','');

	}
	//DEVOLVER VALOR O ENVIAR SEGUNDOS DE DIFERENCIA
	if($RETURN_JUST_TIME==TRUE){
		if($in_seconds){
			return $segundos_diferencia;
		}else{
			return $rs;
		}

	}else{
		echo $rs;
	}


} // end fk_lapse_of_time
function fk_pluralize_one($word){
	$last_word = substr($word, -1);
	$arrFind = array("a","e","i","o","u");

	if(in_array($last_word,$arrFind)){
		return $word."s";
	}else{
		return $word."es";
	}
}
function fk_pluralize($word){

	$words = explode(' ',$word);
	$rs = '';
	foreach($words as $w){
		$rs .= ' '.fk_pluralize_one($w);
	}

	return $rs;





} // end fk_pluralize

function fk_plural($tot,$plural,$singular = ''){
	$rs = '';
	if($tot==1){ $rs = $singular;}else{$rs = $plural;}
	return $rs;

} // end fk_plural

// FUNCION LENGUAGE
function __($str){

	if(!defined($str)){
		return fk_str_format($str, 'html:no-tags');
	}else{
		return fk_str_format(constant($str), 'html:no-tags');
	}

}

function fk_download_file($file,$file_name = null, $use_sys_path = TRUE,$file_type = 'application/octet-stream'){

	// Dar formato al nombre del archivo a descargar
	$file_to_download = ($file_name!=NULL)?$file_name:$file;


	if($file_name==null){
		$pos = strrpos($file, "/");
		if($pos!=''){
			$file_to_download = substr($file, $pos+1,strlen($file));
		}

	}

	if($use_sys_path==true){
		$f = SYSTEM_PATH.$file;
	}else{ $f = $file; }


	header("Content-type: ".$file_type);
	header("Content-Disposition: attachment; filename=\"$file_to_download\"\n");
	$fp = fopen("$f","r");
	fpassthru($fp);
}

function fk_export_excel($content,$filename,$phpexcel_rendering = false){

	if($phpexcel_rendering){
		//Export as real excel file
		$content = str_replace('&', '', $content);
		$content = '<?xml version="1.0" encoding="UTF-8"?><data>'.$content.'</data>';

		if(!class_exists('PHPExcel')){
			load::library('PHPExcel-1.8/Classes/PHPExcel');
		}
		$xml = simplexml_load_string ( $content );
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Apkube Software")
							 ->setLastModifiedBy("Apkube Software")
							 ->setTitle($filename)
							 ->setSubject($filename)
							 ->setDescription($filename." Auto generated by Apkube Software")
							 ->setKeywords("Apkube Data import")
							 ->setCategory("Data Import");
		// Excel col,row
		$col = 'A';
		$row = 1;
		$debug = false;

		if(isset($xml->table->thead)){
		//echo 'thead';
			$tot = count($xml->table->thead->tr->th);
			// Reset col
			$row = '1';
			for ($i=0; $i < $tot; $i++) {
				$xml->table->thead->tr->th[$i];

				if($debug){ echo $col.$row; }
				$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue($col.$row, $xml->table->thead->tr->th[$i]);
		         $col++;

			}
			$row++;
			if($debug){ echo '<br>'; }
		}

		if(isset($xml->table->tbody)){
			// Rows
			foreach ($xml->table->tbody->tr as $d_row) {
				$col = 'A';
				$tot = count($d_row);
				for ($i=0; $i < $tot; $i++) {
					if($debug){ echo $col.$row; }
					$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue($col.$row, $d_row->td[$i]);
			         $col++;
				}
				$row++;
				if($debug){ echo '<br>'; }
			}
		}
		if($debug){ die(); }
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Apkube Hoja 1');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');


	}else{
		//Default export: html table as excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=$filename.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $content;

	}

}
function fk_memory_usage(){
	$size=memory_get_usage(true);
	return fk_size_format($size);


}

function fk_size_format($size){
	$unit=array('B','KB','MB','GB','TB','PB');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
function fk_ok_message_dialog($Message,$AutoClose = TRUE){

	$id = 'fk-msg-dlg'.encode(rand('10000000','10000000000'));

	$x = '<script>
	$(function() {
		var dlg = $( "#'.$id.'" ).dialog({
			autoOpen: true,
			buttons: {
				"Aceptar": function() {
				   $( this ).dialog( "close" );
				}
			}
	    });
	});';
	if($AutoClose==TRUE){
		$x .='setTimeout("$( \'#'.$id.'\' ).dialog(\'close\')",3000);';
	}
	$x.='</script><div id="'.$id.'" title="Mensaje" ><div class="fk-ok-message">'.OK_ICON.$Message.'</div></div>';

	return $x;

}
function fk_ok_message($Message,$AutoHide = TRUE){
	$id = 'fk-message-'.encode(rand('10000000','10000000000'));

	$x = '<div id="'.$id.'" class="alert alert-success">'.OK_ICON.__($Message).'</div>';
	if($AutoHide==TRUE){
		$x .= '<script>setTimeout(function(){$("#'.$id.'").hide(400);},2000);</script>';
	}
	return $x;
}
function fk_alert_message_dialog($Message,$AutoClose = TRUE){

	$id = 'fk-msg-dlg'.encode(rand('10000000','10000000000'));

	$x = '<script>
	$(function() {
		var dlg = $( "#'.$id.'" ).dialog({
			autoOpen: true,
			buttons: {
				"Aceptar": function() {
				   $( this ).dialog( "close" );
				}
			}
	    });
	});';
	if($AutoClose==TRUE){
		$x .='setTimeout("$( \'#'.$id.'\' ).dialog(\'close\')",3000);';
	}
	$x.='</script><div id="'.$id.'" title="Alerta" ><div class="fk-alert-message">'.ALERT_ICON.__($Message).'</div></div>';

	return $x;

}

function fk_alert_message($Message,$AutoClose = TRUE){
	$id = 'fk-message-'.encode(rand('10000000','10000000000'));
	$x = '<div id="'.$id.'" class="alert alert-danger">'.ALERT_ICON.' '.__($Message).'</div>';

	if($AutoClose==TRUE){
		$x .='<script>setTimeout(function(){$("#'.$id.'").hide(400);},2000);</script>';
	}

	return $x;
}

/**
 *
 * fk_message
 * @desc Displays message type: ok,info,warning,error
 * @param $type
 * @param $Message
 * @param  $AutoHide
 */
function fk_message($type,$Message,$AutoHide = FALSE){

	if(php_sapi_name() === 'cli'){
		$x = $Message.PHP_EOL;
	}else{
		$id = 'fk-message-'.encode(rand('10000000','10000000000'));

		$icon = '<span style="float: left; margin-right: 0.3em;"  class="ui-icon ui-icon-info"></span>';
		if($type=='ok'){ $type= 'success'; $icon = '<span style="float: left; margin-right: 0.3em;"  class="ui-icon ui-icon-circle-check"></span>';}
		if($type=='error' || $type=='alert'){$type= 'danger'; $icon = '<span style="float: left; margin-right: 0.3em;"  class="ui-icon ui-icon-alert"></span>';}
		if($type=='info'){$icon = '<span style="float: left; margin-right: 0.3em;"  class="ui-icon ui-icon-info"></span>';}
		if($type=='warning'){$icon = '<span style="float: left; margin-right: 0.3em;"  class="ui-icon ui-icon-notice"></span>';}


		$x = '<div id="'.$id.'" class="alert alert-'.$type.'"><button data-dismiss="alert" class="close close-sm" type="button">
	                                    <i class="fa fa-times"></i>
	                                </button>'.$icon.__($Message).'</div>';
		if($AutoHide==TRUE){
			$x .= '<script>setTimeout(function(){$("#'.$id.'").hide(400);},2000);</script>';
		}
	}

	return $x;
}

function fk_get($val){
	if(isset($_GET[$val])){
		if(is_array($_GET[$val])){ return $_GET[$val]; }else{return utf8_decode($_GET[$val]);}
	}else{ return '';}
}

function fk_post($val){
	if(isset($_POST[$val])){
		if(is_array($_POST[$val])){ return $_POST[$val]; }else{return utf8_decode($_POST[$val]);}
	}else{ return '';}
}

function fk_form_var($val,$type='',$url_p=''){
	$rs = '';



	if(isset($_GET['fk_url_load'])){
		 $url = str_replace('/', '', $_GET['fk_url_load']);
	}else{
		 $url = str_replace('/', '', fk_get('url'));
	}

	if($url_p!=''){$url = str_replace('/', '', $url_p);}

	//echo $url;
	//echo '<br>';
	$url = str_replace('index', '', $url);
	$url = encode($url);

	$get_or_post = true;

	$conf_code = 'FORMVAL:'.$_SESSION['id_usuario'].':'.$url.':'.$val;


	// get or post first
	if(isset($_POST[$val])){
		$rs =  $_POST[$val];
		//$_SESSION['formvars'][$url][$val] = $rs;
		\App\Models\configSysModel::updateConfigSys($conf_code, $rs);
	}elseif(isset($_GET[$val])){
		$rs =  $_GET[$val];
		//$_SESSION['formvars'][$url][$val] = $rs;
		\App\Models\configSysModel::updateConfigSys($conf_code, $rs);
	}else{
		$get_or_post = false;
	}

	// type exceptions
	if($type=='checkbox' && $get_or_post==false){
		//$_SESSION['formvars'][$url][$val] = '';
		\App\Models\configSysModel::updateConfigSys($conf_code, '');
	}


	if($get_or_post==false){

		$rs = \App\Models\configSysModel::getConfigSys($conf_code);
	}

	//echo $conf_code.' ';
	//echo $rs.'<br>';


	return $rs;

}
/**
 *
 * html_input_sanitize
 * @desc Sanitiza valor devuelto por fk_form_var() para mostrarlo dentro de un <input value="{valor-sanitizado}">
 * @param $str  el valor ej. fk_form_var('string')
 *
 */
function html_input_sanitize($str){
	return utf8_decode(htmlspecialchars($str,ENT_QUOTES));
}

/**
 * @deprecated Usar fk_form_var es la version mas actual
 */
function fk_form_varV1($val,$type=''){
	$rs = '';


	$url = str_replace('/', '', fk_get('url'));
	$url = str_replace('index', '', $url);
	$url = encode($url);

	$get_or_post = true;

	// get or post first
	if(isset($_POST[$val])){
		$rs =  $_POST[$val];
		$_SESSION['formvars'][$url][$val] = $rs;
	}elseif(isset($_GET[$val])){
		$rs =  $_GET[$val];
		$_SESSION['formvars'][$url][$val] = $rs;
	}else{
		$get_or_post = false;
	}

	// type exceptions
	if($type=='checkbox' && $get_or_post==false){
		$_SESSION['formvars'][$url][$val] = '';
	}


	if($get_or_post==false){
		if(isset($_SESSION['formvars'][$url][$val])){
			$rs = $_SESSION['formvars'][$url][$val];
		}
	}


	return $rs;

}

function fk_post_get($val){

	if(isset($_POST[$val])){
		if(is_array($_POST[$val])){ return $_POST[$val]; }else{return utf8_decode($_POST[$val]);}
	}elseif(isset($_GET[$val])){
		if(is_array($_GET[$val])){ return $_GET[$val]; }else{return utf8_decode($_GET[$val]);}
	}else{return '';}

}

function fk_count_empty_fields($arr,$method = 'POST'){
	$method = strtoupper($method);
	$err_cnt = 0;
	foreach ($arr as $v){
		if($method=='POST'){
			if(!isset($_POST[$v]) || trim(fk_post($v))==''){	$err_cnt++;	}
		}else{
			if(!isset($_GET[$v]) || trim(fk_post($v))==''){	$err_cnt++;	}
		}
	}

	return $err_cnt;
}


function fk_even_odd($cnt){
	if($cnt%2==0){
		$rs = 'even';
	}else{$rs = 'odd';}
	return $rs ;
}

function fk_unique_code($length = 8){
	$code = md5(uniqid(rand(), true));
	if ($length != "") return substr($code, 0, $length);
	else return $code;
}

function fk_formated_date($fecha){
	return getFormatedDate($fecha);
}

function getFormatedDate($fecha){

	// recibe formato "YYYY-mm-dd" (Y-m-d)
	//echo $fecha;
	//echo '<hr>';

	if(trim($fecha)!='' && trim($fecha)!= '0000-00-00' ){

		$dia=substr($fecha,8,2);
		//echo '<hr>';
		$mes=substr($fecha,5,2);
		//echo '<hr>';
		$agno=substr($fecha,0,4);
		//echo '<hr>';
		setlocale(LC_ALL,"es_CL");
		$loc = setlocale(LC_TIME, NULL);



		if ($mes=="01") $xmes=__('Enero');
		if ($mes=="02") $xmes=__('Febrero');
		if ($mes=="03") $xmes=__('Marzo');
		if ($mes=="04") $xmes=__('Abril');
		if ($mes=="05") $xmes=__('Mayo');
		if ($mes=="06") $xmes=__('Junio');
		if ($mes=="07") $xmes=__('Julio');
		if ($mes=="08") $xmes=__('Agosto');
		if ($mes=="09") $xmes=__('Septiembre');
		if ($mes=="10") $xmes=__('Octubre');
		if ($mes=="11") $xmes=__('Noviembre');
		if ($mes=="12") $xmes=__('Diciembre');

		$xmes = substr($xmes,0,3);

		//return  $fecha= strftime("%d de %B del %Y",mktime(0,0,0,$mes,$dia,$agno));

		return  $fecha= strftime("  ".$xmes." %d, %Y",mktime(0,0,0,$mes,$dia,$agno));
	}else{
		return  '';
	}

}
function fk_header_location_js($link){
	//return '<script>$(document).ready(function(){location.href="'.$link.'"});</script>';
	return '<script>location.href="'.$link.'";</script>';
}
/**
 *
 * @desc       Encode value into base64 string
 * @since      0.1 Beta
 */
// encode codifica los caracteres en base64
function encode($v){

	return trim(base64_encode($v),'=');

}
/**
 *
 * @desc       Decode base64 string
 * @since      0.1 Beta
 */
// encode decodifica los caracteres que estan en base64
function decode($v){

	return base64_decode($v);
}

function fk_json_response($data){

	if(!isset($data['js'])){$data['js']= '';}

	return json_encode($data);
}
function fk_define($name,$value){
	if(!defined($name)){ define($name, $value);}
}

function fk_pdf($html,$return=false,$args=array()){

	Load::library('dompdf/dompdf_config.inc');

		if ( isset( $html )  ) {

		  if ( get_magic_quotes_gpc() )
		    $html = stripslashes($html);

		   $orientation = 'portrait';
		   if(isset($args['orientation'])){ $orientation = $args['orientation']; }

		  $dompdf = new DOMPDF();
		  $dompdf->load_html($html);
		  $dompdf->set_paper('letter', $orientation);
		  $dompdf->render();

		  if($return){
		  	return $dompdf->output();
		  }else{
		  	// send stream to screen
		  	$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
			exit(0);

		  }




		}

}

function getRealPOST() {
    $pairs = explode("&", file_get_contents("php://input"));
    $vars = array();
    foreach ($pairs as $pair) {
        $nv = explode("=", $pair);

        $name = urldecode($nv[0]);
        $value = isset($nv[1])?urldecode($nv[1]):'';
        if(trim($name)!=''){
			$vars[$name] = $value;
        }

    }
    return $vars;
}

function is_connected_to_internet($url='www.google.com'){
	if(!$sock = @fsockopen($url, 80))
	{
		return false;
	}else{
		return true;
	}
}

function fk_barcode($text,$width=150){
	if(trim($text)!=''){
		$link = fk_link().'frontend/plugins/barcodegen/html/image.php?filetype=PNG&dpi=300&scale=2&rotation=0&font_family=Arial.ttf&font_size=10&text='.$text.'&thickness=30&start=NULL&code=BCGcode128';
	    return '<img src="'.$link.'" style="width:'.$width.'px;" >';
	}else{
		return '';
	}

}

function fk_utf8_parser($array){
	// Utf8 encodder
	array_walk_recursive($array, function(&$item, $key){
		if(!mb_detect_encoding($item, 'utf-8', true)){
			$item = utf8_encode($item);
		}
	});

return $array;
}

function http_uploads(){
	return HTTP_UPLOADS;
}

function app_is_on_production(){

	if(app_running_is('on_production',true)){
		return true;
	}else{
		return false;
	}
}

function app_is_on_development(){
	if(app_running_is('on_dev',true)){
		return true;
	}else{
		return false;
	}
}


function app_is_on_test(){
	if(app_running_is('on_test',true)){
		return true;
	}else{
		return false;
	}
}

function app_url_is($url){
	$url_conf = $GLOBALS['QRS']['RUNNING']['app']['www_server'];
	if($url==$url_conf){
		return true;
	}else{
		return false;
	}
}
function app_running_config($var){

	if(isset($GLOBALS['QRS']['RUNNING']['app'][$var])){
		return $GLOBALS['QRS']['RUNNING']['app'][$var];
	}else{
		return '';
	}

}
function app_is_hosted_on_internet(){

	if(app_running_is('on_internet',true)){
		return true;
	}else{
		return false;
	}

}
function uploads_directory(){
	return UPLOADS_DIRECTORY;
}



/**
 * @desc devuelve enlace back similar a history.go(-1) en javascript
 * */
function go_back($default_link = NULL){

	$default_link = ($default_link == NULL) ? fk_link() : fk_link().$default_link ;
	$serv_refer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '';
	$url_refer = get_domain_url($serv_refer);
	$url_this_site = get_domain_url(fk_link());
	$link_refer = ($url_this_site==$url_refer) ? $_SERVER['HTTP_REFERER'] : $default_link;
	return $link_refer;
}

/**
 * @desc genera un transaction key
 * */
function setTransactionKey(){

	$t_key = uniqid();
	$_SESSION['TRANSACTION_KEY'] = $t_key;
	return $t_key;
}
/**
 * @desc comprueba transaction key generado en la ultima operacion
 * */
function confirmTransactionKey($t_key){

	$TransactionKey = isset($_SESSION['TRANSACTION_KEY'])?$_SESSION['TRANSACTION_KEY']:NULL;

	if($t_key==$TransactionKey && $TransactionKey!=NULL ){
		return true;
	}else{
		return false;
	}
}

function get_domain_url($url){
	$arrSrc = array('http://www.','https://www.','http://','https://');
	$url = str_replace($arrSrc, '', $url);
	$url_1 = explode('/', $url);
	$url = $url_1[0];
	return $url;
}

/**
 * @desc verifica la http url y el tipo de navegador y envia a mobil o al descktop
 * */
function verify_http_path(){

	$domain_url = get_domain_url(fk_link());
	$req_host = $_SERVER['HTTP_HOST'];

	if($req_host!=$domain_url){
		header('Location:'.fk_link());
	}

}

function get_defined_url($code){
	if(app_is_hosted_on_internet()){
		return constant('www_'.$code);
	}else{
		return constant('loc_'.$code);
	}
}
function google_verification(){
	if(app_is_hosted_on_internet()){
		?>
<meta
	name="google-site-verification"
	content="1s-3KIfA7rj5Gs6EJx0QXhodRREr3z5hetX4HpF8rW4" />
		<?php
	}

}

function google_analytics(){
	if(app_is_hosted_on_internet()){

		?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-3858434-15']);
  _gaq.push(['_setDomainName', 'apkub.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
		<?php

	}


}

function zerofill($entero, $largo){

	$relleno = '';

	if (strlen($entero) < $largo) {$relleno = str_repeat('0', $largo-strlen($entero));}
	return $relleno . $entero;
}

function codigo_referencia_banco($code){

	$code = zerofill($code, 4);
	$code_ref = 'EK-'.$code;

	return $code_ref;

}

function format_time($t,$f=':') // t = seconds, f = separator
{
	return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}

function app_config_is($var,$value){
	$res = false;
	if(isset($GLOBALS['QRS']['config']['APP'][$var])){
		if($GLOBALS['QRS']['config']['APP'][$var] == $value){
			$res=true;
		}
	}

	return $res;

}

function app_running_is($var,$value){

	$res = false;
	if(isset($GLOBALS['QRS']['RUNNING']['app'][$var])){
		if($GLOBALS['QRS']['RUNNING']['app'][$var] == $value){
			$res=true;
		}
	}

	return $res;

}

function userTimezone($date){

	//Forma de uso
	//$fecha = userTimezone('Y-m-d H:i:s');
	//echo $fecha->format(' Y-m-d H:i:s P');

    	// Create date in default Datetime Zone : Format Y-m-d H:i:s '2015-07-14 14:18:01'
    	$DateTime = new DateTime($date);

		// Change to user Timezone
		if(isset($_SESSION['timezone'])){
			if(trim($_SESSION['timezone'])!=''){
				$DateTime->setTimezone(new DateTimeZone($_SESSION['timezone']));
			}
		}

		//echo $DateTime->format('Y-m-d H:i:sP');
		return $DateTime;
}

function encrypt($cadena){
    $key='hola97971';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
    $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
    return $encrypted; //Devuelve el string encriptado
 
}
 
function decrypt($cadena){
     $key='hola97971';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
     $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    return $decrypted;  //Devuelve el string desencriptado
}