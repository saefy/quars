<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
 
namespace Quars;

use \Jenssegers\Blade\Blade;

class Bladex extends Blade{

	public function __construct($path = null, ContainerInterface $container = null)
    {
		$viewPaths = SYSTEM_PATH_QRS . 'app/Views';
		$cachePath = SYSTEM_PATH_QRS . 'app/Cache';
		if( $path !== null) {
			$viewPaths = $path;
		}
    	
        parent::__construct($viewPaths, $cachePath, $container);
    }
}