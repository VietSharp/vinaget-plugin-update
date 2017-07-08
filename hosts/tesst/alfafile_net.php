<?php

class dl_alfafile_net extends Download {
    
    public function CheckAcc($cookie){
        $data = $this->lib->curl("http://alfafile.net/user", "{$cookie}", "");
        if(stristr($data, 'Premium, till')) return array(true, "Until " .$this->lib->cut_str($data, 'Premium, till', ' </span>'));
        
	//	else if(stristr($data, '<a href="/account.php">') && !stristr($data, '_type">PREMIUM</div>')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    
    public function Login($user, $pass){
        $data = $this->lib->curl("http://alfafile.net/user/login/?url=%2F", "", "email={$user}&password={$pass}&remember_me=1&fp=2459914516");
		return $this->lib->GetCookies($data);
    } ///email={$user}&password={$pass}&remember_me=1&fp=2459914516
	
    public function Leech($url) {
		
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if($this->isredirect($data)) return trim($this->redirect);
		if(stristr($data, 'Sorry, this page does not exist.') || stristr($data, 'Please, try again later or go to')) $this->error("dead", true, false, 2);
		 elseif(!$this->isredirect($data)) {
			$data = $this->lib->curl($url, $this->lib->cookie, '');
			
		}    
		else  return trim($this->redirect);
		return false;
    }
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* alafile.net Download Plugin by Happy - Ken-1230 [04.11.2015]
* Downloader Class By [FZ]
*/
?>