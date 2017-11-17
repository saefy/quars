<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */

function pa($ARR = NULL){
	if($ARR!=NULL){
		$printArray = $ARR;
	}else{$printArray = $_POST;}
	$uniqId=mt_rand(100,1000000);
	if(count($printArray)>0){
		if (php_sapi_name() != "cli") {
			echo '<div id="DEBUG_POST_INFO'.$uniqId.'" class="message" >
					  <a href="javaScript:oculta(\'DEBUG_POST_INFO'.$uniqId.'\');">[Show|Hide]</a>
					  <pre id="data_DEBUG_POST_INFO'.$uniqId.'">';
		}

		print_r($printArray);
		if (php_sapi_name() != "cli") {
			echo '</pre>
	  </div>';
		}

	}
}

function var_dump_pa($ARR = NULL){
	if($ARR!=NULL){
		$printArray = $ARR;
	}else{$printArray = $_POST;}
	$uniqId=mt_rand(100,1000000);
	if(count($printArray)>0){
		echo '<div id="DEBUG_POST_INFO'.$uniqId.'" class="message" >
  <a href="javaScript:oculta(\'DEBUG_POST_INFO'.$uniqId.'\');">[Show|Hide]</a>
  <pre id="data_DEBUG_POST_INFO'.$uniqId.'">';
		var_dump($printArray);
		echo '</pre>
  
  </div>';  
	}
}