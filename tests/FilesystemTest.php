<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/4 16:47
 */

namespace CodeSinging\Filesystem\Tests;

use CodeSinging\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    private $tempDir;
    private $tempFile;
    private $missingDir;
    private $missingFile;
    private $tempMissingFile;
    private $hello = 'Hello';
    private $world = 'World';

    protected function setUp()
    {
        $this->tempDir = __DIR__ . '/tmp';
        $this->tempFile = $this->tempDir . '/file.txt';

        $this->missingDir = __DIR__ . '/missing';
        $this->missingFile = $this->missingDir . '/missing.txt';

        $this->tempMissingFile = $this->tempDir . '/missing.txt';

        mkdir($this->tempDir);
        file_put_contents($this->tempFile, $this->hello);
    }

    protected function tearDown()
    {
        Filesystem::deleteDirectory($this->tempDir);
        Filesystem::deleteDirectory($this->missingDir);
    }

    public function testExists()
    {
        self::assertTrue(Filesystem::exists($this->tempDir));
        self::assertTrue(Filesystem::exists($this->tempFile));
    }

    public function testMissing()
    {
        self::assertTrue(Filesystem::missing($this->missingFile));
        self::assertFalse(Filesystem::missing($this->tempFile));
    }

    public function testIsFile()
    {
        self::assertTrue(Filesystem::isFile($this->tempFile));
        self::assertFalse(Filesystem::isFile($this->tempDir));
        self::assertFalse(Filesystem::isFile($this->missingFile));
        self::assertFalse(Filesystem::isFile($this->missingDir));
    }

    public function testIsDirectory()
    {
        self::assertTrue(Filesystem::isDirectory($this->tempDir));
        self::assertFalse(Filesystem::isDirectory($this->tempFile));
        self::assertFalse(Filesystem::isDirectory($this->missingDir));
        self::assertFalse(Filesystem::isDirectory($this->missingFile));
    }

    public function testIsReadable()
    {
        self::assertTrue(Filesystem::isReadable($this->tempFile));
    }

    public function testIsWritable()
    {
        self::assertTrue(Filesystem::isWritable($this->tempDir));
    }

    public function testGet()
    {
        self::assertEquals($this->hello, Filesystem::get($this->tempFile));
    }

    public function testPut()
    {
        Filesystem::put($this->tempFile, $this->world);
        self::assertStringEqualsFile($this->tempFile, $this->world);
    }

    public function testPrepend()
    {
        Filesystem::prepend($this->tempFile, $this->world);
        Filesystem::prepend($this->tempMissingFile, $this->world);
        self::assertStringEqualsFile($this->tempFile, $this->world . $this->hello);
        self::assertStringEqualsFile($this->tempMissingFile, $this->world);
    }

    public function testAppend()
    {
        Filesystem::append($this->tempFile, $this->world);
        Filesystem::append($this->tempMissingFile, $this->world);
        self::assertStringEqualsFile($this->tempFile, $this->hello . $this->world);
        self::assertStringEqualsFile($this->tempMissingFile, $this->world);
    }

    public function testSetChmod()
    {
        file_put_contents($this->tempFile, $this->hello);
        Filesystem::chmod($this->tempFile, 0755);
        $filePermission = substr(sprintf('%o', fileperms($this->tempFile)), -4);
        $expectedPermissions = DIRECTORY_SEPARATOR == '\\' ? '0666' : '0755';
        $this->assertEquals($expectedPermissions, $filePermission);
    }

    public function testGetChmod()
    {
        file_put_contents($this->tempFile, $this->hello);
        chmod($this->tempFile, 0755);

        $filePermission = Filesystem::chmod($this->tempFile);
        $expectedPermissions = DIRECTORY_SEPARATOR == '\\' ? '0666' : '0755';
        $this->assertEquals($expectedPermissions, $filePermission);
    }

    public function testDelete()
    {
        file_put_contents($this->tempDir . '/file1.txt', 'Hello World');
        file_put_contents($this->tempDir . '/file2.txt', 'Hello World');
        file_put_contents($this->tempDir . '/file3.txt', 'Hello World');
        file_put_contents($this->tempDir . '/file4.txt', 'Hello World');
        file_put_contents($this->tempDir . '/file5.txt', 'Hello World');

        Filesystem::delete($this->tempDir . '/file1.txt');
        self::assertFileNotExists($this->tempDir . '/file1.txt');

        Filesystem::delete([$this->tempDir . '/file2.txt', $this->tempDir . '/file3.txt']);
        self::assertFileNotExists($this->tempDir . '/file2.txt');
        self::assertFileNotExists($this->tempDir . '/file3.txt');

        Filesystem::delete($this->tempDir . '/file4.txt', $this->tempDir . '/file5.txt');
        self::assertFileNotExists($this->tempDir . '/file4.txt');
        self::assertFileNotExists($this->tempDir . '/file5.txt');
    }

    public function testMove()
    {
        Filesystem::move($this->tempFile, $this->tempMissingFile);
        self::assertFileExists($this->tempMissingFile);
        self::assertFileNotExists($this->tempFile);;
    }

    public function testCopy()
    {
        Filesystem::copy($this->tempFile, $this->tempMissingFile);
        self::assertFileExists($this->tempMissingFile);
        self::assertEquals(file_get_contents($this->tempFile), file_get_contents($this->tempMissingFile));
    }

    public function testName()
    {
        self::assertEquals('file', Filesystem::name($this->tempFile));
    }

    public function testBasename()
    {
        self::assertEquals('file.txt', Filesystem::basename($this->tempFile));
    }

    public function testDirname()
    {
        self::assertEquals($this->tempDir, Filesystem::dirname($this->tempFile));
    }

    public function testExtension()
    {
        self::assertEquals('txt', Filesystem::extension($this->tempFile));
    }

    public function testType()
    {
        self::assertEquals('file', Filesystem::type($this->tempFile));
    }

    public function testMimeType()
    {
        self::assertEquals('text/plain', Filesystem::mimeType($this->tempFile));
    }

    public function testSize()
    {
        $size = file_put_contents($this->tempFile, $this->hello . $this->world);
        self::assertEquals($size, Filesystem::size($this->tempFile));
    }

    public function testLastModified()
    {
        file_put_contents($this->tempFile, $this->world);
        self::assertEquals(filemtime($this->tempFile), Filesystem::lastModified($this->tempFile));
    }

    public function testHash()
    {
        file_put_contents($this->tempDir . '/foo.txt', 'foo');
        self::assertEquals('acbd18db4cc2f85cedef654fccc4a4d8', Filesystem::hash($this->tempDir . '/foo.txt'));
    }

    public function testReplace()
    {
        Filesystem::replace($this->tempFile, $this->world);
        self::assertStringEqualsFile($this->tempFile, $this->world);
    }

    public function testGlob()
    {
        file_put_contents($this->tempDir . '/foo.txt', 'foo');
        file_put_contents($this->tempDir . '/bar.txt', 'bar');
        $glob = Filesystem::glob($this->tempDir . '/*.txt');
        $this->assertContains($this->tempDir . '/foo.txt', $glob);
        $this->assertContains($this->tempDir . '/bar.txt', $glob);
    }

    public function testFiles()
    {
        mkdir($this->tempDir . '/foo');
        file_put_contents($this->tempDir . '/foo/1.txt', '1');
        file_put_contents($this->tempDir . '/foo/2.txt', '2');
        mkdir($this->tempDir . '/foo/bar');
        $results = Filesystem::files($this->tempDir . '/foo');
        $this->assertInstanceOf('SplFileInfo', $results[0]);
        $this->assertInstanceOf('SplFileInfo', $results[1]);
        unset($files);
    }

    public function testAllFiles()
    {
        file_put_contents($this->tempDir . '/foo.txt', 'foo');
        file_put_contents($this->tempDir . '/bar.txt', 'bar');
        $allFiles = [];
        foreach (Filesystem::allFiles($this->tempDir) as $file) {
            $allFiles[] = $file->getFilename();
        }
        $this->assertContains('foo.txt', $allFiles);
        $this->assertContains('bar.txt', $allFiles);
    }

    public function testDirectories()
    {
        mkdir($this->tempDir . '/foo');
        mkdir($this->tempDir . '/bar');
        $directories = Filesystem::directories($this->tempDir);
        $this->assertContains($this->tempDir . DIRECTORY_SEPARATOR . 'foo', $directories);
        $this->assertContains($this->tempDir . DIRECTORY_SEPARATOR . 'bar', $directories);
    }

    public function testMakeDirectory()
    {
        self::assertTrue(Filesystem::makeDirectory($this->tempDir . '/foo'));
        self::assertFileExists($this->tempDir . '/foo');
    }

    public function testMoveDirectory()
    {
        mkdir($this->tempDir . '/tmp', 0777, true);
        file_put_contents($this->tempDir . '/tmp/foo.txt', '');
        file_put_contents($this->tempDir . '/tmp/bar.txt', '');
        mkdir($this->tempDir . '/tmp/nested', 0777, true);
        file_put_contents($this->tempDir . '/tmp/nested/baz.txt', '');

        Filesystem::moveDirectory($this->tempDir . '/tmp', $this->tempDir . '/tmp2');
        $this->assertTrue(is_dir($this->tempDir . '/tmp2'));
        $this->assertFileExists($this->tempDir . '/tmp2/foo.txt');
        $this->assertFileExists($this->tempDir . '/tmp2/bar.txt');
        $this->assertTrue(is_dir($this->tempDir . '/tmp2/nested'));
        $this->assertFileExists($this->tempDir . '/tmp2/nested/baz.txt');
        $this->assertFalse(is_dir($this->tempDir . '/tmp'));
    }

    public function testMoveDirectoryAndOverwrite()
    {
        mkdir($this->tempDir . '/tmp', 0777, true);
        file_put_contents($this->tempDir . '/tmp/foo.txt', '');
        file_put_contents($this->tempDir . '/tmp/bar.txt', '');
        mkdir($this->tempDir . '/tmp/nested', 0777, true);
        file_put_contents($this->tempDir . '/tmp/nested/baz.txt', '');
        mkdir($this->tempDir . '/tmp2', 0777, true);
        file_put_contents($this->tempDir . '/tmp2/foo2.txt', '');
        file_put_contents($this->tempDir . '/tmp2/bar2.txt', '');

        Filesystem::moveDirectory($this->tempDir . '/tmp', $this->tempDir . '/tmp2', true);
        $this->assertTrue(is_dir($this->tempDir . '/tmp2'));
        $this->assertFileExists($this->tempDir . '/tmp2/foo.txt');
        $this->assertFileExists($this->tempDir . '/tmp2/bar.txt');
        $this->assertTrue(is_dir($this->tempDir . '/tmp2/nested'));
        $this->assertFileExists($this->tempDir . '/tmp2/nested/baz.txt');
        $this->assertFileNotExists($this->tempDir . '/tmp2/foo2.txt');
        $this->assertFileNotExists($this->tempDir . '/tmp2/bar2.txt');
        $this->assertFalse(is_dir($this->tempDir . '/tmp'));
    }

    public function testCopyDirectory()
    {
        mkdir($this->tempDir . '/tmp', 0777, true);
        file_put_contents($this->tempDir . '/tmp/foo.txt', '');
        file_put_contents($this->tempDir . '/tmp/bar.txt', '');
        mkdir($this->tempDir . '/tmp/nested', 0777, true);
        file_put_contents($this->tempDir . '/tmp/nested/baz.txt', '');

        Filesystem::copyDirectory($this->tempDir . '/tmp', $this->tempDir . '/tmp2');
        $this->assertTrue(is_dir($this->tempDir . '/tmp2'));
        $this->assertFileExists($this->tempDir . '/tmp2/foo.txt');
        $this->assertFileExists($this->tempDir . '/tmp2/bar.txt');
        $this->assertTrue(is_dir($this->tempDir . '/tmp2/nested'));
        $this->assertFileExists($this->tempDir . '/tmp2/nested/baz.txt');

        self::assertFalse(Filesystem::copyDirectory($this->missingDir, $this->tempDir));
    }

    public function testDeleteDirectory()
    {
        self::assertFalse(Filesystem::deleteDirectory($this->tempFile));
        Filesystem::deleteDirectory($this->tempDir);
        self::assertFalse(is_dir($this->tempDir));
        self::assertFileNotExists($this->tempFile);
    }

    public function testCleanDirectory()
    {
        Filesystem::cleanDirectory($this->tempDir);
        self::assertTrue(is_dir($this->tempDir));
        self::assertFileNotExists($this->tempFile);
    }
}