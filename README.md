<h1 align="center"> Filesystem </h1>

<p align="center"> A filesystem package for PHP development.</p>


## Installing

```shell
$ composer require codesinging/filesystem -vvv
```

## Usage

### Filesystem
- `exists(string $path)`
- `missing(string $path)`
- `isFile(string $path)`
- `isDirectory(string $directory)`
- `isReadable(string $path)`
- `isWritable(string $path)`
- `get(string $path)`
- `put(string $path, string $contents, int $flags = 0)`
- `chmod(string $path, int $mode = null)`
- `prepend(string $path, string $content)`
- `append(string $path, string $content)`
- `delete($paths)`
- `move(string $path, string $target)`
- `copy(string $path, string $target)`
- `name(string $path)`
- `basename(string $path)`
- `dirname(string $path)`
- `extension(string $path)`
- `type(string $path)`
- `mimeType(string $path)`
- `size(string $path)`
- `lastModified(string $path)`
- `hash(string $path)`
- `replace(string $path, string $content)`
- `glob(string $pattern, int $flags = 0)`
- `files(string $directory, bool $recursive = false, bool $hidden = false)`
- `allFiles(string $directory, bool $hidden = false)`
- `directories(string $directory)`
- `makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false)`
- `moveDirectory($from, $to, $overwrite = false)`
- `copyDirectory(string $directory, string $destination, int $options = null)`
- `deleteDirectory(string $directory, bool $preserve = false)`
- `cleanDirectory(string $directory)`

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/codesinging/filesystem/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/codesinging/filesystem/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT