<?php 

	class GeoIP{
	
		function getIP(){
			return $_SERVER['REMOTE_ADDR'];
		}
	
		function toUpperText($str){
			$table = array(
			"À"=>"à", "Á"=>"á", "Â"=>"â", "Ã"=>"ã",
			"Ä"=>"ä", "Å"=>"å", "Æ"=>"æ", "Ç"=>"ç",
			"Ð"=>"ð", "È"=>"è", "É"=>"é", "Ê"=>"ê", 
			"Ë"=>"ë", "Ì"=>"ì", "Í"=>"í", "Î"=>"î", 
			"Ï"=>"ï", "Ñ"=>"ñ", "Ò"=>"ò", "Ó"=>"ó",
			"Ô"=>"ô", "Õ"=>"õ", "Ö"=>"ö", "Ø"=>"ø", 
			"Œ"=>"œ", "ß"=>"ß", "Þ"=>"þ", "Ù"=>"ù",
			"Ú"=>"ú", "Û"=>"û", "Ü"=>"ü", "Ý"=>"ý", 
			"Ÿ"=>"ÿ");
			$values = array_values($table);
			$keys = array_keys($table);
			
			for($i=0; $i<count($values); $i++){
				$str = str_replace($values[$i], $keys[$i], $str);
			}
			
			return strtoupper($str);
		}
	
		function toPlainText($str){
			$table = array(
			"À"=>"&Agrave;", "à"=>"&agrave;", "Á"=>"&Aacute;", 
			"á"=>"&aacute;", "Â"=>"&Acirc;", "â"=>"&acirc;", 
			"Ã"=>"&Atilde;", "ã"=>"&atilde;", "Ä"=>"&Auml;", 
			"ä"=>"&auml;", "Å"=>"&Aring;", "å"=>"&aring;", 
			"Æ"=>"&AElig;", "æ"=>"&aelig;", "Ç"=>"&Ccedil;", 
			"ç"=>"&ccedil;", "Ð"=>"&ETH;", "ð"=>"&eth;", 
			"È"=>"&Egrave;", "è"=>"&egrave;", "É"=>"&Eacute;", 
			"é"=>"&eacute;", "Ê"=>"&Ecirc;", "ê"=>"&ecirc;", 
			"Ë"=>"&Euml;", "ë"=>"&euml;", "Ì"=>"&Igrave;", 
			"ì"=>"&igrave;", "Í"=>"&Iacute;", "í"=>"&iacute;", 
			"Î"=>"&Icirc;", "î"=>"&icirc;", "Ï"=>"&Iuml;", 
			"ï"=>"&iuml;", "Ñ"=>"&Ntilde;", "ñ"=>"&ntilde;", 
			"Ò"=>"&Ograve;", "ò"=>"&ograve;", "Ó"=>"&Oacute;", 
			"ó"=>"&oacute;", "Ô"=>"&Ocirc;", "ô"=>"&ocirc;", 
			"Õ"=>"&Otilde;", "õ"=>"&otilde;", "Ö"=>"&Ouml;", 
			"ö"=>"&ouml;", "Ø"=>"&Oslash;", "ø"=>"&oslash;", 
			"Œ"=>"&OElig;", "œ"=>"&oelig;", "ß"=>"&szlig;", 
			"Þ"=>"&THORN;", "þ"=>"&thorn;", "Ù"=>"&Ugrave;", 
			"ù"=>"&ugrave;", "Ú"=>"&Uacute;", "ú"=>"&uacute;", 
			"Û"=>"&Ucirc;", "û"=>"&ucirc;", "Ü"=>"&Uuml;", 
			"ü"=>"&uuml;", "Ý"=>"&Yacute;", "ý"=>"&yacute;", 
			"Ÿ"=>"&Yuml;", "ÿ"=>"&yuml;");
			$values = array_values($table);
			$keys = array_keys($table);
			
			$str = str_replace("&amp;", "&", $str);
			
			for($i=0; $i<count($values); $i++){
				$str = str_replace($values[$i], $keys[$i], $str);
			}
			return $str;
		}
	
		function getTag($source, $tag, $decode){
			$rsp = "";
			$size = strlen("<".$tag.">");
			$init = strrpos($source,"<".$tag.">");
			$end = strrpos($source,"</".$tag.">");
			if(!is_bool($init) && !is_bool($end)){
				$add = $end - ($init + $size);
				$rsp = trim(@substr($source, $init + $size, $add));
				if($decode) {
					$rsp = $this->toPlainText($rsp);//strtoupper
					$rsp = $this->toUpperText($rsp);
				}
			}
			return $rsp;
		}
	
		function getLocation($ip){
			
			$array = array();
			
			$xml = false;
			
			if(!$xml){
				$url = "http://www.geoplugin.net/php.gp?ip=" . $ip;
                                $contents = @file_get_contents($url);
				$contents = @unserialize($contents); //var_export(
				
				$array["city"] = utf8_decode($contents["geoplugin_city"]); //$this->toPlainText
				$array["region"] = utf8_decode($contents["geoplugin_regionName"]);//geoplugin_region
				$array["country"] = utf8_decode($contents["geoplugin_countryName"]);
				$array["latitude"] = $contents["geoplugin_latitude"];
				$array["longitude"] = $contents["geoplugin_longitude"];
				
				//print_r ($array);
				
				return $array;
			}
			else{
				$url = "http://www.geoplugin.net/xml.gp?ip=" . $ip;			
				$s = @file_get_contents($url);			
				if(!strpos($s, "<geoPlugin>")) return $array;			
				$s = str_replace(base64_decode(base64_encode("<?xml version=\"1.0\" encoding=\"UTF-8\"?>")), '', $s);
				
				$s = str_replace("<geoPlugin>", '', $s);
				$s = str_replace("</geoPlugin>", '', $s);
				$s = str_replace("\r\n\r\n", '', $s);
				$s = str_replace("\t", '', $s);
				$s = trim($s);
				
				$decode = true;
				
				$array["city"] = $this->getTag($s, "geoplugin_city", $decode);
				$array["region"] = $this->getTag($s, "geoplugin_regionName", $decode);
				$array["country"] = $this->getTag($s, "geoplugin_countryName", $decode);
				$array["latitude"] = $this->getTag($s, "geoplugin_latitude", $decode);
				$array["longitude"] = $this->getTag($s, "geoplugin_longitude", $decode);
				
				//print_r ($array);
				//var myObject = eval('(' + myJSONtext + ')');
				
				return $array;
			}
			
			return null;
		}
	
	}

?>