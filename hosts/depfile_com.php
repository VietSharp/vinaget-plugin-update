<?php	

class dl_depfile_com extends Download {
    
    public function CheckAcc($cookie){
        $data = $this->lib->curl("https://depfile.com/myspace/space/premium", "sdlanguageid=2;{$cookie}", "");
        if(stristr($data, "<a class='ok' href='/myspace/space/premium'>")) return array(true, "Until ".$this->lib->cut_str($data, "premium'>", '<img src=\''));
		elseif(stristr($data,"<a class='bad' href='/myspace/space/premium'>") ) $this->error("accfree", true, false, 2);
		else return array(false, "accinvalid");
    }
    
    public function Login($user, $pass){
        $data = $this->lib->curl("https://depfile.com/", "sdlanguageid=2", "login=login&loginemail={$user}&loginpassword={$pass}&submit=login&rememberme=on");
        $cookie = "sdlanguageid=2;{$this->lib->GetCookies($data)}";
        return $cookie;
    }
     
    public function Leech($url) {
		//if(!stristr($url, "https")) {
		//	$url = str_replace('http', 'https', $url);
		//}
		$data = $this->lib->curl($url, $this->lib->cookie, "");
        if(stristr($data,'Page Not Found!') || stristr($data,'File was not found in the') || stristr($data,'Provided link contains errors')) $this->error("dead", true, false, 2);
        elseif(stristr($data,'You spent limit on urls') ) $this->error("limitacc", true, false, 2);
        elseif(stristr($data,'File is available only for Premium users.') ) $this->error("accfree".$user, true, false, 2);
		elseif(preg_match('@https?:\/\/[a-z]+\.depcloud\.com\/premdw\/\d+\/[a-z0-9]+\/[^"\'<>\r\n\t]+@i', $data, $giay)) 
        return trim($giay[0]);
		return false;
		
    }//http://dl.depcloud.com/premdw/6293446/b3458ab7da5c6e47f12a2ef9c9c50825/Collection1-Video001._____p101-_8_d__traz-new_1108_-_mmm.flv
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* depfile.com Download Plugin
* Downloader Class By [FZ]
* Download plugin by hitpro [25.11.2015]
*/
?>