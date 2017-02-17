<?php

class dl_rapidgator_net extends Download {

	public function CheckAcc($cookie){
		$data = $this->lib->curl("http://rapidgator.net/api/user/info?{$cookie}", "", "");
		$page  = @json_decode($data, true);
		if(stristr($page['response_details'], 'Error: Session not exist')) return array(false, "accinvalid");
		elseif(stristr($page['response'], 'expire_date')) {
			
		//	else return array(true, "Until ".$this->lib->cut_str($data, ' ">Premium','</a></li>')." <br/>Bandwith available:" .$this->lib->cut_str($this->lib->cut_str($data, 'Bandwith available</td>','<div style='), '<td>','</br>'));
		}
		else return array(false, "ok");
	}
	
	public function Login($user, $pass){
		$data = $this->lib->curl("http://rapidgator.net/api/user/login?username={$user}&password={$pass}", "", "");
		$page  = @json_decode($data, true);
		$data1 = "sid=" . $page['response']['session_id'];
		$cookie = $data1;
		return $cookie;
	}
	
    public function Leech($url) {
		$data = $this->lib->curl($url,"lang=en;".$this->lib->cookie,"");
		if(stristr($data, "You have reached quota of downloaded information") || stristr($data, "You have reached daily quota")) $this->error("LimitAcc");
		elseif(stristr($data,'File not found</div>'))  $this->error("dead", true, false, 2);
		elseif(preg_match('@https?:\/\/\w+\.rapidgator\.net\/\/\?r=download\/index[^"\'><\r\n\t]+@i', $data, $giay))
		return trim($giay[0]);
		return false;
    }

}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Rapidgator Download Plugin 
* Downloader Class By [FZ]
* Add check account by giaythuytinh176 19.7.2013
* Fix check account by rayyan2005 24.4.2014
*/
?>