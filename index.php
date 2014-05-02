<?php
  require 'search_api.php';

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

  $test = new Search('cat');
  display_search_results($test->get_search_results());
?>
