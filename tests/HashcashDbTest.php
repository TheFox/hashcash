<?php

use TheFox\Pow\Hashcash;
use TheFox\Pow\HashcashDb;

class HashcashDbTest extends PHPUnit_Framework_TestCase{
	
	public function testSave(){
		fwrite(STDOUT, __METHOD__.' '.getcwd()."\n");
		$db = new HashcashDb('./test_hashcashs.yml');
		
		for($i = 0; $i < 1000; $i++){
			fwrite(STDOUT, __METHOD__.' test '.$i."\n");
			
			$hashcash = new Hashcash();
			$hashcash->setVersion(1);
			$hashcash->setBits(10);
			$hashcash->setResource('thefox');
			$salt = $hashcash->mint();
			
			$db->addHashcash($hashcash);
			
			fwrite(STDOUT, __METHOD__.' '.$salt."\n");
			
			$s = $db->save();
			fwrite(STDOUT, __METHOD__.' '.$s."\n");
			
			$this->assertTrue($s > 0);
		}
		
		
		
		
		$this->assertFileExists('./test_hashcashs.yml');
	}
	
	/**
	 * @depends testSave
	 */
	public function testLoad(){
		
	}
	
}
