<?php

class Database {
    private $host = "localhost";
    private $dbname = "short_url";
    private $tablename = "urls";
    private $user = "root";
    private $pass = "";

    public function initializeTable(){
        $query='CREATE TABLE IF NOT EXISTS `'.$this->tablename.'` ( `id` int(11) NOT NULL AUTO_INCREMENT,long_url varchar(255) NOT NULL,short_url varchar(255) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;';
        $db = self::connect();

        $stmt = $db->prepare($query);
        $result = $stmt->execute();
        return $result;
    }
    public function connect() {

        $conn_str = "mysql:host=". $this->host .";dbname=". $this->dbname;

        try {
            $conn = new PDO($conn_str, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}