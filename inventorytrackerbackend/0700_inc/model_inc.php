<?php
class container{
	protected $items ='';
	
	function __contruct()
	{
		
	}
	
	function getItems()
	{
		return $this->items;
	}
	
	function addItem($values)
	{
		$this->items = $values;
	}
}
?>