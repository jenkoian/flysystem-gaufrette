# Flysystem Adapter for Gaufrette

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

TODO: Explanation
