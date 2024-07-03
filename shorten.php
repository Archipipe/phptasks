<?php
include 'db.php';
include 'Shortener.php';


$db = new Database();
$conn = $db->connect();

$longUrl = $_POST['longUrl'];

$Shortener = new Shortener($conn);
try{
    $shortUrl = $Shortener->urlToShortCode($longUrl);
    http_response_code(200);
    echo $shortUrl;
} catch (Exception $e){
    http_response_code(400);
    echo $e->getMessage();
}
?>