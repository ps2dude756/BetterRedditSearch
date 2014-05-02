<?php
  require 'reddit_search.php';

  function display_search_results($search_results) {
    foreach ($search_results as $search_result) {
      $result = sprintf(
        "<p><a href=\"http://reddit.com%s\">%s</a></p>", 
        $search_result['data']['permalink'],
        $search_result['data']['title']
      );
      echo $result;
    }
  }

  $test = new RedditSearch('cats');
  $results = $test->get_search_results();
  display_search_results($results);
?>
