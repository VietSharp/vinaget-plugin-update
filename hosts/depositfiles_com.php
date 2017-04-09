<?php

class dl_depositfiles_com extends Download
{
    
    public function CheckAcc($cookie)
    {
        $data = $this->lib->curl("https://depositfiles.com/gold/payment_history.php", $cookie, "");
        if (stristr($data, 'You have Gold access until:')) {
            return array(
                true,
                "(fix by hitpro)<br/>Until " . $this->lib->cut_str($data, '<div class="access">You have Gold access until: <b>', '</b></div>')
            );
        } elseif (stristr($data, 'Your current status: FREE - member'))
            return array(
                false,
                "accfree"
            );
        else
            return array(
                false,
                "accinvalid"
            );
    }
    
    public function Login($user, $pass)
    {
        $data   = $this->lib->curl("https://depositfiles.com/api/user/login?login=" . urlencode($user) . "&password=" . urlencode($pass), '', '');
        $cookie = "lang_current=en;" . $this->lib->GetCookies($data);
        return $cookie;
    }
    
    public function Leech($url)
    {
        list($url, $test) = $this->linkpassword($url);
        $tachid = explode("/", $url);
        if (preg_match("/\/files\/(.*)\/(.+)/i", $url, $id))
            $DFid = $id[1];
        elseif (count($tachid) == 5)
            $DFid = $tachid[4];
        else
            $DFid = $tachid[5];
        $data = $this->lib->curl("https://depositfiles.com/api/download/file?&file_id=" . $DFid . "&file_password=" . urlencode($test), $this->lib->cookie, "", 0);
        $page = @json_decode($data, true);
        if ($page['status'] !== "OK" && $page['error'] == "FileIsPasswordProtected")
            $this->error("reportpass", true, false);
        elseif ($page['status'] !== "OK" && $page['error'] == "FileDoesNotExist")
            $this->error("dead", true, false, 2);
        elseif ($page['status'] !== "OK" && $page['error'] == "FilePasswordIsIncorrect")
            $this->error("wrongpass", true, false, 2);
        elseif ($page['status'] == "OK" && isset($page['data']['download_url']))
            return trim(str_replace("depositfiles.com", "dfiles.eu", $page['data']['download_url']));
        else
            $this->error($page['error'], true, false);
        return false;
    }
    
}


/*http://depositfiles.com/files/srfps4yz2
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Depositfiles.com Download Plugin  
* Date: 16.7.2013
* Fix download by giaythuytinh176 [21.7.2013]
* Fixed check account by giaythuytinh176 [24.7.2013]
* Add support file password by giaythuytinh176 [29.7.2013]
* Fix domain conflict, miss CheckAcc by Fz [24.11.2013]
* Fixed link by giay [29.11.2013]
* Edited & tested to work with user:pass by Steam [14, Feb 2014]
* Fix check/add account by HitPro - VNZLEECH.VN
*/
?>