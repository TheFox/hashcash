<?php

require 'vendor/autoload.php';

use TheFox\Hashcash\Hashcash;
use TheFox\Utilities\Hex;

declare(ticks = 1);


const TIME_MAX = 30;

$test1 = 0;
$test2 = 1;

// Test 1
if($test1){
	print "time max: ".TIME_MAX."\n";

	$exit = false;
	function sig($no){
		global $exit;
		$exit = true;
		#print "ALARM: $no\n";
	}
	$sig = pcntl_signal(SIGALRM, 'sig');
	print "signal setup: ".($sig ? 'ok' : 'failed')."\n";


	for($bits = 10; $bits < 52 && !$exit; $bits++){
		$hashcash = new Hashcash($bits, 'example@example.com');
		$stamp = '';
		
		$start = time();
		fwrite(STDOUT, 'mint '.$bits.' bits: ');
		
		pcntl_alarm(TIME_MAX);
		$stamp = $hashcash->mint();
		
		fwrite(STDOUT, (time() - $start).'sec ');
		fwrite(STDOUT, '"'.$stamp.'" - '.$hashcash->getHash()."\n");
		
		if(!$stamp) break;
	}
}

// Test 2
if($test2){
	$bits = 19;
	$seconds = array();
	for($n = 0; $n < 1000; $n++){
		$hashcash = new Hashcash($bits, 'example@example.com');
		$stamp = '';
		
		$start = time();
		fwrite(STDOUT, $n.' mint '.$bits.' bits: ');
		
		try{
			$stamp = $hashcash->mint();
		}
		catch(Exception $e){
			fwrite(STDOUT, 'error ');
		}
		
		$t = time() - $start;
		$seconds[] = $t;
		
		fwrite(STDOUT, $t.'sec ');
		fwrite(STDOUT, '"'.$stamp.'" - '.$hashcash->getHash()."\n");
		
	}

	var_export($seconds);
}

