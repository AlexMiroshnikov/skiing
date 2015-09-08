<?php

namespace Skiing;


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
	private $_maxKnownDrop = 0;

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
		$this->_init();

		//** @var Node $node */
		//foreach ($this->_sortedValues as $node)
		/**
		 * @var string $key
		 * @var int $val
		 */
		foreach ($this->_sortedValues as $key => $val)
		{
			if ($val < $this->_maxKnownDrop)
			{
				break;
			}

			//$this->_tryRoute($node);
			$this->_tryRouteValue($key, $val);
		}

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

		//$bestRoute = self::_sortByQualityIndex($this->_routes);
		$bestRoute = self::_usort($this->_routes);

		return $bestRoute;
	}

	/**
	 * @param Node $node
	 * @param Route $route
	 */
	private function _tryRoute(Node $node, Route $route = null)
	{
		if (!$route)
		{
			$route = new Route();
		}

//		echo "\n trying route len ".$route->getLength().' ..';
		$route->addNode($node);
		$neighbours = $this->_getSuitableNeighbours($node);

		if (!$neighbours)
		{
//			echo "\n  adding route";
			$this->_addRoute($route);
		}
		else
		{
			foreach ($neighbours as $neighbour)
			{
				$routeClone = clone $route;
				$this->_tryRoute($neighbour, $routeClone);
			}
		}
	}

	/**
	 * @param string $key
	 * @param int $val
	 * @param Route $route
	 */
	private function _tryRouteValue($key, $val, Route $route = null)
	{
		if (!$route)
		{
			$route = new Route();
		}

//		echo "\n trying route len ".$route->getLength().' ..';
		$parts = explode(',', $key);
		$node = Node::factory((int)$parts[0], (int)$parts[1], $val);
		$route->addNode($node);
		$neighbours = $this->_getSuitableNeighbours($node);

		if (!$neighbours)
		{
//			echo "\n  adding route";
			$this->_addRoute($route);
		}
		else
		{
			foreach ($neighbours as $nextKey => $neighbour)
			{
				$routeClone = clone $route;
				$this->_tryRouteValue($nextKey, $neighbour, $routeClone);
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

			//if ($this->_grid[$curCol][$curRow]->getVal() >= $val)
			if ($this->_grid[$curCol][$curRow] >= $val)
			{
				continue;
			}

			//$neighbours[] = $this->_grid[$curCol][$curRow];
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
		echo "\ninit..";
		$this->_routes = array();

		foreach ($this->_map->getData() as $rowNum => $row)
		{
			foreach ($row as $colNum => $val)
			{
				/*
				$node = Node::factory($colNum, $rowNum, $val);
				$this->_grid[$colNum][$rowNum] = $node;
				$this->_sortedValues[] = $node;
				//*/
				//*
				$this->_grid[$colNum][$rowNum] = $val;
				$this->_sortedValues[self::_makeKeyByColRow($colNum, $rowNum)] = $val;
				//*/
			}
		}

		echo "\n sort..";
		//usort($this->_sortedValues, function(Node $a, Node $b){
		uasort($this->_sortedValues, function($a, $b){
			if ($a > $b) return -1;
			if ($a < $b) return 1;
			return 0;
		});
		echo "\ninit finished";
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

		if ($route->getDrop() < $this->_maxKnownDrop)
		{
			return;
		}

		if (($len = $route->getLength()) > (($drop = $route->getDrop()) + 1))
		{
			throw new \LogicException('Length '.$len.' is too large comparing to drop '.$drop);
		}

		$this->_routes[] = $route;
		$this->_maxKnownDrop = $route->getDrop();
	}

	/**
	 * @param array $routes
	 * @return Route
	 */
	private static function _sortByQualityIndex(array $routes)
	{
		$bestRoute = array_shift($routes);

		/** @var Route $route */
		foreach ($routes as $route)
		{
			if ($route->getQualityIndex() > $bestRoute->getQualityIndex())
			{
				$bestRoute = $route;
			}
		}

		return $bestRoute;
	}

	private static function _usort(array $routes)
	{
		usort($routes, function(Route $a, Route $b){
			if ($a->getDrop() < $b->getDrop()) return 1;
			if ($a->getDrop() > $b->getDrop()) return -1;
			if ($a->getLength() < $b->getLength()) return 1;
			if ($a->getLength() > $b->getLength()) return -1;
			if ($a->getFirstVal() < $b->getFirstVal()) return 1;
			if ($a->getFirstVal() > $b->getFirstVal()) return -1;
			return 0;
		});
		reset($routes);
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
}