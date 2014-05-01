<!DOCTYPE html>
<html>
<body>
<?php

$query = str_replace(' ', '%20', $_POST['query']);
$white = $_POST['white'];
$black = $_POST['black'];
$searchType = $_POST['searchType'];
//$username = $_POST['uname'];
//$password = $_POST['password'];

$whiteList = explode(' ', $white);
$blackList = explode(' ', $black);

$nsfwFilter = NULL;
//$onlySubscribed = NULL;

if (isset($_POST['nsfwFilter'])){
	$nsfwFilter = true;
}
else{
	$nsfwFilter = false;
}

/*if (isset($_POST['subscribed'])){
	$onlySubscribed = true;
}
else{
	$onlySubscribed = false;
}*/

if ($searchType == 'selfPosts'){
	$query = $query . '%20self:yes';
}

/*if ($onlySubscribed && strlen($username) > 0 && strlen($password) > 0){

}*/

function printResults($json, $nsfwFilter, $searchType, $blackList){
	$children = $json['data']['children'];
	foreach ($children as $child){
		$www = $child['data']['url'];
		$isImage = (strpos($www, 'imgur') !== FALSE || strpos($www, 'gif') !== FALSE || strpos($www, 'minus') !== FALSE || strpos($www, 'jpeg') !== FALSE || strpos($www, 'png') !== FALSE || strpos($www, 'bmp') !== FALSE);

		$nsfw = ($nsfwFilter && $child['data']['over_18'] == true);
		$onlyPic = ($searchType == 'images' && !$isImage);
		$onlyArticle = ($searchType == 'articles' && ($isImage || $child['data']['is_self'] == true));
		$blackListed = in_array(('/r/' . $child['data']['subreddit']), $blackList);

		if($nsfw || $onlyPic || $onlyArticle || $blackListed){
			continue;
		}

	    echo '<p>' . $child['data']['author'] . '<br>' . $child['data']['title'] . '</p>';
	}
}

if(count($whiteList) == 0){
	$url = file_get_contents('http://www.reddit.com/search.json?q=' . $query . '&restrict_sr=on&limit=100');
	$json = json_decode($url, true); 

	printResults($json, $nsfwFilter, $searchType, $blackList); 
}
else{
	foreach ($whiteList as $sub){
		$url = file_get_contents('http://www.reddit.com' . $sub . '/search.json?q=' . $query . '&restrict_sr=on&limit=100');
		$json = json_decode($url, true);

		echo '<p><b>' . $sub . '</b></p>';
		printResults($json, $nsfwFilter, $searchType, $blackList);
	}
}

?>
</body>
</html>

