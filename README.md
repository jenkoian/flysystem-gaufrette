# [Flysystem](https://github.com/thephpleague/flysystem) Adapter for [Gaufrette](https://github.com/KnpLabs/Gaufrette)

[![Build Status](https://img.shields.io/travis/jenkoian/flysystem-gaufrette/master.svg?style=flat-square)](https://travis-ci.org/jenkoian/flysystem-gaufrette)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/jenkoian/flysystem-gaufrette.svg?style=flat-square)](https://scrutinizer-ci.com/g/jenkoian/flysystem-gaufrette/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/jenkoian/flysystem-gaufrette.svg?style=flat-square)](https://scrutinizer-ci.com/g/jenkoian/flysystem-gaufrette)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

## Installation

```bash
composer require jenko/flysystem-gaufrette
```

## Usage

Basic usage:

```php
use Jenko\Flysystem\GaufretteAdapter;
use Gaufrette\Adapter\Local;

$adapter = new GaufretteAdapter(
    new Local(__DIR__ . '/path/to/files');
); 

$filesystem = new Filesystem($adapter);
```

Advanced usage:

```php
use Jenko\Flysystem\GaufretteAdapter;
use Gaufrette\Adapter\Local;
use Gaufrette\Adapter\Flysystem;

// Hadouken!
$adapter = new GaufretteAdapter(
    new Flysystem(
        new GaufretteAdapter(
            new Flysystem(
                new GaufretteAdapter(
                    new Flysystem(
                        new GaufretteAdapter(
                            new Local(
                                __DIR__ . '/path/to/files'
                            )
                        )
                    )
                )
            )
        )
    )
);

$filesystem = new Filesystem($adapter);
```

## Wait, what?

[Gaufrette added a Flysystem adapter](https://github.com/KnpLabs/Gaufrette/blob/master/doc/adapters/flysystem.md) so it made sense to reciprocate the love and have an adapter going in the other direction.
Although it does feel a little [yo dawg](https://cloud.githubusercontent.com/assets/993350/13571485/99fd5f90-e475-11e5-9f2c-04dea88713fd.png) it is useful. For example
if you have a codebase which is quite coupled to the flysystem API (legacy app, obv you wouldn't have done this) but wish to make a switch to Gaufrette, this will allow you to do so without having to go through 
your codebase changing all calls to the old API.

It also allows you to construct fun, bi-directional, [hadouken-esque](https://imgur.com/BtjZedW) nesting as seen in the advanced usage example above.

## Unsupported methods

Flysystem has a few methods which Gaufrette doesn't quite support, these are listed below:

* update
* updateStream
* copy
* createDir
* getVisibility
* setVisibility

The following methods are only supported for Gaufrette adapters implementing `MetadataSupporter`:

* getMetadata

The following methods are only supported for Gaufrette adapters implementing `SizeCalculator`:

* getSize

The following methods are only supported for Gaufrette adapters implementing `MimeTypeProvider`:

* getMimetype

