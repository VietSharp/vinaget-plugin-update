<?php

class dl_littlebyte_net extends Download {
   
	public function Leech($url) {
		$id = explode('/',$url);
	$id = trim($id[4]);
	$data = $this->curl("http://littlebyte.net/ajax.php","lng=EN","action=getLinks&params[file_id]={$id}&params[key]={$this->lib->cookie}");
	if(stristr($data,'Incorrect premium code')) $this->error("accinvalid", true, false);
		elseif(preg_match('%a href=\'(.*)\' style=%U', $data, $redir)){
		$link = str_replace(" ","%20",trim($redir[1]));
		return $link;
	}
		return false;
	}

}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Real-debrid Download Plugin
*/
?>