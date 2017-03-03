<?php

class dl_bigfile_to extends Download {
    
    public function CheckAcc($cookie){
        $data = $this->lib->curl("https://www.bigfile.to/indexboard.php", "{$cookie}", "");
        if(stristr($data, '_type">PREMIUM</div>')) {
			$tach = explode('<div class="grey_type">', $data);
			return array(true, "Until " .$this->lib->cut_str($tach[2], 'Until', '</div>'));
        }
		else if(stristr($data, '<a href="/account.php">') && !stristr($data, '_type">PREMIUM</div>')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    
    public function Login($user, $pass){
        $data = $this->lib->curl("https://www.bigfile.to/login.php", "", "userName={$user}&userPassword={$pass}&autoLogin=true&action__login=normalLogin");
		return $this->lib->GetCookies($data);
    }
	
    public function Leech($url) {
		$url = str_replace("http://", "https://", $url);
		$id = explode('/',$url);
		$id = $id[4];
		$url = "https://www.bigfile.to/file/". $id;
		
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if(stristr($data, '>The file could not be found.') || stristr($data, 'This file is no longer available.')) $this->error("dead", true, false, 2);
		if(stristr($data, 'You have exceeded your download limit')) $this->error("LimitAcc", true, false, 2);
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
* bigfile.to Download Plugin by giaythuytinh176 [20.2.2014]
* Downloader Class By [FZ]
* Fix http / https / Fix link have space and invalid link by hitpro [01.10.2015]
*/
?>