<?php

namespace Jenko\Flysystem;

use Gaufrette\Adapter;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;

class GaufretteAdapter implements FilesystemAdapter
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $path, string $contents, Config $config): void
    {
        error_clear_last();

        $result = $this->adapter->write($path, $contents);

        if ($result === false) {
            throw UnableToWriteFile::atLocation($path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        error_clear_last();

        $contents = stream_get_contents($contents);
        $result = $this->adapter->write($path, $contents);

        if ($result === false) {
            throw UnableToWriteFile::atLocation($path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function move(string $source, string $destination, Config $config): void
    {
        $result = $this->adapter->rename($source, $destination);

        if ($result === false) {
            throw UnableToMoveFile::fromLocationTo($source, $destination);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        throw new UnsupportedAdapterMethodException('copy is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $path): void
    {
        error_clear_last();

        if ( ! $this->adapter->delete($path)) {
            throw UnableToDeleteFile::atLocation($path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory(string $path): void
    {
        if ( ! $this->adapter->isDirectory($path)) {
            return;
        }

        error_clear_last();

        if ( ! $this->adapter->delete($path)) {
            throw UnableToDeleteDirectory::atLocation($path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory(string $path, Config $config): void
    {
        throw new UnsupportedAdapterMethodException('createDirectory is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists(string $path): bool
    {
        return $this->adapter->exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $path): string
    {
        $contents = $this->adapter->read($path);

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, error_get_last()['message'] ?? '');
        }

        return $contents;
    }

    public function readStream(string $path)
    {
        $contents = $this->read($path);

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, error_get_last()['message'] ?? '');
        }

        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function listContents(string $path, bool $deep): iterable
    {
        return $this->adapter->keys();
    }

    /**
     * {@inheritdoc}
     */
    public function fileSize(string $path): FileAttributes
    {
        if ($this->adapter instanceof Adapter\SizeCalculator) {
            return new FileAttributes($path, $this->adapter->size($path));
        }

        throw new UnsupportedAdapterMethodException('fileSize is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function mimeType(string $path): FileAttributes
    {
        if ($this->adapter instanceof Adapter\MimeTypeProvider) {
            return new FileAttributes($path, null, null, null, $this->adapter->mimeType($path));
        }

        throw new UnsupportedAdapterMethodException('mimeType is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function lastModified(string $path): FileAttributes
    {
        $timestamp = $this->adapter->mtime($path);

        return new FileAttributes($path, null, null, $timestamp);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw new UnsupportedAdapterMethodException('setVisibility is not supported by this adapter.');
    }

    public function visibility(string $path): FileAttributes
    {
        throw new UnsupportedAdapterMethodException('visibility is not supported by this adapter.');
    }
}
