<?php
function get_postObject($result) {
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
  $name = $data['name'];

  return new PostObject($title, $score, $numComments, $author, $subreddit, $date, $selfText, $url, $permalink, $name);
}

class PostObject{
	public $title;
	public $score;
	public $numComments;
	public $author;
	public $subreddit;
	public $date;
	public $rankScore;
  public $selfText; 
  public $url;
  public $permalink;
  public $name;

	public function __construct($title, $score, $numComments, $author, $subreddit, $date, $selfText, $url, $permalink, $name){
		$this->title = $title;
		$this->score = $score;
		$this->numComments = $numComments;
		$this->author = $author;
		$this->subreddit = $subreddit;
		$this->date = $date;
		$this->selfText = $selfText;
		$this->rankScore = .1 * log($this->score + 1);
		$this->url = $url;
    	$this->permalink = $permalink;
   		$this->name = $name;
  }

	public function getTitle(){
		return $this->title;
	}

	public function getScore(){
		return $this->score;
	}

	public function getNumComments(){
		return $this->numComments;
	}

	public function getAuthor(){
		return $this->author; 
	}

	public function getSubreddit(){
		return $this->subreddit;
	}

	public function getDate(){
		return $this->date;
	}

	public function getSelfText(){
		return $this->selfText;
	}

	public function getRankScore(){
		return $this->rankScore;
	}

	public function getURL(){
		return $this->url;
	}

	public function getPermaLink(){
		return $this->permalink;
	}

	public function setTitle($title){
		$this->title = $title;
	}

	public function setScore($score){
		$this->score = $score;
	}

	public function setNumComments($num){
		$this->numComments = $num;
	}

	public function setAuthor($author){
		$this->author = $author;
	}

	public function setSubreddit($subreddit){
		$this->subreddit = $subreddit;
	}

	public function setDate($date){
		$this->date = $date;
	}

	public function setSelfText($text){
		$this->selfText = $text; 
	}

	public function setRankScore($score){
		$this->rankScore = $rankScore; 
	}

	public function addToRankScore($val){
		$this->rankScore += $val; 
	}

}
?>
