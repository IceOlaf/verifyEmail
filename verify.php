<?php
print_r(verifyEmail('dsfdsfdasdsadasdasds@gmail.com', 'root@w3ing.tk', true));

function verifyEmail($toemail, $fromemail, $getdetails = false){
	$email_arr = explode("@", $toemail);
	$domain = $email_arr[1];
	getmxrr($domain, $mxhosts, $mxweight);
	$mx = $mxhosts[array_search(min($mxweight), $mxhosts)];
	
	$connect = @fsockopen($mx, 25); 
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
