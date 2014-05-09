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
  require_once 'reddit_search.php';
  require_once 'postObject.php';
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

    $posts = array();
    if ($whitelist) {
      foreach ($whitelist as $entry) {
        $options['subreddit'] = $entry;
        $posts = array_merge(
          $posts, 
          get_posts($query, $options, $type, $blacklist)
        );
      }
    } else {
      $posts = array_merge(
        $posts,
        get_posts($query, $options, $type, $blacklist)
      );
    }
    $posts = rankPosts($posts, $query);
    displayResults($posts);
  }

  function get_posts($query, $options, $type, $blacklist) {
    $posts = array();
    $after = '';

    while (count($posts) < 100) {
      $search = new RedditSearch($query, $options);
      if ($after) {
        $search->set_pagination(array('after' => $after));
      }
      $results = $search->get_search_results();
      if ($results) {
        $after = end(array_values($results))->name;
        $results = remove_unwanted_results($results, $type, $blacklist);
        $posts = array_merge($posts, $results);
      } else {
        break;
      }
    }

    return $posts;
  }

  function remove_unwanted_results($results, $type, $blacklist) {
    foreach (array_keys($results) as $result_key) {
      if (!shouldDisplay($results[$result_key], $type, $blacklist)) {
        unset($results[$result_key]);
      }
    }

    return $results;
  }

  function get_subreddits($list) {
    // Remove any /r/ or r/ at the beginning of a name
    $list_array = explode(
      ' ', 
      str_replace('r/', '', str_replace('/r/', '', $list))
    );

    foreach (array_keys($list_array) as $key) {
      if (!$list_array[$key]) {
        unset($list_array[$key]);
      }
    }

    // Normalize the array keys
    return array_values($list_array); 
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

  function displayResults($posts) {
    for ($i = 0; $i < min(25, count($posts)); $i++) {
      echo sprintf(
        '<p>
          <a href="http://www.reddit.com%s">%s</a><br />
          votes: %s, %s comments, posted by %s to /r/%s<br />
          posted on: %s<br />
          rankScore: %s
        </p>',
        $posts[$i]->getPermaLink(),
        $posts[$i]->getTitle(),
        $posts[$i]->getScore(),
        $posts[$i]->getNumComments(),
        $posts[$i]->getAuthor(),
        $posts[$i]->getSubreddit(),
        $posts[$i]->date,
        $posts[$i]->getRankScore()
      );
    }
  }

  function rankPosts($posts, $query){
    $query = explode(" ", $query);
    foreach (array_keys($query, '') as $key) {
      unset($query[$key]);
    }

    $retVal = array();
    foreach($posts as $post){
      scorePost($post, $query);
      array_push($retVal, $post);
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
      $titleScore = 0;
      $selfTextScore = 0;
      $term = stem($term, STEM_ENGLISH);

      $title = explode(' ', $postObject->getTitle());
      $selfText = NULL;
      if(!is_null($postObject->getSelfText())){
        $selfText = explode(' ', $postObject->getSelfText());
      }

      foreach ($title as $word) {
        $word = stem($word, STEM_ENGLISH);
        if(strcasecmp($term, $word) == 0){
          $titleScore++;
        }
      }

      if(!is_null($selfText)){
        foreach ($selfText as $word) {
          $word = stem($word, STEM_ENGLISH);
          if(strcasecmp($term, $word) == 0){
            $selfTextScore++;
          }
        }
      }

      $postObject->addToRankScore((log($titleScore + 1) + log($selfTextScore + 1)));
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
