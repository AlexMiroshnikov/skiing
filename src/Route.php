<?php
namespace Skiing;

class Route
{
	/** @var array */
	private $_nodes = array();

	/**
	 * @param Node $node
	 * @return $this
	 */
	public function addNode(Node $node)
	{
		$this->_validateNode($node);
		$this->_nodes[$node->getId()] = $node;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDrop()
	{
		$min = end($this->_nodes);
		reset($this->_nodes);
		$max = current($this->_nodes);
		return ($max->getVal() - $min->getVal());
	}

	/**
	 * @return int
	 */
	public function getLength()
	{
		return count($this->_nodes);
	}

	/**
	 * @param Node $node
	 * @return bool
	 */
	public function hasNode(Node $node)
	{
		return isset($this->_nodes[$node->getId()]);
	}

	/**
	 * @return int|null
	 */
	public function getLastVal()
	{
		return (($lastNode = end($this->_nodes)) ? $lastNode->getVal() : null);
	}

	/**
	 * @return int|null
	 */
	public function getFirstVal()
	{
		reset($this->_nodes);
		return (($node = current($this->_nodes)) ? $node->getVal() : null);
	}

	/**
	 * @return float
	 */
	public function getQualityIndex()
	{
		$index = $this->getLength() + $this->getDrop();
		$index += ($this->getDrop()/$index);
		return $index;
	}

	/**
	 * @param Node $node
	 */
	private function _validateNode(Node $node)
	{
		if (($lastNode = end($this->_nodes)) AND
			$node->getVal() >= $lastNode->getVal()
			)
		{
			throw new \LogicException('$node val is not lesser than the previous one');
		}
	}
}