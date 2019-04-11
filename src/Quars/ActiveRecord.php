<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
namespace Quars;

global $db;
if (!$db) {  \Quars\Load::database(); }

class ActiveRecord{

	public  $fields = array();
	public  $form_fields = array();
	public  $num_rows = 0;
	public  $table = '';
	public  $id_field_name = '';
	public  $db_type;  // db_type, definido por defecto en config/config.ini
	private $db_obj; // Database de acuerdo a db_type
	private $FindMode = ''; // Find mode: [first|last|next|prev]
	private $SqlWhere = ''; // SQL WHERE
	public $SqlAnd = ''; // SQL AND
	public $field_mode = 'view-edit'; // update | view-edit
	public $error_code = ''; // database error code
	public $useHtmlEntities_onField = true; // display field results with htmlentities()
	public $prefix_field = ''; // Prefix field
	private $inserted_id = null;


	/**
	 * @package db_record :: Active Record
	 * @method  __construct()
	 * @since v0.1 beta
	 * */
	public function __construct($table = '',$id_field_name='',$p_db_type = NULL){

		//------------------------------------------
		//create db object
		//------------------------------------------
		if( $p_db_type != NULL ){
			$this->db_type = $p_db_type;
		}else{
			$this->db_type = $GLOBALS['QRS']['RUNNING']['db']['db_type'];
		}
		// $this->db_obj = new db($this->db_type);
		// $this->db_obj->connect();
		Global $db;
		$this->db_obj = $db;

	 //------------------------------------------
	 //inicializar valores de objeto
	 //------------------------------------------

	 // TABLE
	 if($table==''){
	 	$this->table = $this->get_table();
	 }else{$this->table = $table;}


	 // obtiene la estructura de la tabla si no esta definida
	 if(count($this->form_fields)==0){
	 	self::describe_table($this->table);	
	 }
	 //self::describe_table($this->table);	
	 
	  
	 // obtener id_field_name
	 if($this->id_field_name == ''){
	 	$this->id_field_name = ($id_field_name =='') ? $this->get_id_table() : $id_field_name ;
	 }
	 



	 //fields
	 $this->fields[$this->id_field_name] = 0;

	}

	function setDateformat_auto_convert($bolean){
		//$this->dateformat_auto_convert = $bolean;
		//$this->db_obj->dateformat_auto_convert = 1000;

		$this->db_obj->setDateformat_auto_convert($bolean);

		//print_r($this->db_obj);
		//die();

	}

	/**
	 *@package db_record
	 *@method __destruct()
	 *@since v0.1 beta
	 * */
	public function __destruct(){
		//------------------------------------------
	 //destruc db object
		//------------------------------------------
	 //$this->db_obj->close();

	}
	/**
	 *@package db_record
	 *@method insert()
	 *@desc inserts a record
	 *@since v0.1 beta
	 * */
	public function insert() {


		// Insert execution of database type engine (ie. MySql, Firebird, Oracle)
		$rs = $this->db_obj->insert($this->table,$this->fields,$this->id_field_name,$this-> form_fields);

		if($rs==FALSE){
			$this->error_code = $this->db_obj->error_code;
		}else{

			if(app_running_is('sync_enabled', true)){
				$this->inserted_id = $this->db_obj->inserted_id();
				// Save qry
				//echo $this->db_obj->db_obj->sql_query;
				 $sync_qry = 'INSERT INTO  `db_sync` ( `id_sync` ,`sync_date` ,`qry`)
							VALUES (NULL ,  "'.date('Y-m-d H:i:s').'",  "'.encode($this->db_obj->db_obj->sql_query).'" )';
				 $this->db_obj->db_obj->query($sync_qry);
			}
		}

		return $rs;


	}
	/**
	 *@package db_record
	 *@method update()
	 *@desc updates a record
	 *@since v0.1 beta
	 * */
	public function update() {


		// update from database type engine (ie. MySql, Firebird, Oracle)

		$rs = $this->db_obj->update($this->table,$this->fields,$this->id_field_name,$this-> form_fields);
		
		

		if($rs==FALSE){
			$this->error_code = $this->db_obj->error_code;
		}else{
			if(app_running_is('sync_enabled', true)){

				// Save qry
				//echo $this->db_obj->db_obj->sql_query;
				 $sync_qry = 'INSERT INTO  `db_sync` ( `id_sync` ,`sync_date` ,`qry`)
							VALUES (NULL ,  "'.date('Y-m-d H:i:s').'",  "'.encode($this->db_obj->db_obj->sql_query).'" )';
				 $this->db_obj->db_obj->query($sync_qry);
			}
		}

		return $rs;

	}
	/**
	 *@package db_record
	 *@method delete()
	 *@desc deletes a record
	 *@since v0.1 beta
	 * */
	public function delete() {


		$WHERE = ' WHERE '.$this->id_field_name." = '".$this->fields[$this->id_field_name]."' ";

		$sql = 'DELETE FROM '.$this->table.' '.$WHERE;


		$rs = $this->db_obj->query($sql);

		if($rs==FALSE){
			$this->error_code = $this->db_obj->error_code;
		}else{
			if(app_running_is('sync_enabled', true)){
				// Save qry
				//echo $this->db_obj->db_obj->sql_query;
				 $sync_qry = 'INSERT INTO  `db_sync` ( `id_sync` ,`sync_date` ,`qry`)
							VALUES (NULL ,  "'.date('Y-m-d H:i:s').'",  "'.encode($this->db_obj->db_obj->sql_query).'" )';
				 $this->db_obj->db_obj->query($sync_qry);
			}
		}

		return $rs;
	}
	/**
	 *@package db_record
	 *@method save()
	 *@desc inserts or updates a record from a given Id_record;
	 *      if $this->fields[id_record] is set to 0 then Inserts else Updates
	 *@since v0.1 beta
	 * */
	public function save() {
			
	 if($this->fields[$this->id_field_name]==0 ){
	 	$this->insert();
	 }else{$this->update();}

	  
	}
	/**
	 *@package db_record
	 *@method describe_table()
	 *@desc describes a table
	 *@since v0.1 beta
	 * */
	public function describe_table(){
		return $this->form_fields = $this->db_obj->describe_table($this->table);
	} // function describe_table()

