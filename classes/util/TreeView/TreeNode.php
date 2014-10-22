<?php
class TreeNode {
	private $key;
	private $title;
	private $childrenNodes;
	private $parentNode;
	private $data;
	

	function __construct($key, $title, $parentNode, $data = null) {
		$childrenNodes = array();
		$this->key = $key;
		$this->title = $title;
		$this->parentNode = $parentNode;		
		$this->data = $data;

	}

	public function addChild($childNode) {
		assert(!isset($childrenNodes[$childNode->getKey()]));
		$this->childrenNodes[] = $childNode;

	}

	public function removeChild($childKey) {
		assert(isset($this->childrenNodes[$childKey]));
		unset($childrenNodes[$childKey]);
	}

	public function getKey() {
		return $this->key;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getChildrenCount()
	{
		return count($this->childrenNodes);	
	}
	
	public function getParent()
	{
		return $this->parentNode;	
	}
	
	public function getChildren()
	{
		return $this->childrenNodes;	
	}
	
	public function isLeaf()
	{
		return (count($this->childrenNodes) === 0);	
	}
	
	public function getData()
	{		
		return $this->data;				
	}
}
?>