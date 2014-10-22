<?php
require_once (CLASSES_PATH . "/managers/search/ItemSearchManager.class.php");

class ItemsCategoryMenuView {

	private $model;
	private $showRoot;
	private $config;

	function __construct(ItemCategoryModel $itemCategoryModel,$categories_count_array, $config, $showRoot = false, $rootHtmlElementId = null) {
		$this->model = $itemCategoryModel;
		$this->config= $config;
		$this->showRoot = $showRoot;
		$this->categories_count_array = $categories_count_array;
		if ($rootHtmlElementId != null) {
			$this->rootHtmlElementId = $rootHtmlElementId;
		} else {
			$this->rootHtmlElementId = uniqid();
		}
	}

	public function display() {
		$ret = '<ul style="position:absolute;z-index:100;white-space:nowrap;left:0;right:10px;border-radius:0;" id="' . $this->rootHtmlElementId . '">';
		$rootNode = $this->model->getRootNode();
		if ($this->showRoot) {
			$ret .= $this->drawNodeTree($rootNode);
		} else {
			$nodeChildren = $this->model->getNodeChildren($rootNode);
			foreach ($nodeChildren as $chNode) {
				$ret .= $this->drawNodeTree($chNode);
			}
		}
		$ret .= '</ul><script>jQuery("#'.$this->rootHtmlElementId .'").menu();</script>';
		echo $ret;
	}

	private function drawNodeTree(ItemCategoryNode $node) {
		$itemSearchManager = ItemSearchManager::getInstance($this->config, null); 
		$url= $itemSearchManager->getUrlParams('cid', $node->getId());
			$categoryTotalItemsCount = array_key_exists($node->getId(), $this->categories_count_array)?	$this->categories_count_array[$node->getId()]:0;
		$ret = '<li style="min-width: 100px;"><a href="'.$url.'" category_id="'.$node->getId().'">' . $node->getTitle() .' ('.$categoryTotalItemsCount.')'. '</a>';
		
		$nodeChildren = $this->model->getNodeChildren($node);
		if (!empty($nodeChildren)) {
			$ret .= '<ul>';
			foreach ($nodeChildren as $childNode) {
				$ret .= $this->drawNodeTree($childNode);
			}
			$ret .= '</ul>';
		}
		$ret .= '</li>';
		return $ret;
	}

}

?>