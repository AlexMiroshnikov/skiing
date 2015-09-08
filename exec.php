<?php
require_once 'src/Map.php';
require_once 'src/Detector.php';
require_once 'src/Node.php';
require_once 'src/Route.php';

$map = \Skiing\Map::createFromSampleTextFile('./map.txt');
$detector = new \Skiing\Detector($map);
$time = -microtime(true);
$routes = $detector->getRoutes();
$bestRoute = $detector->getBestRoute();
echo "\nBest route: ".$bestRoute->getLength().' - '.$bestRoute->getDrop();
echo "\nRoute details: ".$bestRoute->getNodesAsString();
echo "\nTotal: time: ".round($time + microtime(true), 5).', mem: '.number_format(memory_get_peak_usage(true), 0, '.', ' ')."\n\n";