	/**
	 *@package db_record
	 *@method get_fields()
	 *@desc populates $this->fields array with the record fields
	 *@since v0.1 beta
	 * */
	public function get_fields(){
		$AND = '';
		switch ($this->FindMode) {
			case 'first':
				if(!empty($this->SqlAnd)){
					$AND = 'WHERE 1=1 '.$this->SqlAnd;
				}
				$WHERE = ' '.$AND.'  LIMIT 2 ';
				break;
			case 'prev':
				if(!empty($this->SqlAnd)){
					$AND = $this->SqlAnd;
				}
				$WHERE = ' WHERE '.$this->id_field_name.' < "'.$this->fields[$this->id_field_name].'" '.$AND.' ORDER BY '.$this->id_field_name.' DESC LIMIT 2 ';
				break;
			case 'next':
				if(!empty($this->SqlAnd)){
					$AND = $this->SqlAnd;
				}
				$WHERE = ' WHERE '.$this->id_field_name.' > "'.$this->fields[$this->id_field_name].'" '.$AND.' LIMIT 2 ';
				break;
			case 'last':
				if(!empty($this->SqlAnd)){
					$AND = 'WHERE 1=1 '.$this->SqlAnd;
				}
				$WHERE = ' '.$AND.' ORDER BY '.$this->id_field_name.' DESC LIMIT 2 ';
				break;
			case 'where':
				$WHERE = $this->SqlWhere.' '.$this->SqlAnd;
				break;

			default:
				if(!empty($this->SqlAnd)){
					$AND = $this->SqlAnd;
				}
				$WHERE = ' WHERE '.$this->id_field_name.' = \''.$this->fields[$this->id_field_name].'\' '.$AND;
				break;
		}
			
		$sql = 'SELECT * FROM '.$this->table.' '.$WHERE;
			

		$this->db_obj->query_assoc($sql);
		$this->fields = array(); // Clear Array

		// Return fist result
		if($record = $this->db_obj->next()){
			$this->fields = $record;
		}
		// Set total rows
		$this-> num_rows = $this->db_obj-> num_rows() ;


			
			
			
	} // function get()

