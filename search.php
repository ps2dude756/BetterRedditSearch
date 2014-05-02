<!DOCTYPE html>
<html>
<body>
<?php
  require 'reddit_search.php';

$query = str_replace(' ', '%20', $_POST['query']);
$white = $_POST['white'];
$black = $_POST['black'];
$searchType = $_POST['searchType'];

$whiteList = explode(' ', $white);
$blackList = explode(' ', $black);

$nsfwFilter = NULL;

if (isset($_POST['nsfwFilter'])){
	$nsfwFilter = true;
}
else{
	$nsfwFilter = false;
}

if ($searchType == 'selfPosts'){
	$query = $query . '%20self:yes';
}

function printResults($json, $nsfwFilter, $searchType, $blackList){
	$children = $json;
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
  $search = new RedditSearch($query);
  $results = $search->get_search_results();

	printResults($results, $nsfwFilter, $searchType, $blackList); 
}
else{
	foreach ($whiteList as $sub){
    $search = new RedditSearch($query, array('subreddit' => $sub));
    $results = $search->get_search_results();

		echo '<p><b>' . $sub . '</b></p>';
		printResults($results, $nsfwFilter, $searchType, $blackList);
	}
}

?>
</body>
</html>

