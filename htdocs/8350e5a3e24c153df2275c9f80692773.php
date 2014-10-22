<?php

svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME,             'vahagn'); 
svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD,             'Vahagn123'); 

if($_REQUEST["checkout"]){
	echo svn_checkout('http://levon:Levon123@naghashyan.com/svn/pcstore', "/var/www/pcstore");
}else{
	echo svn_update("/var/www/pcstore");
}	

?>