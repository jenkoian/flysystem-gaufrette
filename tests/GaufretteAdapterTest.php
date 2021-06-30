<?php

namespace Jenko\Flysystem\Tests;

use Gaufrette\Adapter;
use Jenko\Flysystem\GaufretteAdapter;
use Jenko\Flysystem\UnsupportedAdapterMethodException;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToWriteFile;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GaufretteAdapterTest extends TestCase
{
    /**
     * @var GaufretteAdapter|null
     */
    private $gaufrette;

    /**
     * @var Adapter|MockObject|null
     */
    private $gaufretteMock;

    /**
     * @var Config|null
     */
    private $config;

    protected function setUp(): void
    {
        $this->gaufretteMock = $this->createMock(Adapter::class);
        $this->gaufrette = new GaufretteAdapter($this->gaufretteMock);
        $this->config = new Config([]);
    }

    public function testWrite(): void
    {
        $this->gaufretteMock->expects($this->once())->method('write')->willReturn(123);
        $this->gaufrette->write('filename', 'foo', $this->config);
    }

    public function testWriteWillThrowExceptionIfNotWritten(): void
    {
        $this->gaufretteMock->expects($this->once())->method('write')->willReturn(false);
        $this->expectException(UnableToWriteFile::class);
        $this->gaufrette->write('filename', 'foo', $this->config);
    }

    public function testWriteStream(): void
    {
        $expected = ['type' => 'file', 'contents' => '', 'size' => 123, 'path' => 'filename'];
        $this->gaufretteMock->expects($this->once())->method('write')->willReturn(123);
        $this->gaufrette->writeStream('filename', tmpfile(), $this->config);
    }

    public function testWriteStreamWillThrowExceptionIfNotWritten(): void
    {
        $this->gaufretteMock->expects($this->once())->method('write')->willReturn(false);
        $this->expectException(UnableToWriteFile::class);
        $this->gaufrette->writeStream('filename', tmpfile(), $this->config);
    }

    public function testMove(): void
    {
        $this->gaufretteMock->expects($this->once())->method('rename')->willReturn(true);
        $this->gaufrette->move('filename', 'newfilename', $this->config);
    }

    public function testMoveWillThrowExceptionIfNotMoved(): void
    {
        $this->gaufretteMock->expects($this->once())->method('rename')->willReturn(false);
        $this->expectException(UnableToMoveFile::class);
        $this->gaufrette->move('filename', 'newfilename', $this->config);
    }

    public function testCopy(): void
    {
        $this->expectException(UnsupportedAdapterMethodException::class);
        $this->gaufrette->copy('filename', 'newfilename', $this->config);
    }

    public function testDelete(): void
    {
        $this->gaufretteMock->expects($this->once())->method('delete')->willReturn(true);
        $this->gaufrette->delete('filename');
    }

    public function testDeleteDirectory(): void
    {
        $this->gaufretteMock->expects($this->once())->method('delete')->willReturn(true);
        $this->gaufretteMock->expects($this->once())->method('isDirectory')->willReturn(true);
        $this->gaufrette->deleteDirectory('directoryName');
    }

    public function testDeleteDirectoryDoesNotCallDeleteIfNotDirectory(): void
    {
        $this->gaufretteMock->expects($this->never())->method('delete');
        $this->gaufretteMock->expects($this->once())->method('isDirectory')->willReturn(false);

        $this->gaufrette->deleteDirectory('directoryName');
    }

    public function testDeleteDirectoryThrowsExceptionIfNotDeleted(): void
    {
        $this->gaufretteMock->expects($this->once())->method('delete')->willReturn(false);
        $this->gaufretteMock->expects($this->once())->method('isDirectory')->willReturn(true);

        $this->expectException(UnableToDeleteDirectory::class);

        $this->gaufrette->deleteDirectory('directoryName');
    }

    public function testCreateDirectory(): void
    {
        $this->expectException(UnsupportedAdapterMethodException::class);
        $this->gaufrette->createDirectory('directoryName', $this->config);
    }

    public function testSetVisibility(): void
    {
        $this->expectException(UnsupportedAdapterMethodException::class);
        $this->gaufrette->setVisibility('filename', 'visible');
    }

    public function testFileExists(): void
    {
        $this->gaufretteMock->expects($this->once())->method('exists')->willReturn(true);
        $this->assertEquals(true, $this->gaufrette->fileExists('filename'));
    }

    public function testRead(): void
    {
        $this->gaufretteMock->expects($this->once())->method('read')->willReturn('foo');
        
        $data = $this->gaufrette->read('filename');
        $this->assertEquals('foo', $data);
    }

    public function testReadStream(): void
    {
        $stream = tmpfile();
        fwrite($stream, 'foo');

        $this->gaufretteMock->expects($this->once())->method('read')->willReturn('foo');

        $data = $this->gaufrette->readStream('filename');
        $this->assertEquals('foo', stream_get_contents($data));
    }

    public function testListContents(): void
    {
        $keys = ['foo', 'bar', 'baz'];
        $this->gaufretteMock->expects($this->exactly(2))->method('keys')->willReturn($keys);

        // We don't use the second param ($deep) but check for both anyhow.
        $this->assertEquals($keys, $this->gaufrette->listContents('directoryName', false));
        $this->assertEquals($keys, $this->gaufrette->listContents('directoryName', true));
    }

    public function testFileSize(): void
    {
        $gaufretteSizeMock = $this->createMock(GaufretteSizeCalculator::class);
        $gaufretteSizeMock->expects($this->once())->method('size')->willReturn(100);

        $gaufrette = new GaufretteAdapter($gaufretteSizeMock);
        $this->assertEquals(new FileAttributes('filename', 100), $gaufrette->fileSize('filename'));
    }

    public function testFileSizeThrowsExceptionIfAdapterUnsupported(): void
    {
        $this->expectException(UnsupportedAdapterMethodException::class);
        $this->gaufrette->fileSize('filename');
    }

    public function testMimeType(): void
    {
        $gaufretteMimetypeMock = $this->createMock(GaufretteMimeTypeProvider::class);
        $gaufretteMimetypeMock->expects($this->once())->method('mimeType')->willReturn('application/pdf');

        $gaufrette = new GaufretteAdapter($gaufretteMimetypeMock);
        $this->assertEquals(new FileAttributes('filename', null, null, null, 'application/pdf'), $gaufrette->mimeType('filename'));
    }

    public function testMimeTypeThrowsExceptionIfAdapterUnsupported(): void
    {
        $this->expectException(UnsupportedAdapterMethodException::class);
        $this->gaufrette->mimeType('filename');
    }

    public function testLastModified(): void
    {
        $this->gaufretteMock->expects($this->once())->method('mtime')->willReturn(1234567890);
        $this->assertEquals(new FileAttributes('filename', null, null, 1234567890), $this->gaufrette->lastModified('filename'));
    }

    public function testVisiblity(): void
    {
        $this->expectException(UnsupportedAdapterMethodException::class);
        $this->gaufrette->visibility('filename');
    }
}

interface GaufretteSizeCalculator extends \Gaufrette\Adapter, \Gaufrette\Adapter\SizeCalculator
{

}

interface GaufretteMimeTypeProvider extends \Gaufrette\Adapter, \Gaufrette\Adapter\MimeTypeProvider
{

}
