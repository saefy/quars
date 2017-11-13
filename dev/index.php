<?php
include 'autoload.php';


$Q = new \Saefy\Quars\Quars();
echo $Q->okay();

echo '<br>';
$C = new \Saefy\Quars\Client\Client();

echo $C->getPath();