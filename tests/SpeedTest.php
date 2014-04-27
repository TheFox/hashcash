<?php

require 'vendor/autoload.php';

use TheFox\Hashcash\Hashcash;
use TheFox\Utilities\Hex;

declare(ticks = 1);


const TIME_MAX = 30;

$tests = array( 1, 0 );

// Test 1
if($tests[0]){
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
		
		fwrite(STDOUT, (time() - $start).'sec '.'"'.$stamp.'"   '.$hashcash->getHash()."\n");
		
		if(!$stamp) break;
	}
}

// Test 2
if($tests[1]){
	
	$bits = 20;
	$loops = 100;
	
	$seconds = array();
	for($n = 0; $n < $loops; $n++){
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
		
		fwrite(STDOUT, $t.'sec '.'"'.$stamp.'"   '.$hashcash->getHash()."\n");
		#if($stamp) fwrite(STDOUT, $stamp."\n");
		
	}

	#var_export($seconds);
	
	$sum = 0;
	foreach($seconds as $time){
		$sum += $time;
	}
	
	fwrite(STDOUT, "avg: ".($sum / $loops)."\n");
}

