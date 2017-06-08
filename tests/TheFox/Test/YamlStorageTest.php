<?php

namespace TheFox\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TheFox\Storage\YamlStorage;

class YamlStorageTest extends TestCase
{
    public function testBasic()
    {
        $storage = new YamlStorage();

        $this->assertTrue(is_object($storage));
    }

    public function testSave()
    {
        $basePath = realpath(dirname(__FILE__).'/../../..');
        $path = $basePath.'/test_data/test1.yml';

        $storage = new YamlStorage($path);
        $storage->data['test'] = ['test1' => 123, 'test2' => 'test3'];

        $this->assertFalse($storage->getDataChanged());

        $storage->setDataChanged();
        $this->assertTrue($storage->getDataChanged());

        $storage->save();

        $finder = new Finder();
        $files = $finder
            //->path($basePath)
            ->in($basePath.'/test_data')
            ->name('test1.yml');
        $this->assertEquals(1, count($files));
    }

    public function testLoad1()
    {
        $basePath = realpath(dirname(__FILE__).'/../../..');
        $path = $basePath.'/test_data/test1.yml';

        $storage = new YamlStorage($path);
        $storage->setDataChanged();
        $storage->save();

        $storage = new YamlStorage($path);
        $storage->load();

        $this->assertTrue($storage->isLoaded());
    }

    public function testLoad2()
    {
        $basePath = realpath(dirname(__FILE__).'/../../..');
        $path = $basePath.'/test_data/test2.yml';

        $storage = new YamlStorage($path);
        $storage->load();

        $this->assertFalse($storage->isLoaded());
    }

    public function testIsLoaded()
    {
        $storage = new YamlStorage();
        $this->assertFalse($storage->isLoaded());

        $storage->isLoaded(true);
        $this->assertTrue($storage->isLoaded());

        $storage->isLoaded(false);
        $this->assertFalse($storage->isLoaded());
    }

    public function testSetDatadirBasePath()
    {
        $storage = new YamlStorage();
        $this->assertEquals('', $storage->getDatadirBasePath());

        $storage->setDatadirBasePath('test_data');

        $this->assertEquals('test_data', $storage->getDatadirBasePath());
    }
}
