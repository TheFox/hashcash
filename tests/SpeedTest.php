<?php

require 'vendor/autoload.php';

use TheFox\Hashcash\Hashcash;
use TheFox\Utilities\Hex;

declare(ticks = 1);


const TIME_MAX = 30;


print "time max: ".TIME_MAX."\n";

$exit = false;
function sig($no){
	global $exit;
	$exit = true;
	#print "ALARM: $no\n";
}
$sig = pcntl_signal(SIGALRM, 'sig');
print "signal setup: ".($sig ? 'ok' : 'failed')."\n";


for($bit = 10; $bit < 52 && !$exit; $bit++){
	$hashcash = new Hashcash($bit, 'example@example.com');
	$stamp = '';
	
	$start = time();
	fwrite(STDOUT, 'mint '.$bit.' bits: ');
	
	
	pcntl_alarm(TIME_MAX);
	$stamp = $hashcash->mint();
	
	fwrite(STDOUT, (time() - $start).'sec ');
	fwrite(STDOUT, '"'.$stamp.'" - '.$hashcash->getHash()."\n");
	
	if(!$stamp) break;
}
