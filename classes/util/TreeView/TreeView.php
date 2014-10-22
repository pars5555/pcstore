<?php

abstract class TreeView {

	protected $treeViewModel;
	protected $showRoot;
	protected $treeViewId;
	protected $rowsIndent;

	function __construct($treeViewModel, $showRoot, $treeViewId) {
		$this->treeViewModel = $treeViewModel;
		$this->showRoot = $showRoot;
		$this->treeViewId = $treeViewId;
		$this->rowsIndent = 10;
	}

	/**
	 * Default value is 10
	 */
	public function setRowsIndent($rowsIndent) {
		$this->rowsIndent = $rowsIndent;
	}

	public function display($expandable) {
		$root = $this->treeViewModel->getRootNode();
		echo "<ul style='list-style: none;padding-left:15px' id = '" . $this->treeViewId . "' class = 'rootktvUl'>";
		if ($this->showRoot === true) {
			$this->displayNode($root, $expandable, null);
		} else {
			if ($root->getChildren()) {
				foreach ($root -> getChildren() as $key => $child) {
					$this->displayNode($child, $expandable, $root);
				}
			}
		}
		echo "</ul>";
	}

	private function displayNode($node, $expandable, $parentNode) {
		$leaf = $node->isLeaf();
		$isExpanded = $this->treeViewModel->isNodeExpanded($node);
		$nodeData = $this->treeViewModel->getNodeStringData($node);
		echo "<li id='li^" . $node->getKey() . "'>";
		echo "<input type = 'hidden' value='" . $nodeData . "'   id='tw_data^" . $node->getKey() . "'   >";
		if ($parentNode) {
			echo "<input type = 'hidden' value='" . $parentNode->getKey() . "'   id='tw_parent^" . $node->getKey() . "'   >";
		}

		echo "<div id='ktvTopDiv^" . $node->getKey() . "' class = 'ktvTop'>";
		if ($expandable) {
			if (!$leaf) {
				echo "<span id='ktvPMSpan^" . $node->getKey() . "' class='ktvPM  " . ($isExpanded ? 'ktvMinus' : 'ktvPlus') . "'> </span>";
			}
		}
		$titleLeftControlHTML = $this->getTitleLeftControlHTML($node);
		if ($titleLeftControlHTML && strlen($titleLeftControlHTML) > 0) {
			echo $titleLeftControlHTML;
		}
		echo "<span class='ktvText'>" . $this->getNodeTitle($node) . "</span>";
		echo "</div>";

		echo "</li>";
		if ($leaf) {
			//echo $node->getTitle().' is Leaf';
			return;
		}

		echo "<ul id='ktvUI^" . $node->getKey() . "' class = 'ktvUl' style='list-style: none;display:" . ($isExpanded ? 'block' : 'none') . ";padding-left:" . $this->rowsIndent . "px;'>";
		if (count($node->getChildren()) > 0) {
			$childrenKeys = array();
			foreach ($node->getChildren() as $key => $child) {
				$this->displayNode($child, $expandable, $node);
				$childrenKeys[] = $child->getKey();
			}
			echo "<input type = 'hidden' value='" . implode(",", $childrenKeys) . "'   id='tw_children_keys^" . $node->getKey() . "'   >";
		}

		echo "</ul>";
	}

	abstract function getNodeTitle($node);

	abstract function getTitleLeftControlHTML($node);
}
?>