<?php
namespace Skiing;


class Map
{
	/** @var array */
	protected $_data = array();

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->_validate($data);
		$this->_data = $data;
	}

	/**
	 * @param array $data
	 */
	private function _validate(array $data)
	{
		$rows = count($data);

		foreach ($data as $row)
		{
			$cols = count($row);

			if ($cols != $rows)
			{
				//echo "\ndata cols: ".$cols."\n";var_dump($row);echo "\n";
				throw new \InvalidArgumentException('$data must be a square array');
			}
		}
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * @param string $filename
	 * @return Map
	 */
	public static function createFromSampleTextFile($filename)
	{
		if (!file_exists($filename))
		{
			throw new \InvalidArgumentException('File "'.$filename.'" does not exist');
		}

		if (($handler = fopen($filename, 'r')) === false)
		{
			throw new \InvalidArgumentException('Could not open the file "'.$filename.'" for read');
		}

		$data = array();

		while (!feof($handler))
		{
			$line = fgets($handler, 16384);

			if (!$line = trim($line, " \t\r\n"))
			{
				continue;
			}

			$parts = explode(' ', $line);

			if (!$parts)
			{
				continue;
			}

			if ((count($parts) == 2) AND ($parts[0] == $parts[1]))
			{
				continue;
			}

			foreach ($parts as &$part)
			{
				$part = (int)$part;
			}
			unset($part);

			$data[] = $parts;
		}

		fclose($handler);
		return new self($data);
	}
}