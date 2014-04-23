<?php

use TheFox\Hashcash\Hashcash;

class HashcashTest extends PHPUnit_Framework_TestCase{
	
	#protected static $cronjob = null;
	
	public static function setUpBeforeClass(){
		#fwrite(STDOUT, __METHOD__.''."\n");
		#$this->assertTrue(true);
		#$this->assertFalse(false);
		#$this->assertEquals();
	}
	
	public function testBasic(){
		fwrite(STDOUT, __METHOD__.''."\n");
		#$this->markTestIncomplete('This test has not been implemented yet.');
		
		$this->assertTrue(in_array('sha1', hash_algos()), 'sha1 algorithm not found.');
	}
	
	public function testSetGet(){
		#$this->assertTrue(true); return;
		
		$hashcash = new Hashcash(21, 'test1');
		$this->assertEquals(1, $hashcash->getVersion());
		$this->assertEquals(21, $hashcash->getBits());
		$this->assertEquals(date('ymd'), $hashcash->getDate());
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(21);
		$hashcash->setDate('140422');
		$hashcash->setResource('mint1');
		$hashcash->setExtension('ext1');
		$hashcash->setSalt('salt1');
		$hashcash->setSuffix('suffix1');
		
		$this->assertEquals(1, $hashcash->getVersion());
		$this->assertEquals(21, $hashcash->getBits());
		$this->assertEquals('140422', $hashcash->getDate());
		$this->assertEquals('mint1', $hashcash->getResource());
		$this->assertEquals('ext1', $hashcash->getExtension());
		$this->assertEquals('salt1', $hashcash->getSalt());
		$this->assertEquals('suffix1', $hashcash->getSuffix());
	}
	
	public function testMint1(){
		#$this->markTestIncomplete('This test has not been implemented yet.');
		#$this->assertTrue(true); return;
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setDate('140422');
		$hashcash->setResource('mint2');
		$hashcash->setSalt('0000000c4c51ffcfc37b523');
		$this->assertEquals('1:10:140422:mint2::0000000c4c51ffcfc37b523:977', $hashcash->mint());
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(20);
		$hashcash->setDate('140422');
		$hashcash->setResource('mint2');
		$hashcash->setExtension('ext2');
		$hashcash->setSalt('salt2');
		$this->assertEquals('1:20:140422:mint2:ext2:salt2:22060', $hashcash->mint());
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(21);
		$hashcash->setDate('870221');
		$hashcash->setResource('thefox');
		$hashcash->setSalt('2B6kv/rFiCdJRzqhH7P2eA==');
		$this->assertEquals('1:21:870221:thefox::2B6kv/rFiCdJRzqhH7P2eA==:532358', $hashcash->mint());
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setDate('8702210958');
		$hashcash->setResource('thefox');
		$hashcash->setSalt('2B6kv/rFiCdJRzqhH7P2eA==');
		$this->assertEquals('1:10:8702210958:thefox::2B6kv/rFiCdJRzqhH7P2eA==:721', $hashcash->mint());
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setDate('870221095824');
		$hashcash->setResource('thefox');
		$hashcash->setSalt('2B6kv/rFiCdJRzqhH7P2eA==');
		$this->assertEquals('1:10:870221095824:thefox::2B6kv/rFiCdJRzqhH7P2eA==:47', $hashcash->mint());
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setDate('140401');
		$hashcash->setResource('thefox');
		$hashcash->setSalt('2B6kv/rFiCdJRzqhH7P2eA==');
		$this->assertEquals('1:10:140401:thefox::2B6kv/rFiCdJRzqhH7P2eA==:293', $hashcash->mint());
		
		$hashcash = new Hashcash();
		$hashcash->setVersion(1);
		$hashcash->setBits(10);
		$hashcash->setDate('140325');
		$hashcash->setResource('thefox');
		$hashcash->setSalt('2B6kv/rFiCdJRzqhH7P2eA==');
		$this->assertEquals('1:10:140325:thefox::2B6kv/rFiCdJRzqhH7P2eA==:129', $hashcash->mint());
	}
	
	/*public function testMint2(){
		$this->assertTrue(true);
		
		$hashcash = new Hashcash(10, 'example@example.com');
		fwrite(STDOUT, __METHOD__.' mint: '.$hashcash->mint()."\n");
	}*/
	
	public function testVerify(){
		#$this->markTestIncomplete('This test has not been implemented yet.');
		$this->assertTrue(true); return;
		
		$hashcash = new Hashcash();
		$hashcash->setExpiration(0);
		$this->assertTrue(  $hashcash->verify('1:20:140422:mint2::ArrRIabEj3nZrOcM:0000000000007u1E') );
		$this->assertTrue(  $hashcash->verify('1:24:140422:mint2:ext1:Nde2ffWsRoe3DXVQ:00000001M+iu') );
		$this->assertTrue(  $hashcash->verify('1:20:140422:mint2:ext2:salt2:22060') );
		$this->assertTrue(  $hashcash->verify('1:28:140422:::s15xXleWocBKSA95Zw4e1Q==:245861178') );
		$this->assertFalse( $hashcash->verify('1:20:140422:mint3::ArrRIabEj3nZrOcM:0000000000007u1E') );
		$this->assertTrue(  $hashcash->verify('1:21:870221:thefox::2B6kv/rFiCdJRzqhH7P2eA==:532358') );
		
		$hashcash->setExpiration(3600 * 24 * 365);
		$this->assertFalse( $hashcash->verify('1:21:870221:thefox::2B6kv/rFiCdJRzqhH7P2eA==:532358') );
		
		
		$hashcash1 = new Hashcash();
		$hashcash1->setBits(10);
		
		$hashcash2 = new Hashcash();
		$this->assertTrue($hashcash2->verify($hashcash1->mint()));
	}
	
	/**
	 * @expectedException RuntimeException
	 */
	public function testSetVersionRuntimeException(){
		$hashcash = new Hashcash();
		$hashcash->setVersion(0);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetVersionInvalidArgumentException(){
		$hashcash = new Hashcash();
		$hashcash->setDate('20140422');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1
	 */
	public function testVerifyInvalidArgumentException1(){
		#$this->assertTrue(true);
		
		$hashcash = new Hashcash();
		$hashcash->verify('');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 2
	 */
	public function testVerifyInvalidArgumentException2(){
		$hashcash = new Hashcash();
		$hashcash->verify('1:20:140422:mint2:ext2:22060');
	}
	
}
