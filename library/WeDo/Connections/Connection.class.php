<?php
class Connection
{
	private $resource;

	public function __construct($resource)
	{
		$this->resource = $resource;
	}
	protected function getResource()
	{
		return $this->resource;
	}
}
?>