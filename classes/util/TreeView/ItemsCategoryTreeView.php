<?php
require_once (CLASSES_PATH . "/util/TreeView/TreeView.php");
class ItemsCategoryTreeView extends TreeView {

	function __construct($treeViewModel, $showRoot, $treeViewId) {
		parent::__construct($treeViewModel, $showRoot, $treeViewId);
	}

	function getNodeTitle($node) {
		$data = $this->treeViewModel->getNodeData($node);
		if ($data != null) {
			$title = $data->getDisplayName();			
			$style="";			
			$style .= $data->getLastClickable()?'font-weight: bold;':'';								
			return '<span style= "'. $style .'">'. $title.'</span>';
		} else {
			//this case is only for root!
			return $node->getTitle();
		}
	}
	
	function getTitleLeftControlHTML($node)
	{
		return null;		
	}

}
?>