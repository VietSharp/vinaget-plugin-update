<?php
class dl_filenext_com extends Download {
   
	public function CheckAcc($cookie){
        $data = $this->lib->curl("http://www.filenext.com/?op=my_account", "en-US;{$cookie}", "");
        if(stristr($data, 'Traffic available today')) return array(true, "Traffic available: ".$this->lib->cut_str($data, '<TR><TD>Traffic available today</TD><TD><b>', '</b>'));
        //else if(stristr($data, '<TD>My affiliate link</TD>') && !stristr($data, '<TD>Premium account expire</TD>')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    public function Login($user, $pass){
		$data = $this->lib->curl("http://www.filenext.com/login.html", "en-US", "op=login&login={$user}&password={$pass}&redirect=http://www.filenext.com/login.html");
		$cookie = "en-US;{$this->lib->GetCookies($data)}";
		return $cookie;
    }

    public function Leech($url) {
		list($url, $pass) = $this->linkpassword($url);
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if($pass) {
			$post = $this->parseForm($this->lib->cut_str($data, '<Form name="F1"', '</form>'));
			$post["password"] = $pass;
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(stristr($data,'Wrong password'))  $this->error("wrongpass", true, false, 2);
			else if($this->isredirect($data)) return trim($this->redirect);
		}
		if(stristr($data,'type="password" name="password'))  $this->error("reportpass", true, false);
		elseif (stristr($data,'<b>File Not Found</b>') || stristr($data,'<Title>File Not Found</Title>')) $this->error("dead", true, false, 2);
		elseif (!$this->isredirect($data)) {
			$post = $this->parseForm($this->lib->cut_str($data, '<Form name="F1"', '</form>'));
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
            if($this->isredirect($data)) return trim($this->redirect);
		}
		else return trim($this->redirect);
		return false;
    }
}
/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Filenext Download Plugin
* Downloader Class By [LTT}
*/
?>