	/**
	 *@package db_record
	 *@method print_form_field()
	 *@desc returns the list of html fields  automatically from a record
	 *@since v0.1 beta
	 * */
	public function print_form_field($field,$CssName = '',$ExtraAttributes ='' ,$encode_fields =FALSE,$access=TRUE,$read_only=FALSE,$code=''){
		$html_fld = '';






		if($encode_fields==TRUE){
			$field_id_html = 'fkf_'.encode($field.$code);
			$field_name_html = encode($field.$code);
		}else{
			$field_id_html = $field.$code;
			$field_name_html = $field.$code;
		}



		$original_type = $this->form_fields[$field]['Type'];
		$type_x = explode("(",$original_type);
		if($type_x>1){
			$type  = $type_x[0];
		}else{$type = $original_type;}

		// Display Mode

		$mode_view_edit = ($this->field_mode=='view-edit'?true:false);

		if($read_only==true){
			// read only
			$display_as='read-only';
		}else{
			if($this->field_mode=='view-edit'){
				$display_as = 'view-edit';
			}else{
				$display_as='edit';
			}
		}

		//$this->fields[$field] = isset($this->fields[$field]) ? utf8_encode( $this->fields[$field] ) : '' ;

		if($this->useHtmlEntities_onField==true){
			//$this->fields[$field] = isset($this->fields[$field]) ? htmlentities( $this->fields[$field] ) : '' ;
			
		}else{
			$this->fields[$field] = isset($this->fields[$field]) ? ( $this->fields[$field] ) : '' ;
		}

		$this->fields[$field] = isset($this->fields[$field])?utf8_encode($this->fields[$field]):'';




		

		switch($type){
			case "varchar":
				// Class
				$Class = 'class="txt form-control '.@$CssName.'"';
				if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><input style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){
						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}elseif($display_as=='read-only'){
						$html_fld .='<span class="form-control disabled" disabled="disabled" >'.$this->fields[$field].'</span><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}
				}


				break;
			case "autocomplete":
			case "autocomplete_select":
				// Class
				
				
				$slq_elements = fk_get_query_elements($this-> form_fields[$field]['sql']);
				$table = $slq_elements['table'];
				$val_fld_name = $slq_elements['fields'];
				$options = fk_select_text($table, $val_fld_name, $this->fields[$field]);
				
				$text_value = isset($options[1])?$options[1]:'';
				
				
				$Class = 'class="txt form-control '.@$CssName.'"';
				if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><input style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){
						
						$html_fld .= fk_autocomplete_select($field_id_html, $field_name_html, $this->fields[$field], $text_value, $field_id_html.'-apf', $this-> form_fields[$field]['sql'],$this-> form_fields[$field]['attr']);
						
					}elseif($display_as=='read-only'){
						$html_fld .='ACV<span class="form-control disabled" disabled="disabled" >'.$text_value.'</span><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}
				}


				break;	
			case "money": 
			case "number":
				// Class
				$Class = 'class="txt currency form-control '.@$CssName.'" ';
				if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})">$<input style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){
						$symbol = '';
						$group_class_end = '-num';
						if($this->fields[$field]==''){ $this->fields[$field] = 0;}
						if($type=='money'){ $symbol = '<span class="input-group-addon "><i  id="'.$field_id_html.'-calc" class="fa fa-calculator"></i> $</span>  '; $group_class_end = '';}
						$html_fld .='<div class="form-group">
<div class="input-group'.$group_class_end.' ">
'.$symbol.'
<input style="text-align:right;" id="'.$field_id_html.'-2" name="'.$field_name_html.'-2" type="text" value="'.number_format($this->fields[$field], 2, '.', ',').'" '.$Class.' '.@$ExtraAttributes.' >
<input style="text-align:right;display:none" id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' >

</div>
</div>
<script>
$(function(){

	$("#'.$field_id_html.'-calc").click(function(){
		var value = "";
		if(value = prompt("CALCULAR ( ejemplo: 3*3 ) ")){
			var v = eval(value);
			$("#'.$field_id_html.'").val(v);
			$("#'.$field_id_html.'-2").val(v);
			$("#'.$field_id_html.'-2").focus();
		}
	});

	$("#'.$field_id_html.'").change(function(){
		$(this).formatCurrency("#'.$field_id_html.'-2",{symbol:""});
	});

	$("#'.$field_id_html.'").blur(function(){
       $(this).formatCurrency("#'.$field_id_html.'-2",{symbol:""});
       $("#'.$field_id_html.'-2").show();
       $("#'.$field_id_html.'").hide();
	});
	$("#'.$field_id_html.'-2").focus(function(){
       $("#'.$field_id_html.'").show().focus();
       $("#'.$field_id_html.'-2").hide();
	});
	
});

</script>

