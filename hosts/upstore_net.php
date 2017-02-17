<?php
class dl_upstore_net extends Download {
   
	public function CheckAcc($cookie){
        $data = $this->lib->curl("http://upstore.net/account/", "{$cookie}", "");
        if(stristr($data, 'eternal premium')) return array(true, "Until ".$this->lib->cut_str($data, '>eternal premium', '</'));
        else if(stristr($data, 'My affiliate link:') && !stristr($data, 'Premium-Account expire')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    public function Login($user, $pass){
		$data = $this->lib->curl("http://upstore.net/account/login/", "", "url=http%253A%252F%252Fupstore.net%252F&email={$user}&password={$pass}&send=sign+in");
		$cookie = "lang=english;{$this->lib->GetCookies($data)}";
		return $cookie;
    }
    public function Leech($url) {
		$url = str_replace('/upsto.re/', '/upstore.net/', $url);
		list($url, $pass) = $this->linkpassword($url);
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if($pass) {
			$post = $this->parseForm($data,'<form action="','</form>');
			$post["password"] = $pass;
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(stristr($data,'Wrong password'))  $this->error("wrongpass", true, false, 2);
			elseif(preg_match('/https?:\/\/d\d+\.upsto\.re\/[a-zA-Z0-9]\/[^|\s|\t|\r|\n"]+/i', $data, $link)) return trim($link[0]);
		}
		if(stristr($data,'type="password" name="password'))  $this->error("reportpass", true, false);
		elseif (stristr($data,'The file was deleted by its owner') || stristr($data,'Page not found / La page')) $this->error("dead", true, false, 2);
		elseif (!$this->isredirect($data)) {
			//$post = $this->parseForm($this->lib->cut_str($data, '<form id="', '</form>'));
			$post = $this->parseForm($data,'<form action="','</form>');
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			//die($data);
			if(preg_match('/https?:\/\/d\d+\.upsto\.re\/[a-zA-Z0-9]\/[^|\s|\t|\r|\n"]+/i', $data, $link)) return trim($link[0]);
			//die($link[0]);
		}
		else return trim($this->redirect); 
		return false;
    }
}
/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* upstore Download Plugin
* Downloader Class By [FZ]
* Support file password by giaythuytinh176 [26.7.2013][18.9.2013][Fixed]
*/
?>