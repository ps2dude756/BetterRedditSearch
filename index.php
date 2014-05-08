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

    $query = removeStopWords($query);

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
      $results = array();
      foreach ($whitelist as $entry) {
        $options['subreddit'] = $entry;
        $search = new RedditSearch($query, $options);
        array_push($results, $search->get_search_results());
      }
      displayResults($results, $type, $blacklist, $query);
    } else {
      $results = array();
      $search = new RedditSearch($query, $options);
      array_push($results, $search->get_search_results());
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
        $url = $data->getURL();
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
        if (!strcasecmp($data->getSubreddit(), $entry)) {
          return false;
        }
      }
    }

    return true;
  }

  function displayResults($results, $type, $blacklist, $query) {
    $results = rankPosts($results, $query);
    foreach ($results as $result) {
      if (shouldDisplay($result, $type, $blacklist)) {
        echo sprintf(
          '<p>
            <a href="http://www.reddit.com%s">%s</a><br />
            votes: %s, %s comments, posted by %s to /r/%s<br />
            posted on: %s<br />
            rankScore: %s
          </p>',
          $result->getPermaLink(),
          $result->getTitle(),
          $result->getScore(),
          $result->getNumComments(),
          $result->getAuthor(),
          $result->getSubreddit(),
          $result->date,
          $result->getRankScore()
        );
      }
    }
  }

  function rankPosts($jsons, $query){
    $query = explode(" ", $query);

    $retVal = array();
    foreach($jsons as $results){
      foreach($results as $result){
        $data = $result['data'];
        $title = $data['title'];
        $score = $data['score'];
        $numComments = $data['num_comments'];
        $author = $data['author'];
        $subreddit = $data['subreddit'];
        $date = date('D, M d Y @ h:i:s:a T', $data['created_utc']);
        $selfText = $data['selftext'];
        $url = $data['url'];
        $permalink = $data['permalink'];

        $post = new postObject($title, $score, $numComments, $author, $subreddit, $date, $selfText, $url, $permalink);
        scorePost($post, $query);
        array_push($retVal, $post);
      }
    }
    usort($retVal, "cmp");
    return $retVal; 
  }

  function cmp($a, $b){
    if($a->getScore() < $b->getScore()) {
      return 1;
    }
    else if($a->getScore() > $b->getScore()){
      return -1;
    }
    else{
      return 0; 
    }
  }

  function scorePost($postObject, $query) {
    foreach ($query as $term) {
      $postObject->addToRankScore(substr_count($postObject->getTitle(), $term));
      if(!is_null($postObject->getSelfText())){
        $postObject->addToRankScore(substr_count($postObject->getSelfText(), $term));
      }
    }
  }

  function removeStopWords($query){
    $stopWords = array("a","able","about","across","after","all","almost","also","am","among","an","and","any","are","as","at","be","because","been","but","by","can","cannot","could","dear","did","do","does","either","else","ever","every","for","from","get","got","had","has","have","he","her","hers","him","his","how","however","i","if","in","into","is","it","its","just","least","let","like","likely","may","me","might","most","must","my","neither","no","nor","not","of","off","often","on","only","or","other","our","own","rather","said","say","says","she","should","since","so","some","than","that","the","their","them","then","there","these","they","this","tis","to","too","twas","us","wants","was","we","were","what","when","where","which","while","who","whom","why","will","with","would","yet","you","your","ain't","aren't","can't","could've","couldn't","didn't","doesn't","don't","hasn't","he'd","he'll","he's","how'd","how'll","how's","i'd","i'll","i'm","i've","isn't","it's","might've","mightn't","must've","mustn't","shan't","she'd","she'll","she's","should've","shouldn't","that'll","that's","there's","they'd","they'll","they're","they've","wasn't","we'd","we'll","we're","weren't","what'd","what's","when'd","when'll","when's","where'd","where'll","where's","who'd","who'll","who's","why'd","why'll","why's","won't","would've","wouldn't","you'd","you'll","you're","you've");
    $query = preg_replace('/\b('.implode('|',$stopWords).')\b/','',$query);
    return $query;
  }

  main();

  
?>

</body>
</html>
