<?php

class Database {
    public $mysqli;
    public  $clientID;
    public  $clientSecret;
    public  $redirectUri;

    public function __construct(){
        include("./database_credentials.php"); // define variables

        /** SETUP **/
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //Extra error printing
        $this->clientID =$clientID;
        $this->clientSecret =$clientSecret;
        $this->redirectUri =$redirectUri;
        $this->mysqli = new mysqli($dbhost, $dbusername, $dbpasswd, $dbname);
    }
    public function query($query,$bparam=null, ...$param){ //$bparam is the param type,e.g, ssi
        $stmt=$this->mysqli->prepare($query);
        if($bparam!=null){
            $stmt->bind_param($bparam, ...$params);
        }
        if($stmt->execute()){
            //execute failed
            return false;
        }
        //execute succeeded 
        if(($res=$stmt->get_result())!==false){
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return true; 
    }
}