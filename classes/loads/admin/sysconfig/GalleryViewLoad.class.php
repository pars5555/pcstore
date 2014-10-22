<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");

class GalleryViewLoad extends AdminLoad {

	public function load() {
		
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/admin/sysconfig/gallery_view.tpl";
	}

}

?>