';
						//$html_fld .='<div class="input-prepend"><span class="add-on">$</span><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' /></div>';

					}elseif($display_as=='read-only'){
						if($type=='money'){
							$val = fk_money_format($this->fields[$field]);
						}else{
							$val = str_replace('$','',fk_money_format($this->fields[$field]));
						}
						$html_fld .='<div class="form-control" style="text-align:right;" readonly="readonly">'.$val.'</div><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}
				}


				break;

			case "file":
				// Class
				$Class = 'class="txt form-control '.@$CssName.'"';
				if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><input style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){
						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$html_fld .='<br><iframe src="'.fk_link().'QrsGate/upolader/'.$field_id_html.'/" name="ifrmupl-'.$field_id_html.'" style="width:95%;height:45px;" frameborder="0"></iframe>';

						$file_data = '';
						$ArUpl = new ActiveRecord('uploads');
						$totUpl = $ArUpl->find(@$this->fields[$field]);


						if($totUpl==1){
							if(strrpos($ArUpl->fields['tipo'], 'image')>-1){
								//image
								
								$folder = ($ArUpl->fields['folder']!='')?$ArUpl->fields['folder'].'/':'';
								$file_data = '<a href="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" target="_blank"><img src="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" ></a>';
							}else{
								//Other file
								$folder = ($ArUpl->fields['folder']!='')?$ArUpl->fields['folder'].'/':'';
								$file_data = '<a href="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" target="_blank">'.$ArUpl->fields['titulo'].'</a>';
							}

						}

						//$html_fld .='<div id="ico-'.$field_id_html.'">'.$file_data.'</div>';
						$html_fld .='<div id="ico-'.$field_id_html.'"><span>'.$file_data.'</span> <a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="if(confirm(\'Remover?\')){$(\'#'.$field_id_html.'\').val(\'\');$(\'#ico-'.$field_id_html.' span\').html(\'\');$(\'#'.$field_id_html.'\').change();}"><i class="fa fa-trash-o"></i></a></div>';
					}elseif($display_as=='read-only'){
						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$file_data = '';
						$ArUpl = new ActiveRecord('uploads');
						$totUpl = $ArUpl->find(@$this->fields[$field]);

						

						if($totUpl==1){
							
							$folder = ($ArUpl->fields['folder']!='')?$ArUpl->fields['folder'].'/':'';

							if(strrpos($ArUpl->fields['tipo'], 'image')>-1){
								//image
								$file_data = '<a href="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" target="_blank"><img src="'.http_uploads().$ArUpl->fields['archivo'].'" ></a>';
							}else{
								//Other file
								$file_data = '<a href="'.http_uploads().$folder.$ArUpl->fields['archivo'].'" target="_blank">'.$ArUpl->fields['titulo'].'</a>';
							}
						}

						$html_fld .='<div id="ico-'.$this->id_field_name.'">'.$file_data.'</div>';
					}
				}


				break;
		 case ( $type== "timestamp" || $type== "datetime"  ): 
		 	// Class
		 	$Class = 'class=" form-control '.@$CssName.'"';
		 	$date_value='';
		 	if(trim($this->fields[$field])!='' && trim($this->fields[$field])!='0000-00-00 00:00:00'){$date_value = date(DATE_FORMAT.' H:i',strtotime($this->fields[$field]));}

		 	if($access==TRUE){
		 		if($display_as=='view-edit'){
		 			$html_fld .=$this->fields[$field].'<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.$date_value.'" '.$Class.' '.@$ExtraAttributes.'/>';
		 			$html_fld .='<script language="javascript" type="text/javascript">	$(function() {		$("#'.$field_id_html.'").datetimepicker({dateFormat: "'.JS_DATE_FORMAT.'",timeFormat: "H:i",changeMonth: true,changeYear: true,closeOnDateSelect:true});});</script>';
		 		}elseif($display_as=='edit'){

		 			//$date_value = $this->fields[$field];

		 			$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.$date_value.'" '.$Class.' '.@$ExtraAttributes.'/>';
		 			$html_fld .='<script language="javascript" type="text/javascript">	$(function() {		$("#'.$field_id_html.'").datetimepicker({format: "'.JS_DATE_FORMAT.' H:i",changeMonth: true,changeYear: true,closeOnDateSelect:true});});</script>';

		 		}elseif($display_as=='read-only'){
		 			$html_fld .= '<span class="form-control disabled" disabled="disabled" >'.$date_value.'</span>'.'<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.$date_value.'" '.$Class.' '.@$ExtraAttributes.' />';
		 		}
		 	}




		 	break;
		 case "date-select":
		 	//$Class = 'class="date '.@$CssName.'"';
		 	$Class = 'class=" form-control '.@$CssName.'"';
		 	if($access==TRUE){
		 		if($display_as=='view-edit'){
		 			$html_fld .='1<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.@$Class.' '.@$ExtraAttributes.' />';
		 			$html_fld .='<script language="javascript" type="text/javascript">$(function() {$( "#'.$field_id_html.'" ).datepicker1({ dateFormat: "'.JS_DATE_FORMAT.'",showOn: "button",buttonImage: HTTP+"_HTML/img/calendar.gif", buttonImageOnly: true});	});	</script>';
		 		}elseif($display_as=='edit'){
		 			$date_value='';
		 			//if(trim($this->fields[$field])!=''){$date_value = date(DATE_FORMAT,strtotime($this->fields[$field]));}

		 			$date_value = $this->fields[$field];

		 			$Class = rtrim(str_replace('class="', '',$Class),'"');

		 			$html_fld .= fk_date_field($field_id_html, $field_name_html, $date_value,$onclick=null,$cssExtra=$Class,$mode='edit');

		 			//$html_fld .='2<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.$date_value.'" '.@$Class.' '.@$ExtraAttributes.' />[<a href="javascript:void(0)" onclick="$(\'#'.$field_id_html.'\').val(\''.date(DATE_FORMAT).'\')">'.__('Hoy').'</a>]';
		 			//$html_fld .='<script language="javascript" type="text/javascript">$(function(){$( "#'.$field_id_html.'" ).datepicker3({ dateFormat: "'.JS_DATE_FORMAT.'",changeMonth: true,changeYear: true});	});	</script>';
		 		}elseif($display_as=='read-only'){
		 			$html_fld .= getFormatedDate($this->fields[$field]) .'<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
		 		}
		 	}

		 	break;
		 case "date":

		 	$Class = 'class=" form-control '.@$CssName.'"';
		 	if($access==TRUE){
		 		if($display_as=='view-edit'){
		 			$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.@$Class.' '.@$ExtraAttributes.' />';
		 			$html_fld .='<script language="javascript" type="text/javascript">$(function() {$( "#'.$field_id_html.'" ).datepicker4({ dateFormat: "'.JS_DATE_FORMAT.'",showOn: "button",buttonImage: HTTP+"_HTML/img/calendar.gif", buttonImageOnly: true});	});	</script>';
		 		}elseif($display_as=='edit'){
		 			$date_value='';
		 			if(trim($this->fields[$field])!='' && $this->fields[$field]!='0000-00-00' ){$date_value = date(DATE_FORMAT,strtotime($this->fields[$field]));}
		 			
		 			$html_fld .= '<div class="input-group input-group-sm m-bot15"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>';
		 			$html_fld .= '<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.$date_value.'" '.@$Class.' '.@$ExtraAttributes.' />';
		 			$html_fld .= '</div>';

		 			$html_fld .='<script language="javascript" type="text/javascript">$(function(){$( "#'.$field_id_html.'" ).datetimepicker({ format: "'.JS_DATE_FORMAT.'",timepicker:false,closeOnDateSelect:true});	});	</script>';
		 		}elseif($display_as=='read-only'){
		 			$html_fld .= '<span class="form-control disabled" disabled="disabled" >'.getFormatedDate($this->fields[$field]) .'</span><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
		 		}
		 	}


		 	break;
			
		 case "text":
		 	$Class = 'class=" form-control '.@$CssName.'"';
		 	$this->fields[$field] = htmlentities($this->fields[$field]);
		 	if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><textarea style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'"  '.$Class.' '.@$ExtraAttributes.' >'.@$this->fields[$field].'</textarea>';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.@$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){
						$html_fld .='<textarea id="'.$field_id_html.'" name="'.$field_name_html.'"  '.$Class.' '.@$ExtraAttributes.' >'.@$this->fields[$field].'</textarea>';
					}elseif($display_as=='read-only'){
						
						$html_fld .='<div id="tx-'.$field_id_html.'" class="textarea-content">'.nl2br(@$this->fields[$field]).'</div><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}
		 	}

		 	break;
		 case "textarea":
		 	$Class = 'class=" form-control '.@$CssName.'"';
		 	$this->fields[$field] = htmlentities($this->fields[$field]);
		 	$this->fields[$field] = ($this->fields[$field]);
		 	if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><textarea style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'"  '.$Class.' '.@$ExtraAttributes.' >'.@$this->fields[$field].'</textarea>';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.@$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){
						$html_fld .='<textarea id="'.$field_id_html.'" name="'.$field_name_html.'"  '.$Class.' '.@$ExtraAttributes.' >'.@$this->fields[$field].'</textarea>';
					}elseif($display_as=='read-only'){
						
						$html_fld .='<div class="pre" style="border:1px solid #ccc;padding:3px;" id="tx-'.$field_id_html.'">'.nl2br(@$this->fields[$field]).'</div><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}
		 	}
		 	break;
		 case "json":
		 	$Class = 'class=" form-control '.@$CssName.'"';
		 	//$this->fields[$field] = htmlentities($this->fields[$field]);
		 	$this->fields[$field] = ($this->fields[$field]);
		 	if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><textarea style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'"  '.$Class.' '.@$ExtraAttributes.' >'.@$this->fields[$field].'</textarea>';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.@$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){
						$html_fld .='<textarea id="'.$field_id_html.'" name="'.$field_name_html.'"  '.$Class.' '.@$ExtraAttributes.' >'.@$this->fields[$field].'</textarea>';
					}elseif($display_as=='read-only'){
						
						$html_fld .='<div class="pre" style="border:1px solid #ccc;padding:3px;" id="tx-'.$field_id_html.'">'.nl2br(@$this->fields[$field]).'</div>
						<textarea id="'.$field_id_html.'" name="'.$field_name_html.'" class="hide">'.@$this->fields[$field].'</textarea>';
					}
		 	}

		 	break;
		 case "tinyint":
		 	$chk[0] = '';
		 	$chk[1] = '';
		 	$chk_read_val = (@$this->fields[$field]==1?'Si':'No');
		 	if(@$this->fields[$field] == 1 || @$this->fields[$field]==0){
		 		$chk[@$this->fields[$field]] = 'CHECKED';
		 	}
		 	$Class = 'class=" '.@$CssName.'"';
		 	if($access==TRUE){
					if($display_as=='view-edit' || $display_as == 'edit'){
						$html_fld .='Si<input id="'.$field_id_html.'_1" name="'.$field_name_html.'" '.$chk['1'].' type="radio" value="1" '.$Class.' '.@$ExtraAttributes.'>
             No<input id="'.$field_id_html.'_0" name="'.$field_name_html.'" '.$chk['0'].' type="radio" value="0" '.$Class.' '.@$ExtraAttributes.'>';
					}elseif($display_as=='read-only'){
						$html_fld .=$chk_read_val;
					}
		 	}
		 	 

		 	break;
		 case "checkbox":
		 	$chk[0] = '';
		 	$chk[1] = '';

		 	$chk_read_val = (@$this->fields[$field]==1?'Si':'No');
		 	$chk = '';
		 	if(@$this->fields[$field] == 1 ){
		 		$chk = 'checked="checked"';
		 	}

		 	$Class = 'class=" form-control '.@$CssName.'"';
		 	if($access==TRUE){


					if($display_as=='view-edit' || $display_as == 'edit'){

						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.$this->fields[$field].'" >';
						$html_fld .='<input id="'.$field_id_html.'_1" name="'.$field_name_html.'_1" '.$chk.' type="checkbox" value="1" onclick="if($(this).is(\':checked\')==true){$(\'#'.$field_id_html.'\').val(1);}else{$(\'#'.$field_id_html.'\').val(0);}" '.$Class.' '.@$ExtraAttributes.'>';
							
					}elseif($display_as=='read-only'){
						$html_fld .=$chk_read_val;
						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.$this->fields[$field].'" >';
					}
		 	}
		 	 

		 	break;

		 	// Tipo Password
		 case "password":
		 	$Class = 'class="pass form-control '.@$CssName.'"';

		 	if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="password" value="" '.$Class.' '.@$ExtraAttributes.' />';
					}elseif($display_as=='edit'){
						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="password" value="" '.$Class.' '.@$ExtraAttributes.' />';
					}elseif($display_as=='read-only'){
						$html_fld .= '********';
					}
		 	}

		 	break;
		 case "select":
