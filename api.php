<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Media 
{
    private $tvdb_apikey;
    private $tvdb_username;
    private $tvdb_userkey;
    private $sonarr_key;
    private $sonarr_url;
    private $token;

    private $tvdb_url = "https://api.thetvdb.com";
    private $headers = array();

    public function __construct($apikey, $username, $userkey, $sonarrkey, $sonarrurl)
    {
        $this->tvdb_apikey = $apikey;
        $this->tvdb_username = $username;
        $this->tvdb_userkey = $userkey;
        $this->sonarr_key = $sonarrkey;
        $this->sonarr_url = $sonarrurl;
    }

    function getToken()
    {
        $this->headers[] = "Accept: application/vnd.thetvdb.v2.2.0"; //written & tested against 2.2.0 of the api
        $this->headers[] = "Content-Type: application/json";

        $login = json_encode(array(
            "apikey" => $this->tvdb_apikey,
            "username" => $this->tvdb_username,
            "userkey" => $this->tvdb_userkey
        ));

        $ch = curl_init($this->tvdb_url."/login");

        curl_setopt($ch, CURLOPT_POSTFIELDS, $login);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        
        $token = json_decode($response);
        $this->token = $token->token;
        curl_close($ch);
    }

    function searchTV($name)
    {
        if (empty($this->token))
        {
            $this->getToken();
        }
        
        $ch = curl_init($this->tvdb_url."/search/series?name=".$name);
        $this->headers[] = "Authorization: Bearer ".$this->token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        return $response;
    }

}

?>