<?php

class dl_uploadable_ch extends Download {
    
    public function CheckAcc($cookie){
        $data = $this->lib->curl("https://www.uploadable.ch/indexboard.php", "{$cookie}", "");
        if(stristr($data, '_type">PREMIUM</div>')) {
			$tach = explode('<div class="grey_type">', $data);
			return array(true, "Until " .$this->lib->cut_str($tach[2], 'Until', '</div>'));
        }
		else if(stristr($data, '<a href="/account.php">') && !stristr($data, '_type">PREMIUM</div>')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    
    public function Login($user, $pass){
        $data = $this->lib->curl("https://www.uploadable.ch/login.php", "", "userName={$user}&userPassword={$pass}&autoLogin=true&action__login=normalLogin");
		return $this->lib->GetCookies($data);
    }
	
    public function Leech($url) {
		$url = str_replace("http://", "https://", $url);
		$id = explode('/',$url);
		$id = $id[4];
		$url = "https://www.uploadable.ch/file/". $id;
		//die($url);
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if(stristr($data, '>The file could not be found.') || stristr($data, 'This file is no longer available.')) $this->error("dead", true, false, 2);
		elseif(!$this->isredirect($data)) {
			$data = $this->lib->curl($url, $this->lib->cookie, 'download=premium');
			if($this->isredirect($data)) return trim($this->redirect);
		} 
		else  return trim($this->redirect);
		return false;
    }
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Uploadable.ch Download Plugin by giaythuytinh176 [20.2.2014]
* Downloader Class By [FZ]
* Fix http / https / Fix link have space and invalid link by hitpro [01.10.2015]
*/
?>