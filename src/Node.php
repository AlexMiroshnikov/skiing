<?php
namespace Skiing;

class Node
{
	/** @var int */
	private $_col;
	/** @var int */
	private $_row;
	/** @var int */
	private $_val;

	/**
	 * @param $col
	 * @param $row
	 * @param $val
	 * @return Node
	 */
	public static function factory($col, $row, $val)
	{
		$node = new self();
		$node->setCol($col);
		$node->setRow($row);
		$node->setVal($val);
		return $node;
	}

	/**
	 * @return int
	 */
	public function getCol()
	{
		return $this->_col;
	}

	/**
	 * @param int $col
	 */
	public function setCol($col)
	{
		$this->_col = $col;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRow()
	{
		return $this->_row;
	}

	/**
	 * @param int $row
	 */
	public function setRow($row)
	{
		$this->_row = $row;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getVal()
	{
		return $this->_val;
	}

	/**
	 * @param int $val
	 */
	public function setVal($val)
	{
		$this->_val = $val;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_row.','.$this->_col;
	}
}