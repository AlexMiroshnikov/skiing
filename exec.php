<?php
require_once 'src/Map.php';
require_once 'src/Detector.php';
require_once 'src/Node.php';
require_once 'src/Route.php';

$arr = array(
	array(4, 8, 7, 3),
	array(2, 5, 9, 3),
	array(6, 3, 2, 5),
	array(4, 4, 1, 6)
);
/*
$arr = array(
	array(4, 8, 7, 3, 1),
	array(2, 5, 9, 3, 2),
	array(6, 3, 2, 5, 3),
	array(4, 4, 1, 6, 10),
	array(5, 6, 7, 2, 10)
);
//*/

/*
$arr = array(
	array(4, 8),
	array(2, 5)
);
//*/

/*
$arr = array(
	array(4)
);
//*/

//$map = new \Skiing\Map($arr);
$map = \Skiing\Map::createFromSampleTextFile('./map.txt');
$detector = new \Skiing\Detector($map);
$time = -microtime(true);
$routes = $detector->getRoutes();

echo "\nRoutes: ";
/**
 * @var int $i
 * @var \Skiing\Route $route
 */
/*
foreach ($routes as $i => $route)
{
	echo "\n\tRoute ".($i+1).": ".$route->getLength().' - '.$route->getDrop().' : '.$route->getQualityIndex();
}
echo "\n";
//*/

$bestRoute = $detector->getBestRoute();
echo "\nBest route: ".$bestRoute->getLength().' - '.$bestRoute->getDrop();
echo "\nRoute details: ".$bestRoute->getNodesAsString();
echo "\nTotal: time: ".round($time + microtime(true), 5).', mem: '.number_format(memory_get_peak_usage(true), 0, '.', ' ')."\n\n";