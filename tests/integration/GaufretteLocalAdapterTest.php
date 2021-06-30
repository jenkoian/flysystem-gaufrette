<?php

namespace Jenko\Flysystem\Tests\Integration;

use Gaufrette\Adapter\Flysystem;
use Gaufrette\Adapter\Local;
use Jenko\Flysystem\GaufretteAdapter;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class GaufretteLocalAdapterTest extends TestCase
{
    private $local;

    protected function setUp(): void
    {
        $this->local = new GaufretteAdapter(
            new Local(__DIR__ . '/resources')
        );
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob(__DIR__ . '/resources/*'));
    }

    public function testLocalAdapter(): void
    {
        $filesystem = new Filesystem($this->local);

        $filesystem->write('test.txt', 'foo', []);
        $data = $filesystem->read('test.txt');

        $this->assertEquals('foo', $data);
    }
    
    public function testHadouken(): void
    {
        $local = new GaufretteAdapter(
            new Flysystem(
                new GaufretteAdapter(
                    new Flysystem(
                        new GaufretteAdapter(
                            new Flysystem(
                                new GaufretteAdapter(
                                    new Local(
                                        __DIR__ . '/resources'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $filesystem = new Filesystem($local);

        $written = $filesystem->write('test.txt', 'foo', []);
        $this->assertTrue($written);

        $data = $filesystem->read('test.txt');

        $this->assertEquals('foo', $data);
    }
}
