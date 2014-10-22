<?php
require_once (CLASSES_PATH . "/util/TreeView/TreeView.php");
class SubCategorySelectionTreeView extends TreeView {

	function __construct($treeViewModel, $showRoot, $treeViewId) {
		parent::__construct($treeViewModel, $showRoot, $treeViewId);
	}

function getNodeTitle($node) {	
		
			return $node->getTitle();

	}

	function getTitleLeftControlHTML($node)
	{
		$data = $this->treeViewModel->getNodeData($node);
		if ($data)
		{
			return "<input class = 'checkboxes' type='checkbox' name='category^".$data->getId()."'/>";						
		}	
		return null;			
	}

	function getNodeStringData($node) {
		return $this->treeViewModel->getNodeStringData($node);
	}

}
?>