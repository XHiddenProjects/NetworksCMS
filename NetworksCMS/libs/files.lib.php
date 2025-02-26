<?php
namespace NetWorks\libs;
/**
 * File library
 */
class Files{
    public function __construct() {
        
    }
    /**
     * Scans the directory
     * @param string $dir Directory
     * @return array Array of files/directories
     */
    public function scan(string $dir, int $sort=SCANDIR_SORT_ASCENDING): array{
        return array_diff( scandir(directory: $dir, sorting_order: $sort),['.','..']);
    }
    /**
     * Checks if file/directory exists
     * @param string $path Path to check
     * @return bool TRUE if file exists, else FALSE
     */
    public function exists(string $path): bool{
        return file_exists(filename: $path);
    }
}
?>