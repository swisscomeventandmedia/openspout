<?php

namespace OpenSpout\Common\Helper;

use OpenSpout\Common\Exception\IOException;

/**
 * This class provides helper functions to help with the file system operations
 * like files/folders creation & deletion.
 */
class FileSystemHelper implements FileSystemHelperInterface
{
    /** @var string Real path of the base folder where all the I/O can occur */
    protected string $baseFolderRealPath;

    /**
     * @param string $baseFolderPath The path of the base folder where all the I/O can occur
     */
    public function __construct(string $baseFolderPath)
    {
        $realpath = realpath($baseFolderPath);
        \assert(false !== $realpath);
        $this->baseFolderRealPath = $realpath;
    }

    /**
     * Creates an empty folder with the given name under the given parent folder.
     *
     * @param string $parentFolderPath The parent folder path under which the folder is going to be created
     * @param string $folderName       The name of the folder to create
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder or if the folder path is not inside of the base folder
     *
     * @return string Path of the created folder
     */
    public function createFolder(string $parentFolderPath, string $folderName): string
    {
        $this->throwIfOperationNotInBaseFolder($parentFolderPath);

        $folderPath = $parentFolderPath.'/'.$folderName;

        $wasCreationSuccessful = mkdir($folderPath, 0777, true);
        if (!$wasCreationSuccessful) {
            throw new IOException("Unable to create folder: {$folderPath}");
        }

        return $folderPath;
    }

    /**
     * Creates a file with the given name and content in the given folder.
     * The parent folder must exist.
     *
     * @param string $parentFolderPath The parent folder path where the file is going to be created
     * @param string $fileName         The name of the file to create
     * @param string $fileContents     The contents of the file to create
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file or if the file path is not inside of the base folder
     *
     * @return string Path of the created file
     */
    public function createFileWithContents(string $parentFolderPath, string $fileName, string $fileContents): string
    {
        $this->throwIfOperationNotInBaseFolder($parentFolderPath);

        $filePath = $parentFolderPath.'/'.$fileName;

        $wasCreationSuccessful = file_put_contents($filePath, $fileContents);
        if (false === $wasCreationSuccessful) {
            throw new IOException("Unable to create file: {$filePath}");
        }

        return $filePath;
    }

    /**
     * Delete the file at the given path.
     *
     * @param string $filePath Path of the file to delete
     *
     * @throws \OpenSpout\Common\Exception\IOException If the file path is not inside of the base folder
     */
    public function deleteFile(string $filePath): void
    {
        $this->throwIfOperationNotInBaseFolder($filePath);

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Delete the folder at the given path as well as all its contents.
     *
     * @param string $folderPath Path of the folder to delete
     *
     * @throws \OpenSpout\Common\Exception\IOException If the folder path is not inside of the base folder
     */
    public function deleteFolderRecursively(string $folderPath): void
    {
        $this->throwIfOperationNotInBaseFolder($folderPath);

        $itemIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($itemIterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($folderPath);
    }

    /**
     * All I/O operations must occur inside the base folder, for security reasons.
     * This function will throw an exception if the folder where the I/O operation
     * should occur is not inside the base folder.
     *
     * @param string $operationFolderPath The path of the folder where the I/O operation should occur
     *
     * @throws \OpenSpout\Common\Exception\IOException If the folder where the I/O operation should occur
     *                                                 is not inside the base folder or the base folder does not exist
     */
    private function throwIfOperationNotInBaseFolder(string $operationFolderPath): void
    {
        $operationFolderRealPath = realpath($operationFolderPath);
        if (false === $operationFolderRealPath) {
            throw new IOException("Folder not found: {$operationFolderRealPath}");
        }
        $isInBaseFolder = (0 === strpos($operationFolderRealPath, $this->baseFolderRealPath));
        if (!$isInBaseFolder) {
            throw new IOException("Cannot perform I/O operation outside of the base folder: {$this->baseFolderRealPath}");
        }
    }
}
