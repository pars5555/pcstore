<?php
class CategoryPropertyView {
	private $selectedCategoriesId;
	private $groupCategoryId;
	private $categoryManager;
	private $categoryHierarchyManager;

	function __construct($categoryManager, $categoryHierarchyManager, $categories_count_array,$groupCategoryId, $selectedCategoriesId) {
		$this->selectedCategoriesId = $selectedCategoriesId;
		$this->groupCategoryId = $groupCategoryId;
		$this->categoryHierarchyManager = $categoryHierarchyManager;
		$this->categoryManager = $categoryManager;
		$this->categories_count_array = $categories_count_array;
	}

	function display() {
		echo '<div style="width:100%;margin-top:10px;margin-bottom:10px;position:relative;">';
		echo '<div style="font-weight:bold;font-size:14px;color:#AA4400; padding-left:10px;">' . $this->categoryManager->getCategoryById($this->groupCategoryId)->getDisplayName() . '</div>';
		$childrenHierarchyDtos = $this->categoryHierarchyManager->getCategoryChildren($this->groupCategoryId);
		foreach ($childrenHierarchyDtos as $key => $childrenHierarchyDto) {
			echo '<div style="padding-left:30px;padding-top:2px;padding-bottom:2px;">';			
			$categoryTotalItemsCount = array_key_exists($childrenHierarchyDto->getChildId(), $this->categories_count_array)?	$this->categories_count_array[$childrenHierarchyDto->getChildId()]:0;
			$checkedHTMLTag =  (isset($this->selectedCategoriesId)&& in_array($childrenHierarchyDto->getChildId(), $this->selectedCategoriesId))?' checked="checked" ':'';
			$propertyCountTextToBeAppend = empty($checkedHTMLTag)?' ('.$categoryTotalItemsCount.')':'';
			echo '<input category_id="'.$childrenHierarchyDto->getChildId().'" id = "category_property_' . $childrenHierarchyDto->getChildId() . '" class="category_property" type="checkbox" '.$checkedHTMLTag.'/>';
			echo '<label for = "category_property_' . $childrenHierarchyDto->getChildId() . '"  style="line-height:15px;padding-left:5px;">' . $this->categoryManager->getCategoryById($childrenHierarchyDto->getChildId())->getDisplayName() .$propertyCountTextToBeAppend. '</label>';
			echo '</div>';
		}
		echo '</div>';
	}

}
?>