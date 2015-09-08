<?php
//*
if (!isset($argv[1]))
{
	echo "\nUsage: php exec.php /path/to/map.txt\n";
	exit(0);
}

if (!$filename = $argv[1])
{
	echo "\nError: empty filename\n";
	exit(0);
}
//*/

require_once 'src/Map.php';
require_once 'src/Detector.php';
require_once 'src/Node.php';
require_once 'src/Route.php';

try {
	/*
	$arr = array(
		array(4, 8, 7, 3),
		array(2, 5, 9, 3),
		array(6, 3, 2, 5),
		array(4, 4, 1, 6)
	);

	$map = new \Skiing\Map($arr);
	//*/
	$map = \Skiing\Map::createFromSampleTextFile($filename);
	$detector = new \Skiing\Detector($map);
	$time = -microtime(true);
	$routes = $detector->getRoutes();
	$bestRoute = $detector->getBestRoute();
	echo "\nBest route: ".$bestRoute->getLength().' - '.$bestRoute->getDrop();
	echo "\nRoute details: ".$bestRoute->getNodesAsString();
	echo "\nTotal: time: ".round($time + microtime(true), 3).'s, mem: '.number_format(memory_get_peak_usage(true), 0, '.', ' ')."kb\n\n";
} catch (Exception $e) {
	echo "\nAn error occurred:\n".$e->__toString()."\n";
	exit(0);
}