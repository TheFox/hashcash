<?php

require 'vendor/autoload.php';

use TheFox\Pow\Hashcash;
use TheFox\Utilities\Hex;

declare(ticks = 1);

// Mac: 3.4gz
//const BITS = 12; // 35 sec (avg: 0.036 seconds)
//const BITS = 15; // 4:33 min (avg: 0.272 seconds)
//const BITS = 17; // 18 min (avg: 1.108 seconds)
//const BITS = 19; // 74 min (avg: 4.456 seconds)
const BITS = 20; // 147 min (avg: 8.856 seconds)
const TESTS = 1000;
const TIME_MAX = 120;


$next = false;
$exit = false;

function sig_next(){
	fwrite(STDOUT, 'next'."\n");
}

function sig_exit(){
	global $exit;
	$exit = true;
}

$sig = pcntl_signal(SIGALRM, 'sig_next');
print 'signal setup: '.($sig ? 'ok' : 'failed')."\n";
$sig = pcntl_signal(SIGINT, 'sig_exit');
print 'signal setup: '.($sig ? 'ok' : 'failed')."\n";
$sig = pcntl_signal(SIGTERM, 'sig_exit');
print 'signal setup: '.($sig ? 'ok' : 'failed')."\n";

fwrite(STDOUT, 'bits: '.BITS."\n");
fwrite(STDOUT, 'tests: '.TESTS."\n");

$diffMin = TIME_MAX;
$diffMax = 0;
for($testno = 1; $testno <= TESTS && !$exit; $testno++){
	$hashcash = new Hashcash(BITS, 'example@example.com');
	$stamp = '';
	
	$start = time();
	fwrite(STDOUT, 'mint '.$testno.'/'.TESTS.': ');
	
	pcntl_alarm(TIME_MAX);
	$stamp = $hashcash->mint();
	pcntl_alarm(null);
	
	$diff = time() - $start;
	if($diff > $diffMax){
		$diffMax = $diff;
	}
	if($diff < $diffMin){
		$diffMin = $diff;
	}
	
	fwrite(STDOUT, $diff.' sec "'.$stamp.'"   '.$hashcash->getAttempts()."\n");
	
	$times[] = $diff;
	
	if(!$stamp) break;
}

fwrite(STDOUT, 'bits: '.BITS."\n");
fwrite(STDOUT, 'tests: '.TESTS."\n");
fwrite(STDOUT, 'min: '.$diffMin.' seconds'."\n");
fwrite(STDOUT, 'max: '.$diffMax.' seconds'."\n");
fwrite(STDOUT, 'avg: '.(array_sum($times) / TESTS).' seconds'."\n");
