<?php 

	function getCurrentHost(){
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port;   
	}

	function strleft($s1, $s2) {
		return substr($s1, 0, strpos($s1, $s2));
	}
	
	header("Location: ".getCurrentHost()); 
	exit;
	
?>