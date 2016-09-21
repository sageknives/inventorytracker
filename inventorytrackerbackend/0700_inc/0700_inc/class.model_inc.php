<?php
//database objects

class Node implements JsonSerializable
{
	public $id;
	public $title;
	public $parent;
	public $isComplete;
	public $dueDate;
	public $lastUpdated;
	public $desc;
	public $createdBy;
	public $assignedTo;
	private $images = array();
	public $children = array();
	
	function __construct($id,$title,$parent,$isComplete,$dueDate,$lastUpdated,$desc,$createdBy,$assignedTo)
	{
		$this->id = $id;
		$this->title = $title;
		$this->parent = $parent;
		$this->isComplete = $isComplete;
		$this->dueDate = $dueDate;
		$this->lastUpdated = $lastUpdated;
		$this->desc = $desc;
		$this->createdBy = $createdBy;
		$this->assignedTo = $assignedTo;
		$this->images = array();
	}

	public function getUpdatedTodos($dateTime,$todoList)
	{
		$results = '';
		$thisDate = date(strtotime($this->lastUpdated));
		//echo $this->lastUpdated;
		$thatDate = split('[ ]', $dateTime);
		//echo $thisDate . '-' . strtotime($thatDate[0]);
		//echo ':' . ($thisDate - strtotime($thatDate[0])) . ', ';
		if($thisDate - strtotime($thatDate[0]) >= 25130) 
		{
			//echo $thisDate . '-' . strtotime($thatDate[0]);
			//echo ':' . ($thisDate - strtotime($thatDate[0]) - 25130) . ', ';
			$diff = strtotime($thisDate[1]) - strtotime($thatDate[1]);
			//echo $diff;
			if($diff+60 > 0)
			{
				$results .= "1";
				$todoList[] = $this;
			}
		}
		$children = $this->getChildren();
		for ($i=0; $i < count($children); $i++) { 
			$todoList = $children[$i]->getUpdatedTodos($dateTime,$todoList);
		}
		return $todoList;
	}
	//this one is called on the update automatically
	public function jsonSerialize()
    {
        return array(
             'id' => $this->id,
             'title' => $this->title,
             'complete' => $this->isComplete,
             'parent' => $this->parent,
             'duedate' => $this->dueDate,
             'lastupdated' => $this->lastUpdated,
             'desc' => $this->desc,
             'createdby' => $this->createdBy,
             'images' => $this->images,
             'children' => $this->children
        );
    }
	public function getId()
	{
		return $this->id;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getParent()
	{
		return $this->parent;
	}
	
	public function getDueDate()
	{
		return $this->dueDate;
	}
	public function getLastUpdated()
	{
		return $this->lastUpdated;
	}
	public function getDesc()
	{
		return $this->desc;
	}
	public function getCreatedBy()
	{
		return $this->createdBy;
	}
	
	function setId($id)
	{
		$this->id;
	}
	
	function setTitle($title)
	{
		$this->title;
	}

	function setParent($parent)
	{
		$this->parent;
	}
	
	function setDueDate($dueDate)
	{
		$this->dueDate;
	}
	
	public function addChild($node)
	{
		$this->children[] = $node;
	}
	
	public function getChildren()
	{
		$this->children = $this->sortByCompleted($this->children);
		return $this->children;
	}
	
	public function addImage($image)
	{
		$this->images[] = $image;
	}
	
	public function getImages()
	{
		return $this->images;
	}
	
	public function sortByCompleted($list)
	{
		for ($i=0; $i < count($list)-1; $i++) 
		{
			for ($j=$i+1; $j < count($list) ; $j++) 
			{ 
				if($this->boolToNum($list[$i]->isCompleted()) > $this->boolToNum($list[$j]->isCompleted()))
				{
					$temp = $list[$i]->getCopy();
					$list[$i] = $list[$j]->getCopy();
					$list[$j] = $temp->getCopy();
				}
			}
		}	
		return $list;
	}
	public function getCopy()
	{
		$copy =new Node($this->id,$this->title,$this->parent,$this->isComplete,$this->dueDate,$this->lastUpdated,$this->desc,$this->createdBy,$this->assignedTo);
		$thisImages = $this->getImages();
		for ($i=0; $i < count($this->images) ; $i++) { 
			$copy->addImage($thisImages[$i]);
		}
		$children = $this->getChildren();
		for ($i=0; $i < count($children) ; $i++) { 
			$copy->addChild($children[$i]->getCopy());
		}
		return $copy;
	}
	public function boolToNum($bool)
	{
		if($bool) return 1;
		return 0;
	}
	public function isCompleted()
	{
		return $this->isComplete;
	}
	
	//this one is called by method name
	public function toArray()
	{
	    $data = array(
	        'id' => $this->id,
	        'title' => $this->title,
	        'parent' => $this->parent,
	        'complete' => $this->isComplete,
	        'duedate' => $this->dueDate,
	        'lastupdated' => $this->lastUpdated,
            'desc' => $this->desc,
            'createdby' => $this->createdBy,
	        'images' => $this->images,
	        'children' => array()
	    );
	
	    foreach ($this->children as $child) {
	        $data['children'][] = $child->toArray();
	    }
		//foreach ($this->images as $child) {
	    //    $data['images'][] = $child;
	    //}
	
	    return $data;
	}	
}

class View
{
	private $id;
	private $title;
	private $parent;
	private $completed;
	private $desc;
	private $dueDate;
	private $images = array();
	private $children = array();
	
	function __construct($id,$title,$parent,$completed,$desc,$dueDate)
	{
		$this->id = $id;
		$this->title = $title;
		$this->parent = $parent;
		$this->completed = $completed;
		$this->desc = $desc;
		$this->dueDate = $dueDate;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getParent()
	{
		return $this->parent;
	}
	
	public function isCompleted()
	{
		return $this->completed;
	}
	
	public function getDesc()
	{
		return $this->desc;
	}
	
	public function getDueDate()
	{
		return $this->dueDate;
	}
	
	public function addImage($image)
	{
		$this->images[] = $image;
	}
	
	public function getImages()
	{
		return $this->images;
	}
	
	public function addChild($node)
	{
		$this->children[] = $node;
	}
	
	public function getChildren()
	{
		return $this->children;
	}
	
}
