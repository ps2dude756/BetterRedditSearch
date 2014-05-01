<?php

$string_reddit = file_get_contents("http://www.reddit.com/r/funny/search.json?q=cat");
$json = json_decode($string_reddit, true);  

$children = $json['data']['children'];
foreach ($children as $child){
    print $child['data']['author'];
    print $child['data']['title'];
    print "\r\n";
}

?>