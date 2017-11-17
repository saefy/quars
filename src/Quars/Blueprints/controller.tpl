<?php
namespace App\Controllers;

use \Quars\Controller;

class __ControlerName__ extends Controller{
    
    public function index(){
    	fk_header();
    	$this-&gt;load-&gt;view('__FolderName__/index.php');
    	fk_footer();
    } // End index

} // End __ControlerName__
