<?php

use TheFox\Pow\Hashcash;
use TheFox\Pow\HashcashDb;

class HashcashDbTest extends PHPUnit_Framework_TestCase{
	
	public function testDoublespend1(){
		#fwrite(STDOUT, __METHOD__."\n");
		$db = new HashcashDb();
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setResource('thefox');
		$hashcash->setSuffix(1);
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setResource('thefox');
		$hashcash->setSuffix(2);
		$this->assertTrue($db->addHashcash($hashcash));
		
		$this->assertEquals(2, count($db->getHashcashs()));
	}
	
	public function testDoublespend2(){
		#fwrite(STDOUT, __METHOD__."\n");
		$db = new HashcashDb();
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setResource('thefox');
		$hashcash->setSuffix(1);
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setResource('thefox');
		$hashcash->setSuffix(1);
		$this->assertFalse($db->addHashcash($hashcash));
		
		$this->assertEquals(1, count($db->getHashcashs()));
	}
	
	public function testSave1(){
		#fwrite(STDOUT, __METHOD__.''."\n");
		$db = new HashcashDb('./test_hashcashs1.yml');
		
		for($i = 0; $i < 1000; $i++){
			$hashcash = new Hashcash();
			$hashcash->setVersion(1);
			$hashcash->setBits(10);
			$hashcash->setResource('thefox');
			$hashcash->mint();
			$db->addHashcash($hashcash);
		}
		
		$this->assertTrue($db->save() > 0);
		$this->assertFileExists('./test_hashcashs1.yml');
	}
	
	/**
	 * @depends testSave1
	 */
	public function testLoad1(){
		$db = new HashcashDb('./test_hashcashs1.yml');
		$this->assertTrue($db->load());
		$this->assertEquals(1000, count($db->getHashcashs()));
	}
	
	public function testSave2(){
		#fwrite(STDOUT, __METHOD__.''."\n");
		$db = new HashcashDb('./test_hashcashs2.yml');
		
		#$ts = mktime(0, 0, 0, date('d'), date('m'), date('Y'));
		$ts = time();
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT, $ts - 3600 * 24 * 20));
		$hashcash->setResource('thefox');
		$hashcash->mint();
		$db->addHashcash($hashcash);
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT, $ts - 3600 * 24 * 90));
		$hashcash->setResource('thefox');
		$hashcash->mint();
		$db->addHashcash($hashcash);
		
		$this->assertTrue($db->save() > 0);
		$this->assertFileExists('./test_hashcashs2.yml');
	}
	
	/**
	 * @depends testSave2
	 */
	public function testLoad2(){
		$db = new HashcashDb('./test_hashcashs2.yml');
		$this->assertTrue($db->load());
		$this->assertEquals(1, count($db->getHashcashs()));
		
		$db->setDataChanged(true);
		#$db->save();
	}
	
}
