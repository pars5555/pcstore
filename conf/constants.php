<?php
/*
 * PATHES
 */
date_default_timezone_set('Asia/Yerevan');
mb_internal_encoding( 'UTF-8' );

define('HTTP_PROTOCOL', isset($_SERVER["HTTPS"])?"https://":"http://");	
		
define('HTTP_HOST', $_SERVER["HTTP_HOST"]."");
if(strrpos ( HTTP_HOST, "." )){
	define('HTTP_ROOT_HOST', substr(HTTP_HOST, strrpos ( HTTP_HOST, ".", -(strlen(HTTP_HOST) - strrpos ( HTTP_HOST, "." )+1) )));
}else{
	define('HTTP_ROOT_HOST', HTTP_HOST);
}

//---defining document root
if($_SERVER["DOCUMENT_ROOT"]){
	$s_root = $_SERVER["DOCUMENT_ROOT"];
}else{
	$s_root = "/var/www/pcstore";
}

//---defining document root
define('DOCUMENT_ROOT', $s_root."/htdocs");//for dev testing version
//define(DOCUMENT_ROOT, $_SERVER["DOCUMENT_ROOT"]);//for live version

//---defining HOMEPAGE ROOT root
$FAZRoot=substr ( DOCUMENT_ROOT, 0, strrpos  ( DOCUMENT_ROOT, "/htdocs")  );

define('FAZ_ROOT', $FAZRoot);

//---defining smarty root

define('SMARTY_DIR', FAZ_ROOT."/classes/lib/smarty/");

//---defining config file path
define('CONF_PATH', FAZ_ROOT."/conf");

//---defining classes paths
define('CLASSES_PATH', FAZ_ROOT."/classes");

//---defining smarty paths
define('TEMPLATES_DIR', FAZ_ROOT."/templates");
define('CACHE_DIR', TEMPLATES_DIR."/cache");
define('COMPILE_DIR', TEMPLATES_DIR."/compile");
define('CONFIG_DIR', TEMPLATES_DIR."/config");

//---defining data dir path
define('DATA_DIR', FAZ_ROOT."/data");
define('DATA_TEMP_DIR', DATA_DIR."/temp");

//---defining sitemaps dir path
define('SITEMAP_DIR', DATA_DIR."/sitemap");

//---defining data bin path
define('BIN_DIR', FAZ_ROOT."/bin");

//---defining image root dir
define('IMAGE_ROOT_DIR', FAZ_ROOT."/data/images/");

//---defining interface images dir
define('CSS_ROOT_DIR', FAZ_ROOT."/htdocs/css");
define('IMG_ROOT_DIR', FAZ_ROOT."/htdocs/img");
define('HTDOCS_TMP_DIR', FAZ_ROOT."/htdocs/tmp");
define('HTDOCS_TMP_DIR_ATTACHMENTS', HTDOCS_TMP_DIR."/attachments");
?>