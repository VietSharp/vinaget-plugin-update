<?php

class dl_uptobox_com extends Download {
    
    public function CheckAcc($cookie){
        $data = $this->lib->curl("http://uptobox.com/?op=my_account", "lang=english;{$cookie}", "");
        if(stristr($data, 'Premium-Account expire')) return array(true, "Until ".$this->lib->cut_str($data, '>Premium-Account expire:', '</'));
        else if(stristr($data, 'My affiliate link:') && !stristr($data, 'Premium-Account expire')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    
    public function Login($user, $pass){
        $data = $this->lib->curl("https://login.uptobox.com/logarithme", "lang=english", "login={$user}&password={$pass}&op=login");
        $cookie = "lang=english;{$this->lib->GetCookies($data)}";
		return $cookie;
    }
    public function Leech($url) {
		list($url, $pass) = $this->linkpassword($url);
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if($pass) {
			$post = $this->parseForm($this->lib->cut_str($data, '<Form', '</Form>'));
			$post["password"] = $pass;
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(stristr($data,'Wrong password'))  $this->error("wrongpass", true, false, 2);
			elseif(preg_match('/a href="(https?:\/\/.*\/d\/.*)">Click here/i', $data, $link))	return trim($link[1]);
		}
		if(stristr($data,'type="password" name="password'))  $this->error("reportpass", true, false);
		elseif(stristr($data,'The file was deleted by its owner') || stristr($data,'Page not found / La page')) $this->error("dead", true, false, 2);
		elseif(!$this->isredirect($data)) {
			$post = $this->parseForm($this->lib->cut_str($data, '<Form', '</Form>'));
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(preg_match('/a href="(https?:\/\/.*\/d\/.*)">Click here/i', $data, $link))	return trim($link[1]);
		}
		else  return trim($this->redirect);
		return false;
    }
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Uptobox Download Plugin
* Downloader Class By [FZ]
* Support file password by giaythuytinh176 [26.7.2013][18.9.2013][Fixed]
* Fix by Rayyan2005
*/
?>