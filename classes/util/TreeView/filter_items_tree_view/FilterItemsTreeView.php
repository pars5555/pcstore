<?php
require_once (CLASSES_PATH . "/util/TreeView/TreeView.php");
require_once (CLASSES_PATH . "/managers/search/ItemSearchManager.class.php");
class FilterItemsTreeView extends TreeView {
	private $selectedCategoryId;
	function __construct($treeViewModel, $showRoot, $treeViewId, $config,  $selectedCategoryId = null) {
		parent::__construct($treeViewModel, $showRoot, $treeViewId);
		$this->rowsIndent = 20;
		$this->selectedCategoryId = $selectedCategoryId;
		$this->config = $config;
	}

	function getNodeTitle($node) {
		$data = $this->treeViewModel->getNodeData($node);
		if ($data != null) {
			$title = $data->getDisplayName();
			if ($this->selectedCategoryId != $data->getId()) {
				$itemSearchManager=  ItemSearchManager::getInstance(null, null); 
				$url= HTTP_PROTOCOL . HTTP_HOST .'/search?'.$itemSearchManager->getUrlParams('cid', $data->getId());
				
				return '<a href="'.$url.'" 
					
					id="category_link^' . $data->getId() . '" class="category_links" style= "color:#004B91;text-decoration:none;font-size:12px">' . $title . '</a>';
			} else {
				return '<span style= "color:black;font-weight:bold;font-size:12px;">' . $title . '</span>';
			}
		} else {
			return $node->getTitle();
		}
	}

	function getTitleLeftControlHTML($node) {
		return null;
	}

	function getNodeStringData($node) {
		return $this->treeViewModel->getNodeStringData($node);
	}

}
?>