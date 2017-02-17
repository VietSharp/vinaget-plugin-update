<?php
class dl_fshare_vn extends Download {

	public function CheckAcc($cookie){
		$this->lib->curl("https://www.fshare.vn/location/vi", $cookie, "");
		$data = $this->lib->curl("https://www.fshare.vn/account/infoaccount", $cookie, "");
		if(stristr($data, '<dd>VIP</dd>') && stristr($data, '<dt>Hạn dùng</dt>')) return array(true, "Until ".$this->lib->cut_str($this->lib->cut_str($data, '<dt>Hạn dùng</dt>','<dt>Fxu hiện có</dt>'), '<dd>','</dd>'));
		elseif(stristr($data, '<dd>PROMO</dd>') && stristr($data, '<dt>Hạn dùng</dt>')) return array(true, "Until ".$this->lib->cut_str($this->lib->cut_str($data, '<dt>Hạn dùng</dt>','<dt>Fxu hiện có</dt>'), '<dd>','</dd>'));
		elseif(stristr($data, '<dd>BUNDLE</dd>') && stristr($data, '<dt>Hạn dùng</dt>')) return array(true, "Until ".$this->lib->cut_str($this->lib->cut_str($data, '<dt>Hạn dùng</dt>','<dt>Fxu hiện có</dt>'), '<dd>','</dd>'));
		elseif(stristr($data, 'Loại tài khoản') && stristr($data, '<dd>VIP</dd>')) return array(true, "Account is lifetime!!!");
		elseif(stristr($data, 'Free Member')) return array(false, "accfree");
		else return array(false, "accinvalid");
	}
     
    public function Login($user, $pass){
		$data = $this->lib->curl("https://www.fshare.vn", "", "");
		preg_match('%input type="hidden" value="(.*)" name="fs_csrf"%U', $data, $csrf);
		$cookie = $this->lib->GetCookies($data);
		$user = urlencode($user);
		$csrf = $csrf[1];
		$data = $this->lib->curl("https://www.fshare.vn/login", $cookie, "fs_csrf={$csrf}&LoginForm[email]={$user}&LoginForm[password]={$pass}&LoginForm[rememberMe]=0&yt0=Đăng Nhập");
		$cookie = $this->lib->GetCookies($data);
		return $cookie;
    }
	
    public function Leech($url) {
		$url = preg_replace("@http?:\/\/(www\.)?fshare\.vn@", "https://www.fshare.vn", $url);
		list($url, $pass) = $this->linkpassword($url); 
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if(stristr($data,"Tài khoản của quý khách đang được sử dụng để tải xuống bởi một thiết bị khác.")) $this->error("Tài khoản của quý khách đang được sử dụng để tải xuống bởi một thiết bị khác.", true, false);
		elseif(stristr($data,"Thông tin tập tin tải xuống") && stristr($data,">Tải chậm (miễn phí)<"))  $this->error("accfree", true, false);
		elseif(stristr($data,"Tập tin quý khách yêu cầu không tồn tại"))  $this->error("dead", true, false, 2);
		//elseif(stristr($data,"Mật khẩu tập tin")) $this->error("reportpass", true, false);
		elseif(!$this->isredirect($data)) {
			preg_match('%input type="hidden" value="(.*)" name="fs_csrf"%U',$data,$fs_csrf);
			$csrf = trim($fs_csrf[1]);
			preg_match('/id="DownloadForm_linkcode" type="hidden" value="(.*?)" \/>/',$data,$linkcode);
			$linkcode = $linkcode[1];
			while (true) {
				if($pass) {
					$data = $this->lib->curl('https://www.fshare.vn/download/get', $this->lib->cookie, "fs_csrf={$csrf}&DownloadForm[pwd]={$pass}&ajax=download-form&DownloadForm[linkcode]=".$linkcode,0);
				}
				else {
					$data = $this->lib->curl('https://www.fshare.vn/download/get', $this->lib->cookie, "fs_csrf={$csrf}&ajax=download-form&DownloadForm[linkcode]=".$linkcode, 0);
				}
				$page = json_decode($data,true);
				if(isset($page['msg'])) {
					if (stristr($page['msg'],"vui lòng thử")) continue;
					$this->error($page['msg'], true, false, 2);
					$this->lib->save_cookie("fshare.vn","");
				}
				else return trim($page['url']);
			}
		}
		else return trim($this->redirect);
		
		return false;
    }
	
}

/*
* https://www.fshare.vn/file/URZDLOB8WN9Z
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Fshare.VN Download Plugin 
* Downloader Class By [FZ]
* Plugin By giaythuytinh176
* Date: 16.7.2013
* Fixed check account: 18.7.2013
* Support file password by giaythuytinh176 [26.7.2013]
* Fix plugin by rayyan2005 [1.3.2015]
*/
?>