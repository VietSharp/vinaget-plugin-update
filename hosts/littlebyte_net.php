<?php

class dl_littlebyte_net extends Download {
   	public function CheckAcc($cookie){
		$json = str_replace("dummy=", "", $cookie);
		$json = json_decode($json, true);
		if($json["error"] !== 0) {
			return [false, $json["message"]];
		}
		$data = $this->lib->curl("https://real-debrid.com/account", $json["cookie"], "");
		if (strpos($data, '<strong>Free</strong>')) {
			return [false, "accfree"];
		} elseif (strpos($data, '<strong>Premium</strong>')) {
			return [true, "Premium till: " . $this->lib->cut_str($data, "Valid untill: <strong>", "<")];
		}
		return [false, "accinvalid"];
	}

	public function Login($user, $pass){
		$data = $this->lib->curl("https://real-debrid.com/ajax/login.php?user=".urlencode($user)."&pass=".urlencode(md5($pass))."&pin_challenge=&pin_answer=PIN%3A+000000&time=".time(), "", "", 0);
		if (strpos($data, "}{")) $data = "{". $this->lib->cut_str($data, "}{", "}") . "}";
		return "dummy=".$data;
	}
	public function Leech($url) {
		$id = explode('/',$url);
	$id = trim($id[4]);
	$data = $this->curl("http://littlebyte.net/ajax.php","lng=EN","action=getLinks&params[file_id]={$id}&params[key]={$user}");
	if(stristr($data,'Incorrect premium code')) $this->error("accinvalid", true, false);
		elseif(preg_match('%a href=\'(.*)\' style=%U', $data, $redir)){
		return trim($redir[1]);
		
	}
	//	return false;
	}

}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Real-debrid Download Plugin
*/
?>