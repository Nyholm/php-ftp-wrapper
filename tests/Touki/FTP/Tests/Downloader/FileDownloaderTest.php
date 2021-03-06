<?php

/**
 * This file is a part of the FTP Wrapper package
 *
 * For the full informations, please read the README file
 * distributed with this source code
 *
 * @package FTP Wrapper
 * @version 1.1.0
 * @author  Touki <g.vincendon@vithemis.com>
 */

namespace Touki\FTP\Tests\Downloader;

use Touki\FTP\Tests\ConnectionAwareTestCase;
use Touki\FTP\Downloader\FileDownloader;
use Touki\FTP\Model\File;
use Touki\FTP\Model\Directory;
use Touki\FTP\FTP;
use Touki\FTP\FTPWrapper;

/**
 * File downloader test
 *
 * @author Touki <g.vincendon@vithemis.com>
 */
class FileDownloaderTest extends ConnectionAwareTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->downloader = new FileDownloader(new FTPWrapper(self::$connection));
        $this->local      = tempnam(sys_get_temp_dir(), 'filedownloader');
        $this->remote     = new File('file1.txt');
        $this->options    = array(
            FTP::NON_BLOCKING => false
        );
    }

    public function testVote()
    {
        $this->assertTrue($this->downloader->vote($this->local, $this->remote, $this->options));
    }

    public function testDownload()
    {
        $this->assertTrue($this->downloader->download($this->local, $this->remote, $this->options));
        $this->assertFileExists($this->local);
        $this->assertEquals(file_get_contents($this->local), 'file1');

        unlink($this->local);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid remote file given, expected instance of File, got Touki\FTP\Model\Directory
     */
    public function testDownloadWrongFilesystemInstance()
    {
        $remote = new Directory('/');

        $this->downloader->download($this->local, $remote, $this->options);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid local file given. Expected filename, got resource
     */
    public function testDownloadResourceGiven()
    {
        $local = fopen($this->local, 'w+');

        $this->downloader->download($local, $this->remote, $this->options);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid local file given. Expected filename, got directory
     */
    public function testDownloadDirectoryGiven()
    {
        $local = __DIR__;

        $this->downloader->download($local, $this->remote, $this->options);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid option given. Expected false as FTP::NON_BLOCKING parameter
     */
    public function testDownloadNoOptionNonBlocking()
    {
        $this->downloader->download($this->local, $this->remote);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid option given. Expected false as FTP::NON_BLOCKING parameter
     */
    public function testDownloadWrongOptionNonBlocking()
    {
        $this->downloader->download($this->local, $this->remote, array(
            FTP::NON_BLOCKING => true
        ));
    }
}
