<?php
 
class dl_filenext_com extends Download {
 
    public function CheckAcc($cookie){
        $data = $this->lib->curl("http://www.filenext.com/?op=my_account", $cookie, "");
        if(stristr($data, '<TD>Premium account expire</TD>')) 
            return array(true, "Premium expire: ".$this->lib->cut_str($data, 'account expire</TD><TD><b>','</b> <input type="button" class="" value="Extend Premium account"'). "<br>Traffic available:". $this->lib->cut_str($data, 'Traffic available today</TD><TD><b>','</b></TD></TR>'));
		else if(stristr($data, '<TD>My affiliate link</TD>') && !stristr($data, '<TD>Premium account expire</TD>')) return array(false, "accfree");
        else return array(false, "accinvalid");
    }
     
    public function Login($user, $pass){
        $data = $this->lib->curl("http://www.filenext.com/", "", "op=login&token=&rand=&redirect=&login={$user}&password={$pass}");
        $cookie = $this->lib->GetCookies($data);
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
		elseif((stristr($data, "You have reached the")))  $this->error("LimitAcc", true, false, 2);
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
* filenext.com Download Plugin 
* Downloader Class By [FZ]
* Created: Happpy VNZ-Leech.Com [17.02.17]
*/
?>