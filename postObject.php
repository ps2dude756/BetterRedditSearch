<?
class PostObject{
	private $title;
	private $score;
	private $numComments;
	private $author;
	private $subreddit;
	private $date;
	private $link;
	private $rankScore;
	private $selfText; 

	public function __construct($title, $score, $numComments, $author, $subreddit, $date, $link, $selfText){
		$this->title = $title;
		$this->score = $score;
		$this->numComments = $numComments;
		$this->author = $author;
		$this->subreddit = $subreddit;
		$this->date = $date;
		$this->link = $link;
		$this->selfText = $selfText;
		$this->rankScore = $this->score;


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

	public function getTitle(){
		return $this->title;
	}

	public function getSubreddit(){
		return $this->subreddit;
	}

	public function getDate(){
		return $this->date;
	}

	public function getLink(){
		return $this->link;
	}

	public function getSelfText(){
		return $this->selfText;
	}

	public function getRankScore(){
		return $this->rankScore;
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

	public function setLink($link){
		$this->link = $link;
	}

	public function setSelfText($text){
		$this->selfText = $text; 
	}

	public function setScore($score){
		$this->score = $score; 
	}

	public function addToRankScore($val){
		$this->rankScore += $this->rankScore + $val; 
	}

}
?>