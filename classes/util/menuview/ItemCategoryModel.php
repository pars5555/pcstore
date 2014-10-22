<?php
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/util/menuview/ItemCategoryNode.php");

class ItemCategoryModel {

	private $rootNodeId;
	private $categoryManager;
	private $categoryHierarchyManager;
	private $rootNode;

	function __construct($rootId = 0) {
		$this->rootNodeId = $rootId;
		$this->categoryManager = CategoryManager::getInstance(null, null);
		$this->categoryHierarchyManager = CategoryHierarchyManager::getInstance(null, null);
		$this->rootNode = $this->initNodes($this->rootNodeId);
	}
	
	private function initNodes($catId, $parentNode = null)
	{
		$catDto = $this->categoryManager->getCategoryById($catId);
		assert(isset($catDto));				
		$retNode = new ItemCategoryNode($catId, $catDto->getDisplayName(), $catDto->getLastClickable(), null, $parentNode);
		
		$categoryChildrenIdsArray = $this->categoryHierarchyManager ->getCategoryChildrenIdsArray($catId);
		$childrenNodes = array();
		if ($catDto->getLastClickable() == 0 && !empty($categoryChildrenIdsArray))
		{
			foreach ($categoryChildrenIdsArray as $childId) {
				$childrenNodes[] = $this->initNodes($childId, $parentNode);
			}
		}		
		$retNode->setChildrenNodes($childrenNodes);
		return $retNode;
	}
	
	public function getRootNode()
	{
		return $this->rootNode;
	}
	
	public function getNodeChildren(ItemCategoryNode $node)
	{
		return $node->getChildrenNodes();
	}
	
	public function getNodeParent(ItemCategoryNode $node)
	{
		return $node->getParentNode();
	}

}

?>