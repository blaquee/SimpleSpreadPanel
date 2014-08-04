
public class Rot13 { 

    public static String code(String source) {
        try{
            String out = "";
            for (int i = 0; i < source.length(); i++) {
                char c = source.charAt(i);
                if       (c >= 'a' && c <= 'm') c += 13;
                else if  (c >= 'n' && c <= 'z') c -= 13;
                else if  (c >= 'A' && c <= 'M') c += 13;
                else if  (c >= 'A' && c <= 'Z') c -= 13;                
                out += c;
            }
            if(out.length()>0) return out;
        }catch(Exception e){}
        return null;
    }

}
