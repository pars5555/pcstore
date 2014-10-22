<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {paggination} function plugin
 *
 * Type:     function<br>
 * Name:     paggination<br>
 * Purpose:  echo page paggination
 * @author Vahagn Sookiasian
 * @link http://naghashyan.com
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_paggination($params, &$smarty){

	$s_args = $smarty->_tpl_vars;
	$s_ns_args = $smarty->_tpl_vars["ns"];
	$urlPrefix = $s_ns_args["SITE_PATH"].'/'.$s_ns_args["urlPrefix"];
	if ($s_ns_args["pageCount"] > 1){
		$html = '<p class="paging">';
		if($s_ns_args["page"] > 0){
			$html .= '<a href="'.str_replace("pagination", ($s_ns_args["page"]-1), $urlPrefix).$s_ns_args["pagePath"].'"><<</a>';	
		}

		if($s_ns_args["startPage"] > 0){
			$html .= '<a href="'.$urlPrefix.$s_ns_args["pagePath"].'">...</a>';	
		}
		for($i=$s_ns_args["startPage"];$i<$s_ns_args["endPage"];$i++){
			if($s_ns_args["page"] != $i){
				$html .= '<a href="'.$urlPrefix.$i.'.html'.$s_ns_args["pagePath"].'">'.($i+1).'</a>';	
			}else{
				$html .= ($i+1);	
			}	
		}
		if ($s_ns_args["endPage"]< $s_ns_args["pageCount"]){
			$html .= '<a href="'.$urlPrefix.($s_ns_args["pageCount"]-1).'.html'.$s_ns_args["pagePath"].'">...</a>';	
			
		}
		
		if($s_ns_args["page"] != ($s_ns_args["pageCount"]-1)){
			$html .= '<a href="'.$urlPrefix.($s_ns_args["page"]+1).'.html'.$s_ns_args["pagePath"].'">>></a>';	
		}
		$html .= '</p>';
	}

	return $html; 
}

/* vim: set expandtab: */

?>
