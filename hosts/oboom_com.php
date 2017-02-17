<?php
 
class dl_oboom_com extends Download {
               
        private function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
        {       //https://github.com/defuse/password-hashing/blob/master/PasswordHash.php
                $algorithm = strtolower($algorithm);
                if(!in_array($algorithm, hash_algos(), true))
                        trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
                if($count <= 0 || $key_length <= 0)
                        trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
 
                if (function_exists("hash_pbkdf2")) {
                        // The output length is in NIBBLES (4-bits) if $raw_output is false!
                        if (!$raw_output) {
                                $key_length = $key_length * 2;
                        }
                        return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
                }
 
                $hash_length = strlen(hash($algorithm, "", true));
                $block_count = ceil($key_length / $hash_length);
 
                $output = "";
                for($i = 1; $i <= $block_count; $i++) {
                        // $i encoded as 4 bytes, big endian.
                        $last = $salt . pack("N", $i);
                        // first iteration
                        $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
                        // perform the other $count - 1 iterations
                        for ($j = 1; $j < $count; $j++) {
                                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
                        }
                        $output .= $xorsum;
                }
 
                if($raw_output)
                        return substr($output, 0, $key_length);
                else
                        return bin2hex(substr($output, 0, $key_length));
        }
       
    public function CheckAcc($cookie) {
       
                $data = $this->lib->curl('http://www.oboom.com/', 'lang=EN; ' .str_replace(':', '%3A', $cookie), '');
        if (preg_match('@premium_unix"\:([^,]+)@i', $data, $redir)) {
                        if ($redir[1] == 'null') return array(false, "accfree");
                        elseif (preg_match('@traffic"\:{"current"\:([^,]+),"increase"\:[^,]+,"last"\:[^,]+,"max"\:([^}]+)@i', $data, $redir2)) {
                                if ($redir2[1] == 0 && $redir2[2] == 0) return array(false, "accfree");
                                else return array(true, 'Until ' .date('H:i:s Y-m-d', $redir[1]). '<br/> Traffic available: ' .round($redir2[1]/1073741824, 2). ' GB<br/> Max: ' .round($redir2[2]/1073741824, 2). ' GB');
                        }
                }
                else return array(false, "accinvalid");
    }
       
    public function Login($user, $pass) {
               
                $mysalt = strrev($pass);
               
                $hash = $this->pbkdf2('sha1', $pass, $mysalt, 1000, 16);
               
                $post = array(
                        'auth' => $user,
                        'pass' => $hash,
                        'source' => '/#app',
                );
               
                $page = $this->lib->curl('http://www.oboom.com/1.0/login', 'lang=EN', $post, 0);
               
                $json = @json_decode($page, true);
               
                return 'user=' .urlencode($json[1]['cookie']). '; lang=EN; ';
    }
       
    public function Leech($link) {
               
                if (strpos($link, '#')) $link = str_replace('#', '', $link);
 
                if (!preg_match('@https?://(www.)?oboom\.com/([\w]{8})@i', $link, $id)) $this->error('Link invalid?.', true, false);
               
                $link = "http://www.oboom.com/".$id[2];
     
                $page = $this->lib->curl($link, $this->lib->cookie, '');

                if (strpos($page, '400 Bad Request')) $this->error('Link invalid?.', true, false);
               
                if (preg_match('@ocation: (https?://(www\.)?oboom\.com/[^\r\n]+)@i', $page, $redir)) {
                        $page = $this->lib->curl(trim($redir[1]), $this->lib->cookie, '');
                }
               
                if (!preg_match('@https?:\/\/[\w]+\.oboom\.com\/(1\.0|1)\/dlh\?ticket=[^\r\n]+@i', $page, $dlink)) {
                       
                        if (!preg_match('@Redirecting to (https?:\/\/api\.oboom\.com\/(1|1\.0)\/dl\?redirect=true&token=[^\r\n]+)@i', $page, $lik)) {                             

                                if (!preg_match('@Session : "([^"]+)"@i', $page, $token)) $this->error('Token not found.', true, false);
                               
                                $page = $this->lib->curl('http://api.oboom.com/1.0/dl', $this->lib->cookie, array('token' => $token[1], 'item' => $id[2],), 0);
                               
                                $json = @json_decode($page, true);
                               
                                if (isset($json[0]) && $json[0] == 200) {
                                        $link = trim('http://'.$json[1].'/1.0/dlh?ticket='.$json[2]);
                                        if (!preg_match('@https?://[\w]+\.oboom\.com/1\.0/dlh\?ticket=[^\r\n]+@i', $link, $dlink)) $this->error('Download link not found.', true, false);
                                }
                               
                                elseif (isset($json[0]) && $json[0] != 200) $this->CheckErr($json[0]);
                        }
                }
               
                return trim(isset($dlink[0]) ? $dlink[0] : $lik[1]);
               
                return false;
    }
       
        private function CheckErr($code) {      //Th3-822
                if (is_numeric($code)) {
                        switch ($code) {
                                default: $msg = '*No message for this error*';break;
                                case 400: $msg = 'Bad request. You offered an invalid input parameter. See the attached error message for infos what parameter was invalid/missing and more informations.';break;
                                case 403: $msg = 'Access denied. This includes insufficient user permission to access a resource, the maximal upload filesize or IP conflicts with a token.';break;
                                case 404: $msg = 'Resource not found. The first parameter tells you what resource was not not found, the second (which is optional) why.';break;
                                case 409: $msg = 'Conflict. The requested resource has a conflict with another resource. This usually happens on file system operations like copy or move.';break;
                                case 410: $msg = 'Gone. The resource you requested is no longer available and will not come back The parameters are are the same as with 404.';break;
                                case 413: $msg = 'Request entity too large. The request is handling too much resources at once. This can happen during a cp call.';break;
                                case 421: $msg = 'Connection limit exceeded. You are downloading with too many connections at once or your IP is blocked for (the second parameter tells you how long). This error usually comes when you downloaded too much as a guest or free user.';break;
                                case 500: $msg = 'Internal server error. This should not happen but it may. See the attached error message for more informations.';break;
                                case 503: $msg = 'The service is temporary not available. You may retry later. See the parameters for infos about what is not available.';break;
                                case 507: $msg = 'At least one quota like storage space or item count reached.';break;
                                case 509: $msg = 'Bandwidth limit exceeded. You have to get more traffic to access this resource.';break;
                        }
                        $this->error("[Error: $code] $msg.", true, false);
                }
        }
 
}
 
/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Oboom.com Download Plugin by giaythuytinh176 [19-04-2014]
* Downloader Class By [FZ]
*/
?>