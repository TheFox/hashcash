<?php

namespace TheFox\Test;

use PHPUnit_Framework_TestCase;

use TheFox\Pow\Hashcash;
use TheFox\Pow\HashcashDb;

class HashcashDbTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @group large
	 */
	public function testSave1(){
		#fwrite(STDOUT, __METHOD__.''."\n");
		$db = new HashcashDb('test_data/test_hashcashs1.yml');
		
		for($i = 0; $i < 1000; $i++){
			$hashcash = new Hashcash();
			$hashcash->setVersion(1);
			$hashcash->setBits(5);
			$hashcash->setResource('thefox');
			$hashcash->mint();
			$db->addHashcash($hashcash);
		}
		
		$this->assertTrue($db->save() > 0);
		$this->assertFileExists('test_data/test_hashcashs1.yml');
	}
	
	/**
	 * @group large
	 * @depends testSave1
	 */
	public function testLoad1(){
		$db = new HashcashDb('test_data/test_hashcashs1.yml');
		$this->assertTrue($db->load());
		$this->assertEquals(1000, count($db->getHashcashs()));
	}
	
	public function testSave2(){
		#fwrite(STDOUT, __METHOD__.''."\n");
		$db = new HashcashDb('test_data/test_hashcashs2.yml');
		
		#$ts = mktime(0, 0, 0, date('d'), date('m'), date('Y'));
		$ts = time();
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT, $ts - 3600 * 24 * 20));
		$hashcash->setResource('thefox');
		$hashcash->mint();
		$db->addHashcash($hashcash);
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT, $ts - 3600 * 24 * 90));
		$hashcash->setResource('thefox');
		$hashcash->mint();
		$db->addHashcash($hashcash);
		
		$this->assertTrue($db->save() > 0);
		$this->assertFileExists('test_data/test_hashcashs2.yml');
	}
	
	/**
	 * @depends testSave2
	 */
	public function testLoad2(){
		$db = new HashcashDb('test_data/test_hashcashs2.yml');
		$this->assertTrue($db->load());
		$this->assertEquals(1, count($db->getHashcashs()));
		
		$db->setDataChanged(true);
		#$db->save();
	}
	
	public function testSave3(){
		#fwrite(STDOUT, __METHOD__.''."\n");
		$db = new HashcashDb('test_data/test_hashcashs3.yml');
		
		$ts = time();
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setResource('res1');
		$hashcash->mint();
		$this->assertTrue($hashcash->verify());
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT, $ts - 3600 * 24 * 90));
		$hashcash->setResource('res2');
		$hashcash->mint();
		$this->assertFalse($hashcash->verify());
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT, $ts - 3600 * 24 * 90));
		$hashcash->setResource('res3');
		$hashcash->setExpiration(3600 * 24 * 120);
		$hashcash->mint();
		$this->assertTrue($hashcash->verify());
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT, $ts - 3600 * 24 * 10));
		$hashcash->setResource('res4');
		$hashcash->mint();
		$this->assertTrue($hashcash->verify());
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setDate(date('ymdHis', $ts - 60));
		$hashcash->setResource('res5');
		$hashcash->setExpiration(30);
		$hashcash->mint();
		$this->assertFalse($hashcash->verify());
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setDate(date(Hashcash::DATE_FORMAT12, $ts - 60));
		$hashcash->setResource('res6');
		$hashcash->mint();
		$this->assertTrue($hashcash->verify());
		$this->assertTrue($db->addHashcash($hashcash));
		
		#fwrite(STDOUT, __METHOD__.': save '.$db->save()."\n");
		$this->assertTrue($db->save() > 0);
		$this->assertFileExists('test_data/test_hashcashs3.yml');
	}
	
	public function testLoad4(){
		$db = new HashcashDb('test_data/test_hashcashs4.yml');
		$this->assertFalse($db->load());
	}
	
	public function testDoublespend1(){
		#fwrite(STDOUT, __METHOD__."\n");
		$db = new HashcashDb();
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setResource('thefox');
		$hashcash->setSuffix(1);
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
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
		$hashcash->setBits(5);
		$hashcash->setResource('thefox');
		$hashcash->setSuffix(1);
		$this->assertTrue($db->addHashcash($hashcash));
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(5);
		$hashcash->setResource('thefox');
		$hashcash->setSuffix(1);
		$this->assertFalse($db->addHashcash($hashcash));
		
		$this->assertEquals(1, count($db->getHashcashs()));
	}
	
}
