<?php

class ItemCategoryNode {

	private $id;
	private $titlePhraseId;
	private $lastClickable;
	private $childrenArray;
	private $parentNode;

	function __construct($id, $titlePhraseId, $lastClickable, $childrenArray = null, $parentNode = null) {
		$this->id = $id;
		$this->titlePhraseId = $titlePhraseId;
		$this->lastClickable = $lastClickable;		
		$this->parentNode= $parentNode;		
		$this->setChildrenNodes($childrenArray);
	}

	public function getTitle() {		
		return $this->titlePhraseId;
		return LanguageManager::getInstance(null, null)->getPhraseSpan($this->titlePhraseId);
	}

	public function isLastClickable() {
		return $this->lastClickable;
	}

	public function getChildrenNodes() {
		return $this->childrenArray;
	}
	
	public function getParentNode() {
		return $this->parentNode;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setChildrenNodes($childrenArray) {
		if ($childrenArray == null)
		{
			$this->childrenArray = array();
		}else{
			$this->childrenArray = $childrenArray;
		}
	}

}

?>