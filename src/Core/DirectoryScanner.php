<?php
/**
 * This file is part of O3-Shop.
 *
 * O3-Shop is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU General Public License as published by  
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>
 *
 * @copyright  Copyright (c) 2022 OXID eSales AG (https://www.oxid-esales.com)
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

namespace OxidEsales\EshopIdeHelper\Core;

use \Webmozart\PathUtil\Path;

/**
 * Class DirectoryScanner: Recursively scan given path for matching files (case insensitive).
 */
class DirectoryScanner
{
    /**
     * @var array
     */
    private $filePaths = [];

    /**
     * @var string
     */
    private $searchForFileName = '';

    /**
     * @var string
     */
    private $startPath = '';

    /**
     * DirectoryScanner constructor.
     *
     * @param string $searchForFileName
     * @param string $startPath
     */
    public function __construct($searchForFileName, $startPath)
    {
        $this->searchForFileName = strtolower($searchForFileName);
        $this->startPath = $startPath;
    }

    /**
     * Scan shop modules directory.
     *
     * @return array
     */
    public function getFilePaths()
    {
        $this->scanDirectory($this->startPath);

        return $this->filePaths;
    }

    /**
     * Recursive search for matching files.
     *
     * @param string $clearFolderPath Sub-folder path to check for search file name.
     */
    private function scanDirectory($directoryPath)
    {
        if (is_dir($directoryPath)) {
            $files = scandir($directoryPath);
            foreach ($files as $fileName) {
                $filePath = Path::join($directoryPath, $fileName);
                if (is_dir($filePath) && !in_array($fileName, ['.', '..'])) {
                    $this->scanDirectory($filePath);
                } elseif ($this->searchForFileName == strtolower($fileName)) {
                    $this->filePaths[] = $filePath;
                }
            }
        }
    }
}
