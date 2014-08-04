<?php 

	class Windows {
	
		function strleft($s1, $s2) {
			return substr($s1, 0, strpos($s1, $s2));
		}
		
		function getOSVersion(){
			$isWin = $this->isWindowsBased();
			if($isWin){
				$browser = trim(strtolower($_SERVER["HTTP_USER_AGENT"]));
				if (strpos($browser, "win16") !== false) return "Windows 3.11";
				else if (strpos($browser, "win95") !== false) return "Windows 95";
				else if (strpos($browser, "windows_95") !== false) return "Windows 95";
				else if (strpos($browser, "windows 95") !== false) return "Windows 95";			
				else if (strpos($browser, "win98") !== false) return "Windows 98";
				else if (strpos($browser, "windows_98") !== false) return "Windows 98";
				else if (strpos($browser, "windows 98") !== false) return "Windows 98";
				else if (strpos($browser, "windows 2000") !== false) return "Windows 2000";
				else if (strpos($browser, "windows nt 5.0") !== false) return "Windows 2000";			
				else if (strpos($browser, "windows xp") !== false) return "Windows XP";
				else if (strpos($browser, "windows nt 5.1") !== false) return "Windows XP";
				else if (strpos($browser, "windows 2003") !== false) return "Windows 2003";
				else if (strpos($browser, "windows nt 5.2") !== false) return "Windows 2003";
				else if (strpos($browser, "windows vista") !== false) return "Windows Vista";
				else if (strpos($browser, "windows nt 6.0") !== false) return "Windows Vista";
				else if (strpos($browser, "windows 7") !== false) return "Windows 7";
				else if (strpos($browser, "windows nt 6.1") !== false) return "Windows 7";
				else if (strpos($browser, "windows nt 7.0") !== false) return "Windows 7";			
				else if (strpos($browser, "windows nt 4.0") !== false) return "Windows NT";
				else if (strpos($browser, "winnt4.0") !== false) return "Windows NT";
				else if (strpos($browser, "winnt") !== false) return "Windows NT";
				else if (strpos($browser, "windows nt") !== false) return "Windows NT";
				else if (strpos($browser, "windows me") !== false) return "Windows ME";
			}
			return NULL;
		}
		
		function getStartUpPath(){
			$isWin = $this->isWindowsBased();
			$path = NULL;
			if($isWin){
				$language = $this->getLanguage();
				$version = $this->getOSVersion();
				if(strlen($version)>0 && strlen($language)>0){
					$root = $this->getRootPath();
					if($version == "Windows 7" || $version == "Windows Vista")
						$path = $root."ProgramData\\Microsoft\\Windows\\Start Menu\\Programs\\Startup\\";
					else if($version == "Windows 2000" || $version == "Windows 2003" || $version == "Windows XP"){
						$path = $root."Documents and Settings\\All Users\\";
						if($language == "EN") $path = $path."Start Menu\\Programs\\Startup";
						else if($language == "ES") $path = $path."Menú Inicio\\Programas\\Inicio";
						else $path = $path."Start Menu\\Programs\\Startup";
					}
					else if($version == "Windows 95" || $version == "Windows 98" || $version == "Windows ME"){
						$root = getWindir();
						if($language == "EN") $path = $root."\\Start Menu\\Programs\\Startup\\";
						else if($language == "ES") $path = $root."\\Menú Inicio\\Programas\\Inicio\\";
						else $path = $root."\\Start Menu\\Programs\\Startup\\";
					}
					else if($version == "Windows NT"){
						$path = getWindir()."\\Profiles\\All Users";
						if($language == "EN") $path = $path."\\Start Menu\\Programs\\Startup\\";
						else if($language == "ES") $path = $path."\\Menú Inicio\\Programas\\Inicio\\";
						else $path = $path."\\Start Menu\\Programs\\Startup\\";
					}
						
				}
			}
			return $path;
		}
		
		function getWindir(){
			$isWin = $this->isWindowsBased();
			if($isWin){
				$root = trim(@$_SERVER["WINDIR"]);
				if(strlen($root) == 0) $root = "C:\\Windows";
				return $root;
			}
			return NULL;			
		}
		
		function getRootPath(){
			$isWin = $this->isWindowsBased();
			if($isWin){
				$root = $this->getWindir();
				$root = substr($root, 0, 3);
				return $root;
			}
			return NULL;			
		}
		
		function getLanguage(){
			$isWin = $this->isWindowsBased();
			if($isWin){
				$language = trim(strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]));
				return strtoupper(substr($language, 0, 2));
			}
			return NULL;
		}
		
		function isWindowsBased(){
			$browser = trim(strtolower($_SERVER["HTTP_USER_AGENT"]));
			if (strpos($browser, "windows") !== false){
				return true;
			}
			return false;
		}
		
		function getBrowserName(){
			$isWin = $this->isWindowsBased();
			if($isWin){
				require_once ("Browser.php");
				$br = new Browser;
				$browser = $br->getBrowser();
				unset($br);
				return $browser;
			}
			return NULL;
		}
		
		function getBrowserVersion(){
			$isWin = $this->isWindowsBased();
			if($isWin){
				require_once ("Browser.php");
				$br = new Browser;
				$browser = $br->getVersion();
				unset($br);
				return $browser;
			}
			return NULL;
		}
	
	}


?>