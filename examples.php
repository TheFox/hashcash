<?php

require 'vendor/autoload.php';

use TheFox\Pow\Hashcash;


// Example 1: simple mint (real world example)
$hashcash = new Hashcash();
$hashcash->setBits(20);
$hashcash->setResource('example@example.com');
try {
    print "stamp1: '" . $hashcash->mint() . "'\n";
} catch (Exception $e) {
    print 'ERROR 1: ' . $e->getMessage() . "\n";
}


// Example 2a: simple mint, another way (real world example)
$hashcash = new Hashcash(20, 'example@example.com');
$stamp = '';
try {
    $stamp = $hashcash->mint();
    print "stamp2: '" . $stamp . "'\n";
} catch (Exception $e) {
    print 'ERROR 2: ' . $e->getMessage() . "\n";
}

// Example 2b: verify a stamp (real world example)
$hashcash = new Hashcash();
try {
    print "stamp2 verify: '" . ($hashcash->verify($stamp) ? 'Ok' : 'failed') . "'\n";
} catch (Exception $e) {
    print 'ERROR 3: ' . $e->getMessage() . "\n";
}


// Example 3a: use a certain date
$hashcash = new Hashcash(20, 'example@example.com');
$hashcash->setDate('870221');
$stamp = '';
try {
    $stamp = $hashcash->mint();
    print "stamp3: '" . $stamp . "'\n";
} catch (Exception $e) {
    print 'ERROR 4: ' . $e->getMessage() . "\n";
}

// Example 3b: verify a stamp, must fail
$hashcash = new Hashcash();
$hashcash->setExpiration(3600 * 24 * 2); // Expire in 2 days.
try {
    print "stamp3 verify: '" . ($hashcash->verify($stamp) ? 'Ok' : 'failed') . "' (must fail)\n";
} catch (Exception $e) {
    print 'ERROR 5: ' . $e->getMessage() . "\n";
}


// Example 4a: use a certain date
$hashcash = new Hashcash(20, 'example@example.com');
$hashcash->setDate('870221');
$stamp = '';
try {
    $stamp = $hashcash->mint();
    print "stamp4: '" . $stamp . "'\n";
} catch (Exception $e) {
    print 'ERROR 6: ' . $e->getMessage() . "\n";
}

// Example 4a: verify a stamp, ignore expiration
$hashcash = new Hashcash();
$hashcash->setExpiration(0); // Never expire.
try {
    print "stamp4 verify: '" . ($hashcash->verify($stamp) ? 'Ok' : 'failed') . "'\n";
} catch (Exception $e) {
    print 'ERROR 7: ' . $e->getMessage() . "\n";
}


// Example 5: use 1 attempt, must fail with this configuration
$hashcash = new Hashcash(19, 'example@example.com');
$hashcash->setDate('140427');
$hashcash->setSalt('axfcrlV1hxLvF6J9BeDiLw==');
$hashcash->setMintAttemptsMax(1);
$stamp = '';
try {
    $stamp = $hashcash->mint();
    print "stamp5: '" . $stamp . "'\n";
} catch (Exception $e) {
    print 'ERROR 8: ' . $e->getMessage() . "\n";
}


// Example 6: use infinite attempts
$hashcash = new Hashcash(19, 'example@example.com');
$hashcash->setDate('140427');
$hashcash->setSalt('axfcrlV1hxLvF6J9BeDiLw==');
$hashcash->setMintAttemptsMax(0); // Use infinite attempts
$stamp = '';
try {
    $stamp = $hashcash->mint();
    print "stamp6: '" . $stamp . "'\n";
} catch (Exception $e) {
    print 'ERROR 9: ' . $e->getMessage() . "\n";
}

// Example 7a: use short syntax
$stamp = Hashcash::newInstance(20, 'example@example.com')
    ->setDate(date(Hashcash::DATE_FORMAT12))
    ->mint();

// Example 7b: verify short syntax which is valid for 3 minutes
try {
    print "stamp7 verify: '" . (Hashcash::newInstance()->setExpiration(60 * 3)->verify($stamp) ? 'Ok' : 'failed') . "'\n";
} catch (Exception $e) {
    print 'ERROR 10: ' . $e->getMessage() . "\n";
}
