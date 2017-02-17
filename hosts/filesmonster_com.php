<?php
 
class dl_filesmonster_com extends Download {
 
    public function CheckAcc($cookie){
        $data = $this->lib->curl("https://filesmonster.com/", "yab_ulanguage=en;".$cookie, "");
        if(stristr($data, 'Your membership type: <strong>Regular</strong>')) return array(false, "accfree");
        elseif(stristr($data, 'Your membership type: <strong>Premium</strong>') && !stristr($data, 'Premium expired:')) return array(true, "Premium Until: ".$this->lib->cut_str($data, '<p>Valid until: <span class=\'em-success\'>','</span></p>'));
        elseif(stristr($data, "Premium expired:")) return array(false, "Account Expired!");
        else return array(false, "accinvalid");
    }
 
    public function Login($user, $pass){
        $data = $this->lib->curl("https://filesmonster.com/login.php", "yab_ulanguage=en", "act=login&user={$user}&pass={$pass}");
        $cookie = "yab_ulanguage=en;".$this->lib->GetCookies($data);
        return $cookie;
    }
   
    public function Leech($url) {
        $data = $this->lib->curl($url, $this->lib->cookie, "");
        if(stristr($data,'<h3>File not found</h3>') || stristr($data,'<h1 class="block_header">The link could not be decoded</h1>'))   
        $this->error("dead", true, false, 2); 
        elseif(stristr($data,'You have reached your download limit')) $this->error("LimitAcc", true, false);
        elseif(preg_match('/href="(https?:\/\/filesmonster\.com\/get\/[^"\'><\r\n\t]+)"/', $data, $data1))  {
            $data2 = $this->lib->curl($data1[1], $this->lib->cookie, "");
            if(preg_match('/get_link\("([^"\'><\r\n\t]+)"\)/', $data2, $data3)) {
                $data4 = $this->lib->curl("http://filesmonster.com".$data3[1], $this->lib->cookie, "");
                if(preg_match('%url":"(https?:.+fmdepo.net.+)"%U', $data4, $giay))  {
                    $giay = str_replace('\\', '', $giay[1]);
                    $giay = str_replace("https", "http", $giay);
                    return trim($giay);
                }
            }
        }
        return false;
    }
     
}
/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* FilesMonster.com Download Plugin
* Downloader Class By [FZ]
* Download Plugin by giaythuytinh176 [23.07.2013]
* Fixed Plugin With New Domain By KulGuY [02.06.2015]
*/
?>