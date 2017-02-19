<?php
$host = array(); $alias = array(); 
$alias['4share.vn'] = 'up.4share.vn';
$alias['fs.vn'] = 'fshare.vn';
$alias['share.vnn'] = 'share.vnn.vn';
$alias['dfpan.com'] = 'yunfile.com';
$alias['dix3.com'] = 'yunfile.com';
$folderhost = opendir ( "hosts/" );
while ( $hostname = readdir ( $folderhost ) ) {		
	if($hostname == "." || $hostname == ".." || strpos($hostname,"bak") || $hostname == "hosts.php") {continue;}
	if(stripos($hostname,"php")){
		$site = str_replace("_", ".", substr($hostname, 0, -4));
		if(isset($alias[$site])){
			$host[$site] = array(
				'alias' => true,
				'site' => $alias[$site],
				'file' => str_replace(".", "_", $alias[$site]).".php",
				'class' => "dl_".str_replace(array(".","-"), "_", $alias[$site])
			);
		}
		else{
			$host[$site] = array(
				'alias' => false,
				'site' => $site,
				'file' => $hostname,
				'class' => "dl_".str_replace(array(".","-"), "_", $site)
			);
		}
	}
}
closedir ( $folderhost );
?>
