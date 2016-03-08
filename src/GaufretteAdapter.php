<?php

namespace Jenko\Flysystem;

use Gaufrette\Adapter;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Adapter\Polyfill\StreamedReadingTrait;
use League\Flysystem\Config;

class GaufretteAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;
    use StreamedReadingTrait;

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
    public function write($path, $contents, Config $config)
    {
        $result = $this->adapter->write($path, $contents);

        if ($result === false) {
            return false;
        }

        return ['type' => 'file', 'contents' => $contents, 'size' => $result, 'path' => $path];
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        $contents = stream_get_contents($resource);
        $result = $this->adapter->write($path, $contents);

        if ($result === false) {
            return false;
        }

        return ['type' => 'file', 'contents' => $contents, 'size' => $result, 'path' => $path];
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        throw new UnsupportedAdapterMethodException('update is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        throw new UnsupportedAdapterMethodException('update is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        return $this->adapter->rename($path, $newpath);
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        throw new UnsupportedAdapterMethodException('copy is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        return $this->adapter->delete($path);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        if ($this->adapter->isDirectory($dirname)) {
            return $this->adapter->delete($dirname);    
        }
        
        throw new \InvalidArgumentException($dirname . 'is not a valid directory.');
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        throw new UnsupportedAdapterMethodException('createDir is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return $this->adapter->exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        return ['contents' => $this->adapter->read($path), 'path' => $path];
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->adapter->keys();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        if ($this->adapter instanceof Adapter\MetadataSupporter) {
            return $this->adapter->getMetadata($path);
        }
        
        throw new UnsupportedAdapterMethodException('getMetadata is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        if ($this->adapter instanceof Adapter\SizeCalculator) {
            return $this->adapter->size($path);
        }

        throw new UnsupportedAdapterMethodException('getSize is not supported by this adapter.');    
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        if ($this->adapter instanceof Adapter\MimeTypeProvider) {
            return $this->adapter->mimeType($path);
        }

        throw new UnsupportedAdapterMethodException('getMimetype is not supported by this adapter.');
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        $timestamp = $this->adapter->mtime($path);

        return ['timestamp' => $timestamp];
    }
}
