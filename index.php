<?php
include 'db.php';
include 'Shortener.php';

$db = new Database();
$conn = $db->connect();
$Shortener = new Shortener($conn);

$db->initializeTable();

if (isset($_GET['code'])) {
    try{
        $shortCode = $_GET["code"];
        $url = $Shortener->shortCodeToUrl($shortCode);
        header("Location: ".$url);
        exit;
    } catch (Exception $e){
        echo $e->getMessage();
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP URL Shortener</title>

</head>
<body>

    <h2>Short URL</h2>
    <section class="form">
        <form id="urlForm">
            <input type="text" name="longUrl" id="longUrl" placeholder="Введите URL">
            <input type="submit" value="Сократить">
        </form>
    </section>

    <a id="shortUrl"></a>

</body>


<script>
    function sendAjaxRequest(url) {
        var xhr = new XMLHttpRequest();

        xhr.open('POST', '/shorten.php', true);

        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {

            if (xhr.status === 200) {
                document.getElementById('shortUrl').href = xhr.responseText;
                document.getElementById('shortUrl').innerText = xhr.responseText;
            }
            else {
                document.getElementById('shortUrl').href = '';
                document.getElementById('shortUrl').innerText = xhr.responseText;
            }
        };

        xhr.send('longUrl=' + encodeURIComponent(url));
    }


    document.getElementById('urlForm').onsubmit = function(event) {
        event.preventDefault();
        let longUrl = document.getElementById('longUrl').value;
        sendAjaxRequest(longUrl);
    };
</script>
</html>