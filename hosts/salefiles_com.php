<?php

class dl_salefiles_com extends Download {
    
    public function CheckAcc($cookie){
        $data = $this->lib->curl("http://salefiles.com/?op=my_account", $cookie, "");
        if(stristr($data, 'Premium account expire:')) return array(true, "Until ".$this->lib->cut_str($data, 'Premium account expire:</TD><TD><b>','</b></TD><TD><input')." <br/>Bandwith available:" .$this->lib->cut_str($data, 'available today:</TD><TD><b>', '</b></TD></TR>'));
			
		else if(stristr($data, '<strong>REGISTERED</strong> ')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    
    public function Login($user, $pass){
        $data = $this->lib->curl("http://salefiles.com/", "", "op=login&redirect=http%3A%2F%2Fsalefiles.com%2F&login={$user}&password={$pass}");
		// op=login&redirect=http%3A%2F%2Fsalefiles.com%2F&login={$user}&password={$pass}
        $cookie = $this->lib->GetAllCookies($data);
		return $cookie;
    }

    public function Leech($url) {
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if($this->isredirect($data))	return trim($this->redirect);
		elseif(stristr($data,'<h2>File Not Found</h2>') || stristr($data,'<h3>The file was removed by administrator</h3>')) $this->error("dead", true, false, 2);
		elseif(stristr($data,'You have reached the download-limit')) 	$this->error("LimitAcc", true, false);
 		elseif(!$this->isredirect($data)) {
			$post = $this->parseForm($this->lib->cut_str($data, '<Form name="F1"', '</Form>'));
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if($this->isredirect($data))	return trim($this->redirect);
		}  
		else  return trim($this->redirect);
		return false;
    }
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* subyshare Download Plugin by hitpro [18.10.2015]
* Downloader Class By [FZ]
*/
?>