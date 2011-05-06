<?php

function p($msg){
	echo $msg . "\n<br>";
}

$before = get_include_path();

$arr = array('lib/', 'lib/bla');

set_include_path($before . PATH_SEPARATOR. implode($arr, PATH_SEPARATOR));

$after = get_include_path();


p($before);
p($after);

class Bla{
	public $a;
	public $v;
	public $b;
	
	public $asdf;
}

$a = new Bla();
foreach($a as $attribute => $value){
	p($attribute);
}