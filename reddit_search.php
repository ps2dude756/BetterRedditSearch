<?php

  $USER_AGENT = 'BetterRedditSearch';

  function is_yes_or_no($string) {
    return $string === 'yes' or $string === 'no';
  }

  class RedditSearch
  {
    /* Query fields */
    public $query = '';
    public $subreddit = '';
    public $author = '';
    public $site = '';
    public $url_text = '';
    public $self_text = '';
    public $self = '';
    public $nsfw = '';
    public $flair = '';

    /* Pagination */
    public $after = ""; // fullname of an item to use as a slicepoint. Do not use if before is set
    public $before = ""; // fullname of an item to use as a slicepoint. Do not use if after is set
    public $count = ""; // The number of items already seen
    public $limit = ""; // The maximum number of items to return
    public $show = ""; // optional parameter. Should usually use all

    /* Other */
    public $sort = "relevance"; // the type of search to perform. Options are relevance, new, hot, top, comments
    public $syntax = "cloudsearch"; // tye type of search engine to use. Options are cloudsearch, lucene, plain. Should usually use cloudsearch
    public $t = "all"; // the slice of history to search over. Options are hour, day, week, month, year, all.

    /*
     * Initialize the instance.
     * @param query
     *    The query to be evaluated
     * @param options
     *    An associative array of extra queries to be evaluated. Valid entries are:
     *      'subreddit': string - return posts only from this subreddit
     *      'author': string - return posts only from this user
     *      'site': string - return posts only from this domain
     *      'url_text': string - return posts who's text contains this url
     *      'self_text': string - return self posts containing this text
     *      'self': 'yes' or 'no' - allow self posts in the results
     *      'nsfw': 'yes' or 'no' - allow nsfw posts in the results
     *      'flair': string return posts who's link flair contains this text
     */
    public function __construct($query, $options=array()) {
      $this->query = $query;

      if (array_key_exists('subreddit', $options)) {
        $this->subreddit = $options['subreddit'];
      }

      if (array_key_exists('author', $options)) {
        $this->author = $options['author'];
      }

      if (array_key_exists('site', $options)) {
        $this->site = $options['site'];
      }

      if (array_key_exists('url_text', $options)) {
        $this->url_text = $options['url_text'];
      }

      if (array_key_exists('self_text', $options)) {
        $this->self_text = $options;
      }

      if (array_key_exists('self', $options) and is_yes_or_no($options['self'])) {
        $this->self = $options['self'];
      }

      if (array_key_exists('nsfw', $options) and is_yes_or_no($options['nsfw'])) {
        $this->nsfw = $options['nsfw'];
      }

      if (array_key_exists('flair', $options)) {
        $this->flair = $options['flair'];
      }
    }

    public function get_search_results() {
      $curl = curl_init($this->get_url());
      curl_setopt($curl, CURLOPT_HTTPGET, true);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      global $USER_AGENT;
      curl_setopt($curl, CURLOPT_USERAGENT, $USER_AGENT);
      $curl_response = curl_exec($curl);
      curl_close($curl);
      $decoded = json_decode($curl_response, true);
      return $decoded['data']['children'];
    }

    private function get_url() {
      $url = 'http://www.reddit.com/search.json?q='.$this->format_query();
      $url = str_replace(' ', '%20', $url);
      $url = str_replace(';', '%3A', $url);
      return $url;
    }

    private function format_query() {
      $query = '';

      if ($this->subreddit) {
        $query .= sprintf('subreddit:%s ', $this->subreddit);
      }

      if ($this->author) {
        $query .= sprintf('author:%s ', $this->author);
      }

      if ($this->site) {
        $query .= sprintf('site:%s ', $this->site);
      }

      if ($this->url_text) {
        $query .= sprintf('url_text:%s ', $this->url_text);
      }

      if ($this->self_text) {
        $query .= sprintf('self_text:%s ', $this->self_text);
      }

      if ($this->self) {
        $query .= sprintf('self:%s ', $this->self);
      }

      if ($this->nsfw) {
        $query .= sprintf('nsfw:%s ', $this->nsfw);
      }

      if ($this->flair) {
        $query .= sprintf('flair:%s ', $this->flair);
      }

      $query .= $this->query;
      return $query;
    }
  }
?> 
