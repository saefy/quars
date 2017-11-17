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
    	$path = $path ?? SYSTEM_PATH ;
    	$viewPaths = $path . 'app/Views';
    	$cachePath = $path . 'app/Cache';

        parent::__construct($viewPaths, $cachePath, $container);
    }
}