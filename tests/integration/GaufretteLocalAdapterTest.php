<?php

/**
 * @group integration
 */
class GaufretteLocalAdapterTest extends PHPUnit_Framework_TestCase
{
    private $local;

    protected function setUp()
    {
        $this->local = new \Jenko\Flysystem\GaufretteAdapter(
            new \Gaufrette\Adapter\Local(__DIR__ . '/resources')
        );
    }

    protected function tearDown()
    {
        array_map('unlink', glob(__DIR__ . '/resources/*'));
    }

    public function testLocalAdapter()
    {
        $filesystem = new \League\Flysystem\Filesystem($this->local);

        $written = $filesystem->write('test.txt', 'foo');
        $this->assertTrue($written);

        $data = $filesystem->read('test.txt');

        $this->assertEquals('foo', $data);
    }
    
    public function testHadouken()
    {
        $local = new \Jenko\Flysystem\GaufretteAdapter(
            new \Gaufrette\Adapter\Flysystem(
                new \Jenko\Flysystem\GaufretteAdapter(
                    new \Gaufrette\Adapter\Flysystem(
                        new \Jenko\Flysystem\GaufretteAdapter(
                            new \Gaufrette\Adapter\Flysystem(
                                new \Jenko\Flysystem\GaufretteAdapter(
                                    new \Gaufrette\Adapter\Local(
                                        __DIR__ . '/resources'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $filesystem = new \League\Flysystem\Filesystem($local);

        $written = $filesystem->write('test.txt', 'foo');
        $this->assertTrue($written);

        $data = $filesystem->read('test.txt');

        $this->assertEquals('foo', $data);
    }
}
