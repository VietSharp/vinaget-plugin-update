<?php

class dl_turbobit_net extends Download {

    public function CheckAcc($cookie) {
        $data = $this->lib->curl("https://turbobit.net", "user_lang=en;".$cookie, "");
        if (stristr($data, "note'>Turbo access till")) {
			$bw = $this->lib->curl("http://turbobit.net/05vs08q3kkse.html", "user_lang=en;".$cookie, "");
			if(stristr($bw, '> limit of premium downloads'))   return array(true, "LimitAcc");
			else return array(true, "Until ".$this->lib->cut_str($data, 'Turbo access till','</span><a class=\'right\''));
        }
		else if(stristr($data, '<u>Turbo Access</u> denied.')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
         
    public function Login($user, $pass){
        $data = $this->lib->curl("https://turbobit.net/user/login", "user_lang=en", "user[login]={$user}&user[pass]={$pass}&user[captcha_type]=&user[captcha_subtype]=&user[submit]=Sign+in&user[memory]=on");
        $cookie = "user_lang=en;".$this->lib->GetCookies($data);
        return $cookie;
    }
         
    public function Leech($url) {
        if(strpos($url, "/download/free/") == true) {
			$gach = explode('/', $url);
			$url = "https://turbobit.net/{$gach[5]}.html";		
		}
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		$this->save($this->lib->GetCookies($data));
        if(stristr($data,'site is temporarily unavailable') || stristr($data,'This document was not found in System')) $this->error("dead", true, false, 2);
        elseif(stristr($data,'Searching for the file...Please wait… ')) $this->error("dead", true, false, 2);
        elseif(stristr($data, 'You have reached the <a href=\'/user/messages\'>daily</a> limit of premium downloads') || stristr($data, 'You have reached the <a href=\'/user/messages\'>monthly</a> limit of premium downloads')) $this->error("LimitAcc");
		elseif(stristr($data, '<u>Turbo Access</u> denied')) $this->error("blockAcc", true, false);
 		elseif(preg_match('@https?:\/\/turbobit\.net\/\/download\/redirect\/[^"\'><\r\n\t]+@i', $data, $link))	 return trim($link[0]);
		return false;
    }
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Turbobit Download Plugin
* Downloader Class By [FZ]
* Fixed By djkristoph
* Fixed check account by giaythuytinh176 [28.7.2013]
*/
?>