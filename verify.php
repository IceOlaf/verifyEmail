<?php
print_r(verifyEmail('dsfdsfdasdsadasdasds@gmail.com', 'root@w3ing.tk', true));

function verifyEmail($toemail, $fromemail, $getdetails = false){
	$email_arr = explode("@", $toemail);
	$domain = array_slice($email_arr, -1);
	$domain = $domain[0];

	// Trim [ and ] from beginning and end of domain string, respectively
	$domain = ltrim($domain, "[");
	$domain = rtrim($domain, "]");

	$mxhosts = array();
	if( filter_var($domain, FILTER_VALIDATE_IP) )
		$mx_ip = $domain;
	else
		getmxrr($domain, $mxhosts, $mxweight);

	if(!empty($mxhosts) )
		$mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
	else {
		$record_a = dns_get_record($domain, DNS_A);
		if( !empty($record_a) )
			$mx_ip = $record_a[0]['ip'];
		else {

			$result   = "invalid";
			$details .= "No suitable MX records found."

			return ($getdetails) ? array($result, $details) : $result;
		}
	}
	
	$connect = @fsockopen($mx_ip, 25); 
	if($connect){ 
		if(ereg("^220", $out = fgets($connect, 1024))){ 
			fputs ($connect , "HELO $HTTP_HOST\r\n"); 
			$out = fgets ($connect, 1024);
			$details .= $out."\n";
 
			fputs ($connect , "MAIL FROM: <$fromemail>\r\n"); 
			$from = fgets ($connect, 1024); 
			$details .= $from."\n";

			fputs ($connect , "RCPT TO: <$toemail>\r\n"); 
			$to = fgets ($connect, 1024);
			$details .= $to."\n";

			fputs ($connect , "QUIT"); 
			fclose($connect);

			if(!ereg("^250", $from) || !ereg("^250", $to)){
				$result = "invalid"; 
			}
			else{
				$result = "valid";
			}
		} 
	}
	else{
		$result = "invalid";
		$details .= "Could not connect to server";
	}
	if($getdetails){
		return array($result, $details);
	}
	else{
		return $result;
	}
}
?>
