<?php

class dl_extmatrix_com extends Download {
    
	public function CheckAcc($cookie){
		$data = $this->lib->curl("http://www.extmatrix.com/", $cookie, "");
		if(stristr($data, 'Premium End:')) return array(true, "Until ".$this->lib->cut_str($this->lib->cut_str($data, 'Premium End:</td>', '<tr'), '<td>', '</td>'));
		else if(stristr($data, 'Free Member')) return array(false, "accfree");
		else return array(false, "accinvalid");
	}
    
    public function Login($user, $pass){
        $data = $this->lib->curl("http://www.extmatrix.com/login.php", "", "user={$user}&pass={$pass}&captcha=&submit=Login&task=dologin&return=.%2Fmembers%2Fmyfiles.php");
		$cookie = $this->lib->GetAllCookies($data);
		return $cookie;
    }
	
    public function Leech($url) {
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if(stristr($data,'>The file you have requested does not exists.<')) $this->error("dead", true, false, 2);
		elseif(stristr($data,'><b>Password<')) 	$this->error("reportpass", true, false);
		elseif(stristr($data,'You have reached the download-limit')) $this->error("LimitAcc", true, false);
		elseif(!$this->isredirect($data)) {
			$post = $this->parseForm($this->lib->cut_str($data, '<Form name="F1"', '</Form>'));
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(preg_match('%span>(\s+)<a href="(.*)">(.*)</a%U',$data,$linkpre)) return trim($linkpre[2]);
		}
		else  return trim($this->redirect);
		return false;
    }
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Extmatrix.com Download Plugin by rayyan2005
* Downloader Class By [FZ]
*/
?>