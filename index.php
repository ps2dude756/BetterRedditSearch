<!DOCTYPE html>
<html>
<body>

<form action="" method="get">
	Search Reddit: <input type="search" name="query"><br>
	<!--Subreddits to Search: <input type="search" name="white"><br>-->
	<!--Subreddits to Avoid: <input type="search" name="black"><br>-->
	<!--<input type="radio" name="searchType" value="all" checked>All Posts-->
	<!--<input type="radio" name="searchType" value="selfPosts">Only Self Posts-->
	<!--<input type="radio" name="searchType" value="images">Only Image Posts-->
	<!--<input type="radio" name="searchType" value="articles">Only Article Posts<br>-->
  <!--<input type="checkbox" name="nsfwFilter" value="true">Filter nsfw<br>-->
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
  date_default_timezone_set('America/Chicago');

  function get_options() {
    $options = array();

    if (isset($_GET['self'])) {
      $options['self'] = $_GET['self'];
    }

    if (isset($_GET['nsfw'])) {
      $options['nsfw'] = $_GET['nsfw'];
    }

    return $options;
  }

  function displayResults($results) {
    foreach ($results as $result) {
      $data = $result['data'];
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

  $query = "";
  if (isset($_GET['query'])) {
    $query = $_GET['query'];
  }
  $options = get_options();


  $search = new RedditSearch($query, $options);
  $results = $search->get_search_results();
  displayResults($results);

?>

</body>
</html>