//$display_as = 'read-only';
		 	// Get Select Options from sql

		 	$Class = 'class="sel form-control '.@$CssName.'"';

		 	if($access==TRUE){

		 		$option_selected = isset($this->form_fields[$field]['selected_option']) ? $this->form_fields[$field]['selected_option'] : @$this->fields[$field];

					if($display_as=='view-edit'){

						$options = fk_select_options($this-> form_fields[$field]['sql_options'],$option_selected);
						$html_fld .='<select style="width:100%;height:34px"  id="'.$field_id_html.'" name="'.$field_name_html.'" '.$Class.' '.@$ExtraAttributes.' ><option value="">-- --</option>'.$options.'</select>';

					}elseif($display_as=='edit'){

						$add_new_op = '';
						$add_new_txt = '';
						if(isset($this->form_fields[$field]['add_new'])){
							$add_new_op = '<option value="new">+ Nuevo</option>';
							$add_new_txt .='<input type="text" id="'.$field_id_html.'-txt" class="form-control" name="'.$field_name_html.'-txt" style="display:none" >';
							$add_new_txt .='<a href="javascript:void(0)" id="'.$field_id_html.'-cancel" style="display:none" onclick="fkSelect($(this),true);" >Cancelar</a>';
						}

						$options = fk_select_options($this-> form_fields[$field]['sql_options'],$option_selected);
						$html_fld .='<select style="width:100%;height:34px" id="'.$field_id_html.'" name="'.$field_name_html.'" '.$Class.' '.@$ExtraAttributes.' onchange="fkSelect($(this),false);" ><option value="">[ Seleccione ]</option>'.$options.$add_new_op.'</select>';
						$html_fld .=$add_new_txt;

					}elseif($display_as=='read-only'){

						if(isset($this-> form_fields[$field]['sql_complex'])){
							// query complex version
							$options = fk_select_complex_query($this-> form_fields[$field]['sql_complex'], array($option_selected));

						}else{
							// Simple query version
							$slq_elements = fk_get_query_elements($this-> form_fields[$field]['sql_options']);
							$this-> form_fields[$field]['sql_options'];
							//pa($slq_elements);
							$table = $slq_elements['table'];
							$val_fld_name = $slq_elements['fields'];
							$options = fk_select_text($table, $val_fld_name, $option_selected);

						}

						$html_fld .= '<span class="form-control disabled" disabled="disabled" >'.$options[1].'</span><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" />';

					}
		 	}

		 	break;
		 case "hidden":
		 	$Class = 'class="hdn '.@$CssName.'"';
		 	$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
		 	break;
		 case "search_field":
				// Class
				$Class = 'class="txt form-control '.@$CssName.'"';
				if($access==TRUE){

					$option_selected = isset($this->form_fields[$field]['selected_option']) ? $this->form_fields[$field]['selected_option'] : @$this->fields[$field];

					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><input style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.$this->fields[$field].'"  />';
					}elseif($display_as=='edit'){

						$options = fk_select_complex_query($this-> form_fields[$field]['sql_complex'], array($option_selected));

						if(isset($options[1])){ $options[1] = utf8_encode($options[1]); }

						/*$html_fld .='';
						 $html_fld .='
						 <input type="button" id="'.$field_id_html.'-btn" value="&nbsp;" class="btn btn-primary search2" onclick="appForm_PopupSrc({id:\''.$field_id_html.'\',tbl:\''.$this->table.'\',forceOpen:true})">
						 <input type="button" id="'.$field_id_html.'-btn" class="btn btn-primary empty" value="&nbsp;" onclick="appForm_ClearPopupSrc({id:\''.$field_id_html.'\',tbl:\''.$this->table.'\'});">
						 ';*/

						

						$html_fld .= '
						<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'"  />
						<div class="input-group m-bot15 search-field">
							<input id="'.$field_id_html.'-2" name="'.$field_name_html.'-2" type="text" value="'.@$options[1].'" class="txt form-control '.$CssName.' searchbox" '.@$ExtraAttributes.' onblur="appForm_PopupSrc({id:\''.$field_id_html.'\',tbl:\''.$this->table.'\'})" />
                                <div class="input-group-btn" style="padding-bottom: 4px;">
                                    <button tabindex="-1" class="btn btn-white" type="button" onclick="appForm_PopupSrc({id:\''.$field_id_html.'\',tbl:\''.$this->table.'\',forceOpen:true})" ><i class="fa fa-clone"></i></button>
                                    <button tabindex="-1" data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button"><span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);" onclick="appForm_ClearPopupSrc({id:\''.$field_id_html.'\',tbl:\''.$this->table.'\'});">Limpiar</a></li>
                                    </ul>
                                </div>
                            </div>';
						$html_fld .='<div id="srcfld-rs-'.$field_id_html.'"></div>';
						
						// Autocomplete 	
						$enc_field_id_html = encode($field_id_html);
					
						$html_fld .='
					<script type="text/javascript">
					$(function(){
						var source'.$enc_field_id_html.' = new Bloodhound({
						datumTokenizer: Bloodhound.tokenizers.obj.whitespace("value"),
						queryTokenizer: Bloodhound.tokenizers.whitespace,
						remote: {
						  url: HTTP+"QrsGate/autocompleteAppForm/'.$this->table.'/'.$field_id_html.'/?q=%QUERY",
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
						

					}elseif($display_as=='read-only'){

						$options = fk_select_complex_query($this-> form_fields[$field]['sql_complex'], array($option_selected));

						$html_fld .='<span class="form-control disabled" disabled="disabled" >'.$options[1].'</span><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}
				}


				break;
		 default:
		 	$Class = 'class="txt form-control '.@$CssName.'"';
		 	if($access==TRUE){
					if($display_as=='view-edit'){
						$html_fld .='<div class="fld" onclick="appForm_updfldTxt({id:\''.$field_id_html.'\'})"><input style="display:none" id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
						$html_fld .='<span id="val-'.$field_id_html.'">'.@$this->fields[$field].'</span>&nbsp;<span class="ui-icon ui-icon-gear"></span></div>';
						$html_fld .='<input id="cur-v-'.$field_id_html.'" type="hidden" value="'.@$this->fields[$field].'"  />';

					}elseif($display_as=='edit'){
						$html_fld .='<input id="'.$field_id_html.'" name="'.$field_name_html.'" type="text" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}elseif($display_as=='read-only'){
						$html_fld .='<span class="form-control disabled" disabled="disabled" >'.$this->fields[$field].'</span><input id="'.$field_id_html.'" name="'.$field_name_html.'" type="hidden" value="'.@$this->fields[$field].'" '.$Class.' '.@$ExtraAttributes.' />';
					}
		 	}
		 	break;
		}

		$html_fld = '<div class="form-group">'.$html_fld.'</div>';
			
		return $html_fld;
			
	} // print_form_field()


	/**
	 *@package db_record
	 *@method get_table()
	 *@desc returns the table name
	 *@since v0.1 beta
	 * */
	public function get_table(){
		
		$class = get_class($this);
		$xp_class = explode('\\',$class);
		$len = count($xp_class);
		
		// Remove "Record"
		return substr($xp_class[$len - 1], 0, -6);
		
	} // get_table
	# returns the table name
	/**
	 *@package db_record
	 *@method get_id_table()
	 *@desc returns the primary key id field name
	 *@since v0.1 beta
	 * */
	private function get_id_table(){

		$pri_key_id=$this->db_obj->get_primary_key_id();
		$id_field_name = $pri_key_id['Field'];
		return $id_field_name;
			
	} // get_table
	/**
	 *@package db_record
	 *@method find($Id,$FindMode );
	 *@desc finds a record from a given id
	 *@param $Id
	 *@param $FindMode
	 *@var  $FindMode  [first | prev | next | last | where]
	 *@return  Integer  num_rows found
	 *@since v0.1 beta
	 * */
	public function find($id,$FindMode = NULL){
		//Define Find Mode
		if($FindMode!=NULL){
			$this->FindMode = $FindMode;
		}
		$this->fields[$this->id_field_name] = $id;
		self::get_fields();
		return $this-> num_rows;
			
	}
	/**
	 * @package db_record :: Active Record
	 * @method  find_where($Where)
	 * @example $dbRecord->find_where('id_record = "3" AND other_field = "2" ');
	 * @return  Integer  num_rows found
	 * @desc    Llena el arraglo record->fields[] con los valores del primer registro
	 *          encontrado
	 *          y devuelve el total de registros encontrados
	 * */
	public function find_where($Where,$params=null){

		//Define Find Mode
		$this->FindMode = 'where';

		if($params!=null){
			// con argumentos
			if(is_array($params)){
				// add escape string
				$safeparam = array();
				foreach ($params as $param) {
					$safeparam[] = $this->db_obj->escape_string($param);
				}
				// array mode
				$repl = array('"%s"');
				$find = array('?');
				$Where = str_replace($find,$repl,$Where);
				$Where =  vsprintf($Where,$safeparam);

				$this->SqlWhere= ' WHERE ( '.$Where.' )';
			}else{
				// single mode
				$this->SqlWhere= ' WHERE ( '.$Where.' )';
			}
				
		}else{
			// normal (bajo en seguridad mysql inyection)
			$this->SqlWhere= ' WHERE ( '.$Where.' )';
		}



		self::get_fields();
		return $this-> num_rows;

	}

	/**
	 * @package db_record
	 * @method  inserted_id()
	 * @desc returns the inserted id of last query
	 * @since v0.1 beta
	 * */
	public function inserted_id(){
		if($this->inserted_id!=NULL){
			$ins_id = $this->inserted_id;
			$this->inserted_id = NULL;
			return $ins_id;
		}else{
			return $this->db_obj->inserted_id();	
		}
		
	}

	/**
	 * @package db_record
	 * @method  and_condition()
	 * @desc sets sql AND contition
	 * @since v0.1 beta
	 * */
	public function and_condition($and){
		return $this->SqlAnd=$and;
	}


}
