<?php
	
	/** url path or file */
	$method = "url";  					// "file" or "url"
	$source = "http://torstatus.blutmagie.de/ip_list_all.php/Tor_ip_list_ALL.csv"; 	// do not use $_GET, $_POST, $_SERVER WIL LEAD to RFI - major security risk
	
	if($method == "url") {
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $source); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101209 Firefox/3.6.13'); 
		$data = curl_exec($ch); 
		curl_close($ch); 
	} else {
		$data = file_get_contents($source);
	}

	/** data stuff */
	$time = time();
	$ips = explode("\n", $data);
	
	/** 
	 * creates a database of ipv4 proxies.  Requires MaxMind GeoIPCity.day.  Time pull table structure is defined as:
	 
		CREATE TABLE IF NOT EXISTS `proxylist` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `time` int(20) NOT NULL,
		  `ip` varchar(20) NOT NULL,
		  `hostname` varchar(100) NOT NULL,
		  `hostmask` varchar(100) NOT NULL,
		  `geoip_continent` varchar(2) NOT NULL,
		  `geoip_country` varchar(2) NOT NULL,
		  `geoip_state` varchar(20) NOT NULL,
		  `geoip_city` varchar(20) NOT NULL,
		  `geoip_postal_code` varchar(20) NOT NULL,
		  `geoip_latitude` varchar(20) NOT NULL,
		  `geoip_longitude` varchar(20) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
		
	 */
	header('Content-type: text/plain');
	foreach($ips as $ip) {
		if(trim($ip) != "") {
		
			$hostname = trim(gethostbyaddr($ip));
			$temp = explode(".", $hostname);
			if(!is_numeric(end($temp))) $hostmask = $temp[count($temp)-2].".".$temp[count($temp)-1];
			else $hostmask = "";
			
			$city_data = geoip_record_by_name($ip);
			
			
			/** data */
			echo "insert into proxylist (time, ip, hostname, hostmask, geoip_continent, geoip_country, geoip_state, geoip_city, geoip_postal_code, geoip_latitude, geoip_longitude) VALUES(";
			echo '"'.$time.'",';
			echo '"'.$ip.'",';
			echo '"'.$hostname.'",';
			echo '"'.$hostmask.'",';
			echo '"'.geoip_continent_code_by_name($ip).'",';
			echo '"'.geoip_country_code_by_name($ip).'",';
			echo '"'.$city_data['region'].'",';
			echo '"'.$city_data['city'].'",';
			echo '"'.$city_data['postal_code'].'",';
			echo '"'.$city_data['latitude'].'",';
			echo '"'.$city_data['longitude'].'"'.");\n";
		}
	}
?>




