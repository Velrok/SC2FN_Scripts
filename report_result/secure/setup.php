<?php

require_once('./secure/config.php');

// setting up the include path
$inc_path = get_include_path();
set_include_path($inc_path . PATH_SEPARATOR . implode($lib_paths, PATH_SEPARATOR) );


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

class Replay{
	
	protected $filename_;
	
	public function __construct ($filename) {
		$this->filename_ = $filename;
		if(!is_readable($this->filename_)){
			throw new FileException("can't read replay: $this->filename_");
		}
	}
	
}

class Game{
	
}

class OneVsOneGame extends Game {
	
	$replay;
	$players;
	
	public function __construct ($filename) {
		$this->players = new Array(2);
		
		$this->replay = new Replay($filename);
		
		$teams = $this->replay->teams();
		if($teams->count() != 2) {
			throw new BusinessLogicException("Replay needs to have exacty 2 Teams in a 1vs1 Game.");
		}

		foreach($teams as $team){
			if($team->players()->count() != 1){
				throw new BusinessLogicException("Each team hase to have exactly 1 player in a 1vs1 Game.");
			}

			$players = $team->players();
			$this->players[] = $players[0];
		}
	}
	
}






