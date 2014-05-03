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

	public function __construct($init = array()){
		$this->title = $init['title'];
		$this->score = $init['score'];
		$this->numComments = $init['numComments'];
		$this->author = $init['author'];
		$this->title = $init['title'];
		$this->subreddit = $init['subreddit'];
		$this->date = $init['date'];
		$this->link = $init['link'];
		$this->selfText = $init['selfText'];
		$this->rankScore = $init['rankScore'];


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

	public static cmp($a, $b){
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

}
?>