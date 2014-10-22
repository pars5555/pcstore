<?php
require_once (CLASSES_PATH . "/util/TreeView/TreeNode.php");
abstract class TreeViewModel {
	private $rootNode;
	/**
	 * array(key => Node, key => Node, ...)
	 */
	private $node_map;
	/**
	 * array(parent_key => array(child1_key, child2_key, child3_key ...), ... )
	 */
	private $node_hierachy;

	/**
	 * array(key => true, key => false, ...)
	 */

	private $node_expand;

	function __construct($rootKey, $rootTitle, $data = null, $expand = true) {
		$this->rootNode = new TreeNode($rootKey, $rootTitle, null,$data);
		$this->node_map[$rootKey] = $this->rootNode;		
		$this->node_expand[$rootKey] = $expand;
	}

	function getRootNode() {
		return $this->rootNode;
	}

	function insertNode($parentNodeKey, $node, $expand = true) {
		assert(isset($this->node_map[$parentNodeKey]));
		assert(!isset($this->node_hierachy[$parentNodeKey][$node->getKey()]));
		$this->node_map[$node->getKey()] = $node;
		$this->node_hierachy[$parentNodeKey][$node->getKey()] = $node;
		$this->node_map[$parentNodeKey]->addChild($node);
		$this->node_expand[$node->getKey()] = $expand;
		
	}

	function _insertNode($parentNodeKey, $nodeKey, $nodeTitle, $data = null, $expand = true) {
		assert(isset($this->node_map[$parentNodeKey]));
		assert(!isset($this->node_hierachy[$parentNodeKey][$nodeKey]));		
		$node = new TreeNode($nodeKey, $nodeTitle,  $this->node_map[$parentNodeKey], $data);		
		$this->insertNode($parentNodeKey, $node, $expand);
	}

	function removeNode($node) {
		$key = $node->getkey();
		assert(isset($this->node_map[$key]));
		//node exists
		$node = $this->node_map[$key];
		$parentNode = $node->getParentNode();
		assert(isset($parentNode));
		$parentNodeKey = $parentNode->getKey();
		$this->node_map[$parentNodeKey]->removeChild($key);
		unset($this->node_map[$key]);
		unset($this->node_hierachy[$parentNodeKey][$key]);
		if (isset($this->node_expand[$key])) {
			unset($this->node_expand[$key]);
		}
	}

	function collapseNode($node) {
		$key = $node->getkey();
		assert(isset($this->node_map[$key]));
		assert(isset($this->node_expand[$key]));
		$this->node_expand[$key] = false;		
	}
	
	function expandNode($node) {
		$key = $node->getkey();
		assert(isset($this->node_map[$key]));
		assert(isset($this->node_expand[$key]));
		$this->node_expand[$key] = true;		
	}

	function isNodeExpanded($node)
	{
		return $this->node_expand[$node->getkey()];				
	}

	function getNodeData($node)
	{					
		return $this->node_map[$node->getkey()]->getData();			
	}

	abstract function getNodeStringData($node);	
	
	function getNode($node) {
		return $this->node_map[$node->getkey()];
	}

	function getParentNode($node) {
		$key = $node->getkey();
		assert(isset($this->node_map[$key]));
		//node exists
		$node = $this->node_map[$key];
		$parentNode = $node->getParentNode();
		assert(isset($parentNode));
		$parentNodeKey = $node->getParentNode();
		assert(isset($this->node_map[$parentNodeKey]));
		//parent exists
		return $parentNode;
	}

}
?>