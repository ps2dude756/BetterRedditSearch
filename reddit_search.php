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
    public $after = "";
    public $before = "";
    public $count = 0;
    public $limit = 100;
    public $show = "all";

    /* Other
     * These parameters should be left as their defaults. Below is an explanation of their function
     * sort: string -  the type of search to perform.
     *  Options are 'relevance', 'new', 'hot', 'top', 'comments'
     * syntax: string - the type of search engine to use.
     *  Options are 'cloudsearch', 'lucene', 'plain'.
     *  Should usually use 'cloudsearch'
     * t: string - the slice of history to search over.
     *  Options are 'hour', 'day', 'week', 'month', 'year', 'all'.
     */
    public $sort = "relevance";
    public $syntax = "cloudsearch";
    public $t = "all";

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

    /*
     * Set pagination data for the search
     * @param pagination
     *    An associative array containing pagination data. Allowed keys are:
     *      'after': string - the fullname of an item to use as a slicepoint. Do not use if before is set 
     *      'before': string - the fullname of an item to use as a slicepoint. Do not use if after is set
     *      'count': integer - the number of items already seen
     *      'limit': integer - the maximum number of items to return
     *      'show': string - optional parameter. Should usually use 'all'
     */
    public function set_pagination($pagination) {
      if (array_key_exists('after', $pagination)) {
        $this->after = $pagination['after'];
      }

      if (array_key_exists('before', $pagination)) {
        $this->before = $pagination['before'];
      }

      if (array_key_exists('count', $pagination)) {
        $this->count = $pagination['count'];
      }

      if (array_key_exists('limit', $pagination)) {
        $this->limit = $pagination['limit'];
      }

      if (array_key_exists('show', $pagination)) {
        $this->show = $pagination['show'];
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
      $url = 'http://www.reddit.com/search.json?q='.$this->format_query().$this->format_pagination().$this->format_other();
      $url = str_replace(' ', '%20', $url);
      $url = str_replace(';', '%3A', $url);
      var_export($url);
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

    private function format_pagination() {
      $format_string = '';

      if ($this->after) {
        $format_string .= '&after='.$this->after;
      }

      if ($this->before) {
        $format_string .= '&before='.$this->before;
      }

      if ($this->count) {
        $format_string .= '&count='.$this->count;
      }

      if ($this->limit) {
        $format_string .= '&limit='.$this->limit;
      }

      if ($this->show) {
        $format_string .= '&show='.$this->show;
      }

      return $format_string;
    }

    private function format_other() {
      $format_string = '';

      if ($this->sort) {
        $format_string .= '&sort='.$this->sort;
      }

      if ($this->syntax) {
        $format_string .= '&syntax='.$this->syntax;
      }

      if ($this->t) {
        $format_string .= '&t='.$this->t;
      }

      return $format_string;
    }
  }
?> 
