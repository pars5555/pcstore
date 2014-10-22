<?php
 require_once (CLASSES_PATH . "/util/TreeView/TreeViewModel.php");
 
 
class ItemsCategoryTreeViewModel extends TreeViewModel {
	function __construct($rootKey, $rootTitle, $data = null, $expand = true) {
		parent::__construct($rootKey, $rootTitle, $data, $expand);
	}

	function getNodeStringData($node) {
		if ($node->getData()) {
			return $node->getData()->toJSON();
		} else
			return "";
	}

}
?>