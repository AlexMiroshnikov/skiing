<?php
namespace Skiing;

/**
 * Class Detector
 * @package Skiing
 */
class Detector
{
	/** @var Map */
	private $_map = null;

	/** @var array */
	private $_grid = array();

	/** @var array */
	private $_sortedValues = array();

	/** @var array */
	private $_routes = null;

	/** @var int */
	private $_maxKnownLength = 0;

	/** @var array */
	private static $deltas = array(
		array(-1, 0),
		array(0, -1),
		array(1, 0),
		array(0, 1),
	);

	/**
	 * @param Map $map
	 */
	public function __construct(Map $map)
	{
		$this->_map = $map;
	}

	/**
	 * @return array
	 */
	public function getRoutes()
	{
		self::_log('init..');
		$this->_init();

		self::_log('iterate sorted values..');

		/**
		 * @var string $key
		 * @var int $val
		 */
		foreach ($this->_sortedValues as $key => $val)
		{
			$this->_tryRouteViaValue($key, $val);
		}

		self::_log('iterated!');
		return $this->_routes;
	}

	/**
	 * @return Route
	 */
	public function getBestRoute()
	{
		if ($this->_routes === null)
		{
			$this->getRoutes();
		}

		$bestRoute = self::_usort($this->_routes);
		return $bestRoute;
	}

	/**
	 * @param string $key
	 * @param int $val
	 * @param Route $route
	 */
	private function _tryRouteViaValue($key, $val, Route $route = null)
	{
		if (!$route)
		{
			$route = new Route();
		}

		$parts = explode(',', $key);
		$node = Node::factory((int)$parts[0], (int)$parts[1], $val);
		$route->addNode($node);
		//unset($this->_sortedValues[$key]);
		$neighbours = $this->_getSuitableNeighbours($node);

		if (!$neighbours)
		{
			$this->_addRoute($route);
		}
		else
		{
			foreach ($neighbours as $nextKey => $neighbour)
			{
				$routeClone = clone $route;
				$this->_tryRouteViaValue($nextKey, $neighbour, $routeClone);
			}
		}
	}

	/**
	 * @param Node $node
	 * @return array
	 */
	private function _getSuitableNeighbours(Node $node)
	{
		$neighbours = array();

		$col = $node->getCol();
		$row = $node->getRow();
		$val = $node->getVal();

		foreach (self::$deltas as $deltas)
		{
			$curCol = $col + $deltas[0];
			$curRow = $row + $deltas[1];

			if (!isset($this->_grid[$curCol][$curRow]))
			{
				continue;
			}

			if ($this->_grid[$curCol][$curRow] >= $val)
			{
				continue;
			}

			$neighbours[self::_makeKeyByColRow($curCol, $curRow)] = $this->_grid[$curCol][$curRow];
		}

		arsort($neighbours);
		return $neighbours;
	}

	/**
	 * @param void
	 * @return void
	 */
	private function _init()
	{
		$this->_routes = array();

		foreach ($this->_map->getData() as $rowNum => $row)
		{
			foreach ($row as $colNum => $val)
			{
				$this->_grid[$colNum][$rowNum] = $val;

				if ($val)
				{
					$this->_sortedValues[self::_makeKeyByColRow($colNum, $rowNum)] = $val;
				}
			}
		}

		self::_log('sort max values..');
		uasort($this->_sortedValues, function($a, $b){
			if ($a > $b) return -1;
			if ($a < $b) return 1;
			return 0;
		});
	}

	/**
	 * @param Route $route
	 */
	private function _addRoute(Route $route)
	{
		if (!$route->getDrop())
		{
			return;
		}

		if ($route->getLength() < $this->_maxKnownLength)
		{
			return;
		}

		if (($len = $route->getLength()) > (($drop = $route->getDrop()) + 1))
		{
			throw new \LogicException('Length '.$len.' is too large comparing to drop '.$drop);
		}

		if ($len > $this->_maxKnownLength)
		{
			$this->_routes = array();
			self::_log("\tflush routes, ".$len);
		}

		$this->_routes[] = $route;
		$this->_maxKnownLength = $len;
	}

	/**
	 * @param array $routes
	 * @return mixed
	 */
	private static function _usort(array $routes)
	{
		self::_log('sorting..');
		usort($routes, function(Route $a, Route $b){
			if ($a->getLength() < $b->getLength()) return 1;
			if ($a->getLength() > $b->getLength()) return -1;
			if ($a->getDrop() < $b->getDrop()) return 1;
			if ($a->getDrop() > $b->getDrop()) return -1;
			if ($a->getFirstVal() < $b->getFirstVal()) return 1;
			if ($a->getFirstVal() > $b->getFirstVal()) return -1;
			return 0;
		});

		reset($routes);
		self::_log('sorted!');
		return current($routes);
	}

	/**
	 * @param int $col
	 * @param int $row
	 * @return string
	 */
	private static function _makeKeyByColRow($col, $row)
	{
		return $col.','.$row;
	}

	/**
	 * @param string $msg
	 */
	private static function _log($msg)
	{
		echo "\n\t".date('Y-m-d H:i:s')."\t".$msg;
	}
}