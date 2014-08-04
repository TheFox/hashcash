<?php

require 'vendor/autoload.php';

use TheFox\Pow\Hashcash;
use TheFox\Utilities\Hex;

declare(ticks = 1);

// Mac: 3.4gz
//const BITS = 12; // 35 sec
//const BITS = 15; // 4:33 min
const BITS = 17; // 18:27 min
const TESTS = 1000;
const TIME_MAX = 30;


$exit = false;

function sig(){
	global $exit;
	$exit = true;
}

$sig = pcntl_signal(SIGALRM, 'sig');
$sig = pcntl_signal(SIGINT, 'sig');
$sig = pcntl_signal(SIGTERM, 'sig');
print 'signal setup: '.($sig ? 'ok' : 'failed')."\n";

fwrite(STDOUT, 'bits: '.BITS."\n");
fwrite(STDOUT, 'tests: '.TESTS."\n");

for($testno = 1; $testno <= TESTS && !$exit; $testno++){
	$hashcash = new Hashcash(BITS, 'example@example.com');
	$stamp = '';
	
	$start = time();
	fwrite(STDOUT, 'mint '.$testno.'/'.TESTS.': ');
	
	pcntl_alarm(TIME_MAX);
	$stamp = $hashcash->mint();
	
	$diff = time() - $start;
	fwrite(STDOUT, $diff.' sec "'.$stamp.'"   '.$hashcash->getAttempts()."\n");
	
	$times[] = $diff;
	
	if(!$stamp) break;
}

fwrite(STDOUT, 'bits: '.BITS."\n");
fwrite(STDOUT, 'tests: '.TESTS."\n");
fwrite(STDOUT, 'avg: '.(array_sum($times) / TESTS).' seconds'."\n");
