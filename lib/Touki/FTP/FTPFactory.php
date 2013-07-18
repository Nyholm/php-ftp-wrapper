<?php

namespace Touki\FTP;

use Touki\FTP\Manager\FTPFilesystemManager;

/**
 * Factory class for FTP
 *
 * @author Touki <g.vincendon@vithemis.com>
 */
class FTPFactory
{
    /**
     * Creates an FTP instance
     *
     * @return FTP An FTP instance
     */
    public function build(ConnectionInterface $connection)
    {
        if (!$connection->isConnected()) {
            $connection->open();
        }

        $wrapper = new FTPWrapper($connection);

        $factory = new FilesystemFactory(new PermissionsFactory);
        $manager = new FTPFilesystemManager($wrapper, $factory);

        $dlVoter = new DownloaderVoter;
        $dlVoter->addDefaultFTPDownloaders($wrapper);

        $ulVoter = new UploaderVoter;
        $ulVoter->addDefaultFTPUploaders($wrapper);

        return new FTP($manager, $dlVoter, $ulVoter);
    }
}