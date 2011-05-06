<?php
// setting up the include path
require_once('./secure/config.php');

$inc_path = get_include_path();
set_include_path($inc_path . PATH_SEPARATOR . implode($lib_paths, PATH_SEPARATOR) );


// includes
require_once('mpqfile.php');
require_once('sc2replay.php');
require_once('sc2map.php');


/*

			helpers

*/ 
function link_to( $target ){
    global $base_path;
    return ($base_path . $target);
}



/*

		classes

*/

class BaseException extends Exception {
	
}

class FileException extends BaseException {
	
}

class BusinessLogicException extends BaseException {
	
}

class Player{
	protected $parser_values;
	
	public function __construct($parser_values){
		$this->parser_values = $parser_values;
	}
	
	public function isWinner(){
		if($this->parser_values['won']){
			return true;
		} else {
			return false;
		}
	}
	
	public function getRaseFullName(){
		return $this->parser_values['race'];
	}
	
	public function getName(){
		return $this->parser_values['name'];
	}
}

class Map{
	protected $sc2_replay;
	protected $image_path;
	
	public function __construct($sc2_replay){
		$this->sc2_replay = $sc2_replay;
	}
	
	public function getName(){
		return $this->sc2_replay->getMapName();
	}
	
	public function getImagePath(){
		if(!$this->image_path){
			$this->image_path = "./image/maps/";
			$invalid = array(' ', "'");
			$this->image_path .= str_replace($invalid, "_", $this->getName());
			$this->image_path .= ".jpg";
		}
		return $this->image_path;
	}
}

class Replay{
	
	protected $filename_;
	protected $mpq_file_;
	protected $sc2_replay_;
	protected $map;
	protected $teams_;
	
	public function __construct ($filename) {
		$this->filename_ = $filename;
		if(!is_readable($this->filename_)){
			throw new FileException("can't read replay: $this->filename_");
		}
		
		$this->mpq_file_ = new MPQFile($this->filename_);
		if(! $this->mpq_file_->getState())
			throw new FileException("invalid replay file");
		$this->sc2_replay_ = $this->mpq_file_->parseReplay();
	}
	
	public function teams(){
		// caching
		if(!$this->teams_){
			$this->teams_ = new ArrayObject();
			foreach($this->sc2_replay_->getPlayers() as $player){
				$team_number = $player['team'];
				$this->teams_[$team_number][] = new Player($player);
			}
		}
		return $this->teams_;
	}
	
	public function map(){
		if(!$this->map){
			$this->map = new Map($this->sc2_replay_);
		}
		return $this->map;
	}
	
}

class Game{
	
}

class OneVsOneGame extends Game {
	
	protected $replay;
	protected $players;
	protected $map;
	
	public function __construct ($filename) {
		$this->players = new ArrayObject();
		
		$this->replay = new Replay($filename);
		
		$teams = $this->replay->teams();
		if($teams->count() != 2) {
			throw new BusinessLogicException("Replay needs to have exacty 2 Teams in a 1vs1 Game.");
		}

		foreach($teams as $team){
			if(count($team) != 1){
				throw new BusinessLogicException("Each team hase to have exactly 1 player in a 1vs1 Game.");
			}

			foreach($team as $player){
				$this->players[] = $player;
			}
		}
	}
	
	public function getMap(){
		return $this->replay->map();
	}
	
	public function getPlayers(){
		return $this->players;
	}
	
}






