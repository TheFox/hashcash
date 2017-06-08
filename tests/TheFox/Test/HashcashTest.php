<?php

namespace TheFox\Test;

use PHPUnit\Framework\TestCase;
use TheFox\Pow\Hashcash;

class HashcashTest extends TestCase
{
    public function testBasic()
    {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $this->assertTrue(in_array('sha1', hash_algos()), 'sha1 algorithm not found.');
        
        $time = mktime(10, 2, 0, 2, 26, 1987);
        $this->assertEquals('870226', date(Hashcash::DATE_FORMAT, $time));
        
        $time = mktime(9, 59, 24, 2, 24, 1987);
        $this->assertEquals('8702240959', date(Hashcash::DATE_FORMAT10, $time));
        
        $time = mktime(9, 58, 24, 2, 21, 1987);
        $this->assertEquals('870221095824', date(Hashcash::DATE_FORMAT12, $time));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 1
     */
    public function testSetVersionRuntimeException1()
    {
        $hashcash = new Hashcash();
        $hashcash->setVersion(0);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 2
     */
    public function testSetVersionRuntimeException2()
    {
        $hashcash = new Hashcash();
        $hashcash->setVersion(9999);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetDateInvalidArgumentException()
    {
        $hashcash = new Hashcash();
        $hashcash->setDate('20140422');
    }

    public function testSetAttempts()
    {
        $hashcash = new Hashcash();
        $this->assertEquals(0, $hashcash->getAttempts());

        $hashcash->setAttempts(10);
        $this->assertEquals(10, $hashcash->getAttempts());
    }

    public function testGetHash()
    {
        $hashcash = new Hashcash();
        $this->assertEquals('', $hashcash->getHash());

        $hashcash->setHash('xyz');
        $this->assertEquals('xyz', $hashcash->getHash());
    }

    public function testGetStamp()
    {
        $hashcash = new Hashcash();
        $this->assertEquals('1:20:' . date(Hashcash::DATE_FORMAT) . '::::', $hashcash->getStamp());
    }

    public function testSetGet1()
    {
        //$this->assertTrue(true); return;

        $hashcash = new Hashcash(5, 'test1');
        $this->assertEquals(5, $hashcash->getBits());
        $this->assertEquals('test1', $hashcash->getResource());
    }

    public function testSetGet2()
    {
        //$this->assertTrue(true); return;

        $hashcash = new Hashcash(5, 'test1');
        $this->assertEquals(1, $hashcash->getVersion());
        $this->assertEquals(5, $hashcash->getBits());
        $this->assertEquals(date('ymd'), $hashcash->getDate());

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(5);
        $hashcash->setDate('140422');
        $hashcash->setResource('mint1');
        $hashcash->setExtension('ext1');
        $hashcash->setSalt('salt1');
        $hashcash->setSuffix('suffix1');

        $this->assertEquals(1, $hashcash->getVersion());
        $this->assertEquals(5, $hashcash->getBits());
        $this->assertEquals('140422', $hashcash->getDate());
        $this->assertEquals('mint1', $hashcash->getResource());
        $this->assertEquals('ext1', $hashcash->getExtension());
        $this->assertEquals('salt1', $hashcash->getSalt());
        $this->assertEquals('suffix1', $hashcash->getSuffix());
    }

    /**
     * @group large
     */
    public function testMint1()
    {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        //$this->assertTrue(true); return;

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(5);
        $hashcash->setDate('140422');
        $hashcash->setResource('mint2');
        $hashcash->setSalt('0000000c4c51ffcfc37b523');
        $this->assertEquals('1:5:140422:mint2::0000000c4c51ffcfc37b523:3', $hashcash->mint());

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(7);
        $hashcash->setDate('140422');
        $hashcash->setResource('mint2');
        $hashcash->setExtension('ext2');
        $hashcash->setSalt('salt2');
        $this->assertEquals('1:7:140422:mint2:ext2:salt2:13', $hashcash->mint());

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(6);
        $hashcash->setDate('870221');
        $hashcash->setResource('thefox');
        $hashcash->setSalt('SNIrgHNPdcH3NNu+0CcG8g==');
        $this->assertEquals('1:6:870221:thefox::SNIrgHNPdcH3NNu+0CcG8g==:45', $hashcash->mint());

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(6);
        $hashcash->setDate('8702210958');
        $hashcash->setResource('thefox');
        $hashcash->setSalt('sPc4d5G2UZpTuTmyfOy6IA==');
        $this->assertEquals('1:6:8702210958:thefox::sPc4d5G2UZpTuTmyfOy6IA==:13', $hashcash->mint());

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(5);
        $hashcash->setDate('870221095824');
        $hashcash->setResource('thefox');
        $hashcash->setSalt('lN4IGUU6R5FH27OhM+DGkw==');
        $this->assertEquals('1:5:870221095824:thefox::lN4IGUU6R5FH27OhM+DGkw==:14', $hashcash->mint());

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(5);
        $hashcash->setDate('140401');
        $hashcash->setResource('thefox');
        $hashcash->setSalt('GeUosqsUPpxts37XWLeWdg==');
        $this->assertEquals('1:5:140401:thefox::GeUosqsUPpxts37XWLeWdg==:1', $hashcash->mint());

        $hashcash = new Hashcash();
        $hashcash->setVersion(1);
        $hashcash->setBits(5);
        $hashcash->setDate('140325');
        $hashcash->setResource('thefox');
        $hashcash->setSalt('Ifr62IiXO9YHQ2tXyqSOUQ==');
        $this->assertEquals('1:5:140325:thefox::Ifr62IiXO9YHQ2tXyqSOUQ==:5', $hashcash->mint());
        $this->assertEquals('01d106345750ab94a8d80e1c0dbe0da3662d476e', $hashcash->getHash());
    }

    public function testMintAll()
    {
        $hashcash = new Hashcash(11);
        $hashcash->setDate('141119');

        $stamps = $hashcash->mintAll();
        $this->assertEquals([
            '1:11:141119::::656',
            '1:11:141119::::1580',
        ], $stamps);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testMintAttemptsMaxRuntimeException()
    {
        $hashcash = new Hashcash(5, 'example@example.com');
        $hashcash->setDate('140427');
        $hashcash->setSalt('axfcrlV1hxLvF6J9BeDiLw==');
        $hashcash->setMintAttemptsMax(1);
        $hashcash->mint();
    }

    public function testVerify()
    {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        //$this->assertTrue(true); return;

        $hashcash = new Hashcash();
        $hashcash->setExpiration(0);

        $this->assertTrue($hashcash->verify('1:20:140422:mint2::ArrRIabEj3nZrOcM:0000000000007u1E'));
        $this->assertTrue($hashcash->verify('1:24:140422:mint2:ext1:Nde2ffWsRoe3DXVQ:00000001M+iu'));
        $this->assertTrue($hashcash->verify('1:20:140422:mint2:ext2:salt2:256507'));
        $this->assertTrue($hashcash->verify('1:28:140422:::s15xXleWocBKSA95Zw4e1Q==:245861178'));
        $this->assertTrue($hashcash->verify('1:21:870221:thefox::2B6kv/rFiCdJRzqhH7P2eA==:995214'));

        $this->assertFalse($hashcash->verify('1:20:140422:mint3::ArrRIabEj3nZrOcM:0000000000007u1E'));

        $hashcash->setExpiration(3600 * 24 * 365);
        $this->assertFalse($hashcash->verify('1:21:870221:thefox::2B6kv/rFiCdJRzqhH7P2eA==:995214'));

        $hashcash1 = new Hashcash();
        $hashcash1->setBits(10);

        $hashcash2 = new Hashcash();
        $this->assertTrue($hashcash2->verify($hashcash1->mint()));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1
     */
    public function testVerifyInvalidArgumentException1()
    {
        //$this->assertTrue(true);

        $hashcash = new Hashcash();
        $hashcash->verify('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 2
     */
    public function testVerifyInvalidArgumentException2()
    {
        //$this->assertTrue(true);

        $hashcash = new Hashcash();
        $hashcash->verify('1:20:140422:mint2:ext2:22060');
    }
}
