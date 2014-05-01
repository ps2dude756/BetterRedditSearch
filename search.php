<!DOCTYPE html>
<html>
<body>
<?php

$query = str_replace(' ', '%20', $_POST["query"]);

$url = file_get_contents("http://www.reddit.com/search.json?q=" . $query . "&restrict_sr=on");
$json = json_decode($url, true);  

$children = $json['data']['children'];
foreach ($children as $child){
    echo "<p>" . $child['data']['author'] . "<br>" . $child['data']['title'] . "</p>";
}

?>
</body>
</html>

