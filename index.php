<!DOCTYPE html>
<html>
<body>

<form action="" method="get">
	Search Reddit: <input type="search" name="query"><br>
	Subreddits to Search (space separated): <input type="search" name="whitelist"><br>
	Subreddits to Avoid (space separated): <input type="search" name="blacklist"><br>
  Types of posts: <select name="type">
    <option value="">All Posts</option>
    <option value="image">Only Image Posts</option>
    <option value="article">Only Article Posts</option>
  </select><br />
  Self Posts: <select name="self">
    <option value="">Allow</option>
    <option value="no">None</option>
    <option value="yes">Only</option>
  </select><br />
  NSFW Posts: <select name="nsfw">
    <option value="">Allow</option>
    <option value="no">None</option>
    <option value="yes">Only</option>
  </select><br />
  <input type="submit">
</form>

<?php
  require 'reddit_search.php';
  require 'postObject.php';
  date_default_timezone_set('America/Chicago');

  function main() {
    $query = '';
    if (isset($_GET['query'])) {
      $query = $_GET['query'];
    }

    $type = '';
    if (isset($_GET['type'])) {
      $type = $_GET['type'];
    }

    $options = get_options($type);

    $blacklist = array();
    if (isset($_GET['blacklist'])) {
      $blacklist = get_subreddits($_GET['blacklist']);
    }

    $whitelist = array();
    if (isset($_GET['whitelist'])) {
      $whitelist = get_subreddits($_GET['whitelist']);
    }

    if ($whitelist) {
      foreach ($whitelist as $entry) {
        $options['subreddit'] = $entry;
        $search = new RedditSearch($query, $options);
        $results = $search->get_search_results();
        echo sprintf('<p><b>/r/%s</b>', $entry);
        displayResults($results, $type, $blacklist);
      }
    } else {
      $search = new RedditSearch($query, $options);
      $results = $search->get_search_results();
      displayResults($results, $type, $blacklist, $query);
    }
  }

  function get_subreddits($list) {
    return explode(' ', str_replace('/r/', '', $list));
  }

  function get_options($type) {
    $options = array();

    if (isset($_GET['self'])) {
      $options['self'] = $_GET['self'];
    }

    if (isset($_GET['nsfw'])) {
      $options['nsfw'] = $_GET['nsfw'];
    }

    if ($type === 'article') {
      $optiosn['self'] = 'no';
    }

    return $options;
  }

  function shouldDisplay($data, $type, $blacklist) {
    if ($type === 'image') {
        $url = $data['url'];
        $image_types = array('imgur', 'gif', 'minus', 'jpeg', 'png', 'bmp');
        foreach ($image_types as $image_type) {
          if (strpos($url, $image_type)) {
            return false;
          }
        }
    }
    elseif ($type === 'article') {
      // already removed self posts, just remove images
      return !shouldDisplay($data, 'image');
    }

    if ($blacklist) {
      foreach ($blacklist as $entry) {
        if (!strcasecmp($data['subreddit'], $entry)) {
          return false;
        }
      }
    }

    return true;
  }

  function displayResults($results, $type, $blacklist, $query) {
    $results = rankPosts($results, $query)
    foreach ($results as $result) {
      $data = $result['data'];
      if (shouldDisplay($data, $type, $blacklist)) {
        echo sprintf(
          '<p>
            <a href="http://www.reddit.com%s">%s</a><br />
            votes: %s, %s comments, posted by %s to /r/%s<br />
            posted on: %s
          </p>',
          $data['permalink'],
          $data['title'],
          $data['score'],
          $data['num_comments'],
          $data['author'],
          $data['subreddit'],
          date('D, M d Y @ h:i:s:a T', $data['created_utc'])
        );
      }
    }
  }

  function rankPosts($results, $query){
    $query = explode(" ", $query);

    $retVal = array();
    foreach($results as $result){
      $data = $results['data'];
      $title = $data['title'];
      $score = $data['score'];
      $numComments = $data['num_comments'];
      $author = $data['author'];
      $subreddit = $data['subreddit'];
      $date = date('D, M d Y @ h:i:s:a T', $data['created_utc']);
      $selfText = $data['selftext'];

      $post = new postObjcet($title, $score, $numComments, $author, $subreddit, $date, $link, $selfText)
      scorePost($post, $query);
      array_push($retVal, $post);
    }

    usort($retVal, "cmp")

    return $retVal; 
  }

  function cmp($a, $b){
    if $a->getScore() < $b->getScore(){
      return -1;
    }
    else if($a->getScore() > $b->getScore()){
      return 1;
    }
    else{
      return 0; 
    }
  }

  function scorePost($postObject, $query) {
    foreach ($query as $term) {
      $postObjcet.addToRankScore(substr_count($postObjcet.getTitle(), $term));
      if(!is_null($postObjcet.getSelfText())){
        $postObjcet.addToRankScore(substr_count($postObjcet.getSelfText(), $term));
      }
    }
  }

  main();

  
?>

</body>
</html>
