<?php
 
class dl_firedrop_com extends Download {
 
    public function CheckAcc($cookie){
        $data = $this->lib->curl("https://firedrop.com/account_edit.html", $cookie, "");
        if(stristr($data, '<span class="label label-success">PROFESSIONAL</span>')) 
            return array(true, "Time Left: ".$this->lib->cut_str($data, 'Next Payment Due','</td></tr>'));
    //    elseif(stristr($data, 'Inactive')) return array(false, "accfree");
        else return array(false, "accinvalid");
    }
     
    public function Login($user, $pass){
        $data = $this->lib->curl("https://firedrop.com/ajax/_account_login.ajax.php", "", "username={$user}&password={$pass}");
        $cookie = $this->lib->GetCookies($data);/// username=minhlaobao&password=*eQgRrk*%23yk9
        return $cookie;
    }
     
    public function Leech($url) {
        $data = $this->lib->curl($url,$this->lib->cookie,"");
        if(preg_match('/ocation: (.*)/', $data, $matches)) return trim($matches[1]);//$matches[1],$this->lib->cookie,"");
        elseif((stristr($data, "File doesn't exist")))  $this->error("dead", true, false, 2);
       // elseif(preg_match('@https?:\/\/\w+\.nitroflare\.com\/d\/[^"\'><\r\n\t]+@i', $data, $invo))
        return trim($invo[0]);
        return false;
    }
 
}
 
/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Firedrop.com Download Plugin 
* Downloader Class By [FZ]
* Created: Hitpro [07.11.15]
*/
?>