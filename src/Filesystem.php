<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 16:44
 */

namespace CodeSinging\Filesystem;

use ErrorException;
use Exception;
use FilesystemIterator;
use Symfony\Component\Finder\Finder;

class Filesystem
{
    /**
     * Determine if a file or directory exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function exists(string $path)
    {
        return file_exists($path);
    }

    /**
     * Determine if a file or directory is missing.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function missing(string $path)
    {
        return !file_exists($path);
    }

    /**
     * Determine if the given path is a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isFile(string $path)
    {
        return is_file($path);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param string $directory
     *
     * @return bool
     */
    public static function isDirectory($directory)
    {
        return is_dir($directory);
    }

    /**
     * Determine if the given path is readable.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Get the contents of a file.
     *
     * @param string $path
     *
     * @return false|string
     * @throws Exception
     */
    public static function get(string $path)
    {
        if (self::isFile($path)) {
            return file_get_contents($path);
        }

        throw new Exception('Filesystem does not exist at path: ' . $path);
    }

    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param string $contents
     * @param int    $flags
     *
     * @return bool|int
     */
    public static function put(string $path, string $contents, int $flags = 0)
    {
        return file_put_contents($path, $contents, $flags);
    }

    /**
     * Get or set UNIX mode of a file or directory.
     *
     * @param string   $path
     * @param int|null $mode
     *
     * @return mixed
     */
    public static function chmod($path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }

        return substr(sprintf('%o', fileperms($path)), -4);
    }

    /**
     * Prepend to a file.
     *
     * @param string $path
     * @param string $content
     *
     * @return bool|int
     * @throws Exception
     */
    public static function prepend(string $path, string $content)
    {
        self::exists($path) and $content .= self::get($path);

        return self::put($path, $content);
    }

    /**
     * Append to a file.
     *
     * @param string $path
     * @param string $content
     *
     * @return bool|int
     */
    public static function append(string $path, string $content)
    {
        return file_put_contents($path, $content, FILE_APPEND);
    }

    /**
     * Delete the file at a given path.
     *
     * @param string|array $paths
     *
     * @return bool
     */
    public static function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (!@unlink($path)) {
                    $success = false;
                }
            } catch (ErrorException $e) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Move a file to a new location.
     *
     * @param string $path
     * @param string $target
     *
     * @return bool
     */
    public static function move($path, $target)
    {
        return rename($path, $target);
    }

    /**
     * Copy a file to a new location.
     *
     * @param string $path
     * @param string $target
     *
     * @return bool
     */
    public static function copy($path, $target)
    {
        return copy($path, $target);
    }

    /**
     * Extract the file name from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Extract the trailing name component from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Extract the parent directory from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the file type of a given file.
     *
     * @param string $path
     *
     * @return string
     */
    public static function type($path)
    {
        return filetype($path);
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param string $path
     *
     * @return string|false
     */
    public static function mimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

    /**
     * Get the file size of a given file.
     *
     * @param string $path
     *
     * @return int
     */
    public static function size($path)
    {
        return filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param string $path
     *
     * @return int
     */
    public static function lastModified($path)
    {
        return filemtime($path);
    }

    /**
     * Get the MD5 hash of the file at the given path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function hash(string $path)
    {
        return md5_file($path);
    }

    /**
     * Write the contents of a file, replacing it atomically if it already exists.
     *
     * @param string $path
     * @param string $content
     *
     * @return void
     */
    public static function replace($path, $content)
    {
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;
        $tempPath = tempnam(dirname($path), basename($path));
        chmod($tempPath, 0777 - umask());

        file_put_contents($tempPath, $content);
        rename($tempPath, $path);
    }

    /**
     * Find path names matching a given pattern.
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return array
     */
    public static function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     * @param bool   $hidden
     *
     * @return array
     */
    public static function files(string $directory, bool $recursive = false, bool $hidden = false)
    {
        $finder = Finder::create()->files()->ignoreDotFiles(!$hidden)->in($directory);
        $recursive or $finder->depth(0);
        return iterator_to_array($finder, false);
    }

    /**
     * Get all of the files from the given directory (recursive).
     * @param string $directory
     * @param bool   $hidden
     *
     * @return array
     */
    public static function allFiles(string $directory, bool $hidden = false)
    {
        return self::files($directory, true, $hidden);
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param string $directory
     *
     * @return array
     */
    public static function directories(string $directory)
    {
        $directories = [];
        $finder = Finder::create()->directories()->in($directory)->depth(0);
        foreach ($finder as $directory) {
            $directories[] = $directory->getPathname();
        }

        return $directories;
    }

    /**
     * Create a directory.
     *
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     * @param bool   $force
     *
     * @return bool
     */
    public static function makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Move a directory.
     *
     * @param string $from
     * @param string $to
     * @param bool   $overwrite
     *
     * @return bool
     */
    public static function moveDirectory($from, $to, $overwrite = false)
    {
        if ($overwrite && self::isDirectory($to)) {
            if (!self::deleteDirectory($to)) {
                return false;
            }
        }
        return @rename($from, $to) === true;
    }

    /**
     * Copy a directory to another location.
     *
     * @param string   $directory
     * @param string   $destination
     * @param int|null $options
     *
     * @return bool
     */
    public static function copyDirectory(string $directory, string $destination, int $options = null)
    {
        if (!self::isDirectory($directory)) {
            return false;
        }

        $options = $options ?: FilesystemIterator::SKIP_DOTS;

        if (!self::isDirectory($destination)) {
            self::makeDirectory($destination, 0777, true);
        }

        $items = new FilesystemIterator($directory, $options);

        foreach ($items as $item) {
            $target = $destination . '/' . $item->getBasename();

            if ($item->isDir()) {
                $path = $item->getPathname();

                if (!self::copyDirectory($path, $target, $options)) {
                    return false;
                }
            } else {
                if (!self::copy($item->getPathname(), $target)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Delete a directory recursively.
     *
     * @param string $directory
     * @param bool   $preserve
     *
     * @return bool
     */
    public static function deleteDirectory(string $directory, bool $preserve = false)
    {
        if (!self::isDirectory($directory)) {
            return false;
        }

        $items = new FilesystemIterator($directory);

        foreach ($items as $item) {
            if ($item->isDir() && !$item->isLink()) {
                self::deleteDirectory($item->getPathname());
            } else {
                self::delete($item->getPathname());
            }
        }

        if (!$preserve) {
            @rmdir($directory);
        }

        return true;
    }

    /**
     * Empty the specified directory of all files and folders.
     *
     * @param string $directory
     *
     * @return bool
     */
    public static function cleanDirectory(string $directory)
    {
        return self::deleteDirectory($directory, true);
    }
}