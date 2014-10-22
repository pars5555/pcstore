<?php
// $pageCount = 50;
// $pages=0;
// $showedPage = 5;
// if($_REQUEST["pages"]){
// $pages = $_REQUEST["pages"]-1;
// }
//
// foreach($pages as $page){
// $page=$pageCount/$showedPage;
// }
// var_dump($pages);exit;

$assocArr = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'j' => array('h' => 'H', 'k' => array('1' => array('1' => 'First2', '2' => 'Second2'), '2' => 'Second'), 'l' => 'L'));

$tab = 0;
$optionText = getDeepArr($assocArr, $tab);
$str = "<select>";
$str .= $optionText;
$str .= "</select>";

echo $str;
//var_dump($optionText);
function getDeepArr($assocArr, $tab) {
	$tab++;
	foreach ($assocArr as $arrayValue) {
		
		if (is_array($arrayValue)) {
			//var_dump($tab);
			$optionText .= getDeepArr($arrayValue, $tab);
		}else{
			$tabing = "";
			for($i=1;$i<$tab;$i++ ){
				$tabing .= " - ";
			}
			//var_dump($tab." - - - ".$arrayValue);
			$optionText .= "<option value=''>".$tabing.$arrayValue."</option>";
		}
	}
	return $optionText;
}
?>