<?php	
	$url = 'http://torstatus.blutmagie.de/ip_list_all.php/Tor_ip_list_ALL.csv';
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101209 Firefox/3.6.13'); 
	$data = curl_exec($ch); 
	curl_close($ch); 
	$ips = explode("\n", $data);
	echo "sudo iptables -F\n";
	echo "sudo iptables -t nat -F\n";
	echo "sudo iptables -X\n";
	foreach($ips as $ip) {
		if(trim($ip) != "") {
			echo "sudo iptables -A INPUT -s ".$ip." -j DROP\n";
		}
	}
?>
