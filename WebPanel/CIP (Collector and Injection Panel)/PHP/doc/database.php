<?php 

    function getUploadLimit(){
        return (1024 * 500) * 1;//500K
    }

    function getSID(){ 
        return "user";
    }

    function getPWR(){ 
        return "pass";
    }

    function getHOST(){ 
        return "localhost";
    }

    function getDB(){ 
        return "collector";
    }

    //http://localhost/cip/cip/doc/home.php

?>