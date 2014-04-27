<?php

require 'vendor/autoload.php';

use TheFox\Hashcash\Hashcash;


// Example 1: simple mint
$hashcash = new Hashcash();
$hashcash->setBits(20);
$hashcash->setResource('example@example.com');
try{
	print "hashcash stamp1: '".$hashcash->mint()."'\n";
}
catch(Exception $e){
	print "ERROR 1: ".$e->getMessage()."\n";
}


// Example 2a: simple mint, another way
$hashcash = new Hashcash(20, 'example@example.com');
$stamp = '';
try{
	$stamp = $hashcash->mint();
	print "hashcash stamp2: '".$stamp."'\n";
}
catch(Exception $e){
	print "ERROR 2: ".$e->getMessage()."\n";
}

// Example 2b: verify a stamp
$hashcash = new Hashcash();
try{
	print "hashcash stamp2 verify: '".( $hashcash->verify($stamp) ? 'Ok' : 'failed' )."'\n";
}
catch(Exception $e){
	print "ERROR 3: ".$e->getMessage()."\n";
}


// Example 3a: use a certain date
$hashcash = new Hashcash(20, 'example@example.com');
$hashcash->setDate('870221');
$stamp = '';
try{
	$stamp = $hashcash->mint();
	print "hashcash stamp3: '".$stamp."'\n";
}
catch(Exception $e){
	print "ERROR 4: ".$e->getMessage()."\n";
}

// Example 3b: verify a stamp, must fail
$hashcash = new Hashcash();
$hashcash->setExpiration(3600 * 24 * 2); // Expire in 2 days.
try{
	print "hashcash stamp3 verify: '".( $hashcash->verify($stamp) ? 'Ok' : 'failed' )."' (must fail)\n";
}
catch(Exception $e){
	print "ERROR 5: ".$e->getMessage()."\n";
}


// Example 4a: use a certain date
$hashcash = new Hashcash(20, 'example@example.com');
$hashcash->setDate('870221');
$stamp = '';
try{
	$stamp = $hashcash->mint();
	print "hashcash stamp4: '".$stamp."'\n";
}
catch(Exception $e){
	print "ERROR 6: ".$e->getMessage()."\n";
}

// Example 4a: verify a stamp, ignore expiration
$hashcash = new Hashcash();
$hashcash->setExpiration(0); // Never expire.
try{
	print "hashcash stamp4 verify: '".( $hashcash->verify($stamp) ? 'Ok' : 'failed' )."'\n";
}
catch(Exception $e){
	print "ERROR 7: ".$e->getMessage()."\n";
}

?>