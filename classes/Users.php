<?php
include_once('lib/Collection.php');
include_once('classes/User.php');

class Users extends Collection
{
	public function __construct()
	{
		parent::__construct(new User());
	}
	
	public function getBy($properties = array())
	{
		$this->elements = array();
		$rows = $this->getData($properties);
		
		foreach ($rows as $row)
		{
			$this->elements[] = new User($row);
		}
		
		return $this->elements;
	}
}
?>