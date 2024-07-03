<?php
class Shortener
{
    protected static $chars = "abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789";
    protected static $table = "urls";
    protected static $codeLength = 7;

    protected $conn;


    public function __construct($conn){
        $this->conn = $conn;
    }

    public function urlToShortCode($url){
        if(empty($url)){
            throw new Exception("Нет URL.");
        }

        if($this->validateUrl($url) == false){
            throw new Exception("URL не валиден.");
        }


        $shortUrl = $this->urlExistsInDB($url);
        if($shortUrl== false){
            $shortUrl = $this->createShortUrl($url);
            $this->insertUrlInDB($url, $shortUrl);

        }

        return $shortUrl;
    }

    protected function validateUrl($url){
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    protected function getSelfDomain() {
        $protocol = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $protocol = 'https';
        }
        return $protocol . '://' . $_SERVER['HTTP_HOST'];
    }



    protected function urlExistsInDB($url){
        $query = "SELECT short_url FROM ".self::$table." WHERE long_url = :long_url LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $params = array(
            "long_url" => $url
        );
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result["short_url"];
    }


    protected function createShortUrl(){
        $shortCode = $this->generateRandomString(self::$codeLength);
        return $this->getShortUrl($shortCode);
    }

    protected function getShortUrl($shortCode){
        $selfUrl = $this->getSelfDomain();
        $shortUrl = $selfUrl.'/?code='.$shortCode;

        return $shortUrl;
    }

    protected function generateRandomString($length){
        $sets = explode('|', self::$chars);
        $all = '';
        $randString = '';
        foreach($sets as $set){
            $randString .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++){
            $randString .= $all[array_rand($all)];
        }
        $randString = str_shuffle($randString);
        return $randString;
    }

    protected function insertUrlInDB($long_url, $short_url){
        $query = "INSERT INTO ".self::$table." (long_url, short_url) VALUES (:long_url, :short_url)";
        $stmnt = $this->conn->prepare($query);
        $params = array(
            "long_url" => $long_url,
            "short_url" => $short_url
        );
        $stmnt->execute($params);

        return $this->conn->lastInsertId();
    }

    public function shortCodeToUrl($code){
        if(empty($code)) {
            throw new Exception("Нет кода");
        }

        if($this->validateShortCode($code) == false){
            throw new Exception("Код невалидный");
        }

        $shortUrl = $this->getShortUrl($code);
        $urlRow = $this->getUrlFromDB($shortUrl);
        if(empty($urlRow)){
            throw new Exception("Кода не существует");
        }

        return $urlRow["long_url"];
    }

    protected function validateShortCode($code){
        $rawChars = str_replace('|', '', self::$chars);
        return preg_match("|[".$rawChars."]+|", $code);
    }

    protected function getUrlFromDB($short_url){
        $query = "SELECT long_url FROM ".self::$table." WHERE short_url = :short_url LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $params=array(
            "short_url" => $short_url
        );
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result;
    }

}