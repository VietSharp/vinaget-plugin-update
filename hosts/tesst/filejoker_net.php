<?php


class dl_filejoker_net extends Download {
	
	public function CheckAcc($cookie){
		$data = curl("https://filejoker.net/profile", "{$cookie}", "");
		if(stristr($data, 'Premium account expires:')) 
		return array(true, "Until ".$this->lib->cut_str($data, 'Premium account expires:','<a href="/premium" class="btn btn-green">Extend Premium<')."<br>Traffic Available:".$this->lib->cut_str($this->lib->cut_str($data, '<td>Traffic Available:</td>','</tr>'),'<td>','</td>') );
		else return array(false, "accinvalid" ); 
	}
	
	public function Login($user, $pass){
		$data = curl("https://filejoker.net/login", "", "email={$user}&password={$pass}&recaptcha_response_field=&op=login&redirect=&rand=");
		$cookie = $this->lib->GetCookies($data);   ///  email={$user}&password={$pass}&recaptcha_response_field=&op=login&redirect=&rand=
		return $cookie;
	}
	
/*     public function Leech($url) {
		$post = $this->parseForm($this->lib->cut_str($data, '<form action="', '</form>'));
		$data = curl($url, $this->lib->cookie, $post);
	//	die ($data);
		if(stristr($data,'>File Not Found<') || stristr($data,'>404 - Not Found<')) $this->error("dead", true, false, 2);
		elseif(!$this->isredirect($data)) {
			$post = $this->parseForm($this->lib->cut_str($data, '<form name="F1"', '</form>'));
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(preg_match('%<a href="(.*)" class="btn btn-green">Download File</a>%U', $data, $giay));
		//	die ($giay[1]);
			return $giay[1];
		}
		else  return trim($this->redirect);
		return false;
    } */
	    public function Leech($url) {
		//$data = $this->lib->curl($url, $this->lib->cookie, "");
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if($this->isredirect($data))	return trim($this->redirect);
		
		elseif{
			$post = $this->parseForm($data,'<form action="','</form>');
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(preg_match('%a href="(.*)" class="btn btn-green">Download File</a%U',$data,$linkpre)) return trim($linkpre[1]);
			elseif(stristr($data,'File Not Found')) $this->error("dead", true, false, 2);
			elseif(stristr($data,'You have reached your download limit')) $this->error("LimitAcc", true, false);
		}
		//else return trim($this->redirect);
		return false;
    }
}

function curl($url,$cookies,$post,$header=1){
		$ch = @curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));
		if ($cookies) curl_setopt($ch, CURLOPT_COOKIE, $cookies);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER,$url); 
		if ($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_HEADER,1);
		curl_setopt($ch,CURLOPT_COOKIESESSION,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
		$page = curl_exec( $ch);
		curl_close($ch); 
		return $page;
	}
/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Ryushare Download Plugin 
* Downloader Class By [FZ]
* Check account, fixed small error by giaythuytinh176 [18.7.2013]
* Support file password by giaythuytinh176 [29.7.2013]
*/
?>