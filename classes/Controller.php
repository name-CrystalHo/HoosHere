<?php

class Controller {

    private $db;
    
    private $url = "/HoosHere";
    
    //will have instance of database object
    public function __construct() {
        $this->db = new Database();
    }
    //handles logic of methods
    public function run($command) {
        switch($command) {
            case "logout":
                $this->destroySession();           
            case "login":
            default:
                $this->login();
                break;
        }
            
    }
    //destroy and restart
    public function destroySession() {          
        session_destroy();
        session_start();

    }
    public function login() {
        // our login code from index.php last time!
        $error_msg = ""; 
        require 'google-api/vendor/autoload.php';
       
        // Creating new google client instance
        $client = new Google_Client();
        // Enter your Client ID
        $client->setClientId($this->db->clientID);
        // Enter your Client Secrect
        $client->setClientSecret($this->db->clientSecret);
        // Enter the Redirect URL
        $client->setRedirectUri($this->db->redirectUri);
       
        // Adding those scopes which we want to get (email & profile Information)
        $client->addScope("email");
        $client->addScope("profile");
       if(isset($_GET['code'])){
        
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if(!isset($token["error"])){
          
            $db_connection=$this->db->mysqli;
            $client->setAccessToken($token['access_token']);

            // getting profile information
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            
            // Storing data into database
            $full_name = mysqli_real_escape_string($db_connection, trim($google_account_info->name));
            $email = mysqli_real_escape_string($db_connection, $google_account_info->email);
            // $profile_pic = mysqli_real_escape_string($db_connection, $google_account_info->picture);
            //check if UVA Student
            $stmt=$this->db->mysqli->prepare( "SELECT * FROM `banned` where email=?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $res = $stmt->get_result();        
            $data = $res->fetch_all();
            $regex="<[a-z][a-z][a-z]?[0-9][a-z][a-z]?[a-z]?@virginia.edu>";
            if(preg_match($regex,$email)){
                            // checking user already exists or not
                $get_user = $this->db->mysqli->prepare( "SELECT `email` FROM `user` WHERE `email`=?");
                $get_user->bind_param("s",$email);
                $get_user->execute();
                $res = $get_user->get_result();        
                $data = $res->fetch_all(MYSQLI_ASSOC);
                $_SESSION['email'] = $email; 
                header('Location: /HoosHere/home');
                return;
                }
                else{

                    // if user not exists we will insert the user
                    $insert =$this->db->mysqli->prepare ("INSERT INTO  `user`(`email`,`name`) VALUES(?,?)");
                    $insert->bind_param("ss",$email,$full_name);
                }
            }
            else{
                $_SESSION['error']="<div class='alert alert-danger'style = 'margin:0;'><b>Error: $error_msg </b></div>";
                header('Location: /HoosHere/login');
            }

        }
        include("templates/login.php");
    }
        
}