<?php

class GaufretteAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jenko\Flysystem\GaufretteAdapter
     */
    private $gaufrette;

    /**
     * @var Gaufrette\Adapter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $gaufretteMock;

    /**
     * @var \League\Flysystem\Config
     */
    private $config;

    protected function setUp()
    {
        $this->gaufretteMock = $this->getMock('Gaufrette\Adapter');
        $this->gaufrette = new \Jenko\Flysystem\GaufretteAdapter($this->gaufretteMock);
        $this->config = new \League\Flysystem\Config([]);
    }

    public function testWrite()
    {
        $this->gaufretteMock->expects($this->once())->method('write')->willReturn(true);
        $this->assertEquals(true, $this->gaufrette->write('filename', 'foo', $this->config));
    }

    public function testWriteStream()
    {
        $this->gaufretteMock->expects($this->once())->method('write')->willReturn(true);
        $this->assertEquals(true, $this->gaufrette->writeStream('filename', tmpfile(), $this->config));
    }

    /**
     * @expectedException Jenko\Flysystem\UnsupportedAdapterMethodException
     */
    public function testUpdate()
    {
        $this->gaufrette->update('filename', 'foo', $this->config);
    }

    /**
     * @expectedException Jenko\Flysystem\UnsupportedAdapterMethodException
     */
    public function testUpdateStream()
    {
        $this->gaufrette->updateStream('filename', tmpfile(), $this->config);
    }

    public function testRename()
    {
        $this->gaufretteMock->expects($this->once())->method('rename')->willReturn(true);
        $this->assertEquals(true, $this->gaufrette->rename('filename', 'newfilename'));
    }

    /**
     * @expectedException Jenko\Flysystem\UnsupportedAdapterMethodException
     */
    public function testCopy()
    {
        $this->gaufrette->copy('filename', 'newfilename');
    }

    public function testDelete()
    {
        $this->gaufretteMock->expects($this->once())->method('delete')->willReturn(true);
        $this->assertEquals(true, $this->gaufrette->delete('filename'));
    }

    public function testDeleteDir()
    {
        $this->gaufretteMock->expects($this->once())->method('delete')->willReturn(true);
        $this->gaufretteMock->expects($this->once())->method('isDirectory')->willReturn(true);
        $this->assertEquals(true, $this->gaufrette->deleteDir('directoryName'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteDirThrowsExceptionIfNonDirectory()
    {
        $this->gaufretteMock->expects($this->never())->method('delete');
        $this->gaufretteMock->expects($this->once())->method('isDirectory')->willReturn(false);

        $this->gaufrette->deleteDir('directoryName');
    }

    /**
     * @expectedException Jenko\Flysystem\UnsupportedAdapterMethodException
     */
    public function testCreateDir()
    {
        $this->gaufrette->createDir('directoryName', $this->config);
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetVisibility()
    {
        $this->gaufrette->setVisibility('filename', 'visible');
    }

    public function testHas()
    {
        $this->gaufretteMock->expects($this->once())->method('exists')->willReturn(true);
        $this->assertEquals(true, $this->gaufrette->has('filename'));
    }

    public function testRead()
    {
        $this->gaufretteMock->expects($this->once())->method('read')->willReturn('foo');
        
        $data = $this->gaufrette->read('filename');
        $this->assertEquals('foo', $data['contents']);
    }

    public function testReadStream()
    {
        $stream = tmpfile();
        fwrite($stream, 'foo');

        $this->gaufretteMock->expects($this->once())->method('read')->willReturn('foo');

        $data = $this->gaufrette->readStream('filename');
        $this->assertEquals('foo', stream_get_contents($data['stream']));
    }

    public function testListContents()
    {
        $keys = ['foo', 'bar', 'baz'];
        $this->gaufretteMock->expects($this->once())->method('keys')->willReturn($keys);
        $this->assertEquals($keys, $this->gaufrette->listContents('directoryName'));
    }

    public function testGetMetadata()
    {
        $metadata = ['isDir' => true, 'bar' => 'baz'];
        $gaufretteMetadataSupporterMock = $this->getMock('GaufretteMetadataSupporter');
        $gaufretteMetadataSupporterMock->expects($this->once())->method('getMetadata')->willReturn($metadata);

        $gaufrette = new \Jenko\Flysystem\GaufretteAdapter($gaufretteMetadataSupporterMock);
        $this->assertEquals($metadata, $gaufrette->getMetadata('filename'));
    }

    /**
     * @expectedException Jenko\Flysystem\UnsupportedAdapterMethodException
     */
    public function testGetMetadataThrowsExceptionIfAdapterUnsupported()
    {
        $this->gaufrette->getMetadata('filename');
    }

    public function testGetSize()
    {
        $gaufretteSizeMock = $this->getMock('GaufretteSizeCalculator');
        $gaufretteSizeMock->expects($this->once())->method('size')->willReturn(100);

        $gaufrette = new \Jenko\Flysystem\GaufretteAdapter($gaufretteSizeMock);
        $this->assertEquals(100, $gaufrette->getSize('filename'));
    }

    /**
     * @expectedException Jenko\Flysystem\UnsupportedAdapterMethodException
     */
    public function testGetSizeThrowsExceptionIfAdapterUnsupported()
    {
        $this->gaufrette->getSize('filename');
    }

    public function testGetMimetype()
    {
        $gaufretteMimetypeMock = $this->getMock('GaufretteMimeTypeProvider');
        $gaufretteMimetypeMock->expects($this->once())->method('mimeType')->willReturn('application/pdf');

        $gaufrette = new \Jenko\Flysystem\GaufretteAdapter($gaufretteMimetypeMock);
        $this->assertEquals('application/pdf', $gaufrette->getMimetype('filename'));
    }

    /**
     * @expectedException Jenko\Flysystem\UnsupportedAdapterMethodException
     */
    public function testGetMimetypeThrowsExceptionIfAdapterUnsupported()
    {
        $this->gaufrette->getMimetype('filename');
    }

    public function testGetTimestamp()
    {
        $this->gaufretteMock->expects($this->once())->method('mtime')->willReturn(1234567890);
        $this->assertEquals(1234567890, $this->gaufrette->getTimestamp('filename'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetVisiblity()
    {
        $this->gaufrette->getVisibility('filename');
    }
}

interface GaufretteMetadataSupporter extends \Gaufrette\Adapter, \Gaufrette\Adapter\MetadataSupporter
{

}

interface GaufretteSizeCalculator extends \Gaufrette\Adapter, \Gaufrette\Adapter\SizeCalculator
{

}

interface GaufretteMimeTypeProvider extends \Gaufrette\Adapter, \Gaufrette\Adapter\MimeTypeProvider
{

}
