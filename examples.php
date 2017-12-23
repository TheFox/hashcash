<?php

require 'vendor/autoload.php';

use TheFox\Pow\Hashcash;

// Example 1: simple mint (real world example)
$hashcash = new Hashcash();
$hashcash->setBits(20);
$hashcash->setResource('example@example.com');
try {
    $stamp = $hashcash->mint();
    printf("stamp1: '%s'\n", $stamp);
} catch (Exception $e) {
    printf("ERROR 1: %s\n", $e->getMessage());
}

// Example 2a: simple mint, another way (real world example)
$hashcash = new Hashcash(20, 'example@example.com');
$stamp = '';
try {
    $stamp = $hashcash->mint();
    printf("stamp2: '%s'\n", $stamp);
} catch (Exception $e) {
    printf("ERROR 2: %s\n", $e->getMessage());
}

// Example 2b: verify a stamp (real world example)
$hashcash = new Hashcash();
try {
    printf("stamp2 verify: '%s'\n", $hashcash->verify($stamp) ? 'Ok' : 'failed');
} catch (Exception $e) {
    printf("ERROR 3: %s\n", $e->getMessage());
}

// Example 3a: use a certain date
$hashcash = new Hashcash(20, 'example@example.com');
$hashcash->setDate('870221');
$stamp = '';
try {
    $stamp = $hashcash->mint();
    printf("stamp3: '%s'\n", $stamp);
} catch (Exception $e) {
    printf("ERROR 4: %s\n", $e->getMessage());
}

// Example 3b: verify a stamp, must fail
$hashcash = new Hashcash();
$hashcash->setExpiration(3600 * 24 * 2); // Expire in 2 days.
try {
    printf("stamp3 verify: '%s' (must fail)\n", $hashcash->verify($stamp) ? 'Ok' : 'failed');
} catch (Exception $e) {
    printf("ERROR 5: %s\n", $e->getMessage());
}

// Example 4a: use a certain date
$hashcash = new Hashcash(20, 'example@example.com');
$hashcash->setDate('870221');
$stamp = '';
try {
    $stamp = $hashcash->mint();
    printf("stamp4: '%s'\n", $stamp);
} catch (Exception $e) {
    printf("ERROR 6: %s\n", $e->getMessage());
}

// Example 4a: verify a stamp, ignore expiration
$hashcash = new Hashcash();
$hashcash->setExpiration(0); // Never expire.
try {
    printf( "stamp4 verify: '%s'\n",$hashcash->verify($stamp) ? 'Ok' : 'failed');
} catch (Exception $e) {
    printf("ERROR 7: %s\n", $e->getMessage());
}

// Example 5: use 1 attempt, must fail with this configuration
$hashcash = new Hashcash(19, 'example@example.com');
$hashcash->setDate('140427');
$hashcash->setSalt('axfcrlV1hxLvF6J9BeDiLw==');
$hashcash->setMintAttemptsMax(1);
$stamp = '';
try {
    $stamp = $hashcash->mint();
    printf("stamp5: '%s'\n", $stamp);
} catch (Exception $e) {
    printf("ERROR 8: %s\n", $e->getMessage());
}

// Example 6: use infinite attempts
$hashcash = new Hashcash(19, 'example@example.com');
$hashcash->setDate('140427');
$hashcash->setSalt('axfcrlV1hxLvF6J9BeDiLw==');
$hashcash->setMintAttemptsMax(0); // Use infinite attempts
$stamp = '';
try {
    $stamp = $hashcash->mint();
    printf("stamp6: '%s'\n", $stamp);
} catch (Exception $e) {
    printf("ERROR 9: %s\n", $e->getMessage());
}

// Example 7a: use short syntax
$stamp = (new Hashcash(20, 'example@example.com'))
    ->setDate(date(Hashcash::DATE_FORMAT12))
    ->mint()
;

// Example 7b: verify short syntax which is valid for 3 minutes
try {
    $status=(new Hashcash())->setExpiration(60 * 3)->verify($stamp) ? 'Ok' : 'failed';
    printf("stamp7 verify: '%s'\n",$status);
} catch (Exception $e) {
    printf("ERROR 10: %s\n", $e->getMessage());
}
