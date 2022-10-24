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

/**
 * Class ModuleMetadataParser: parse module metadata.php extend section.
 */
class ModuleMetadataParser
{
    /**
     * @var DirectoryScanner
     */
    private $scanner;

    /**
     * ModuleMetadataParser constructor.
     *
     * @param DirectoryScanner $scanner
     */
    public function __construct(DirectoryScanner $scanner)
    {
        $this->scanner = $scanner;
    }

    /**
     * Get all module chain extensions.
     * Key is module class, value is shop class.
     *
     * @return array
     */
    public function getChainExtendedClasses()
    {
        $chainExtendMap = [];
        $paths = $this->scanner->getFilePaths();

        foreach ($paths as $path) {
            $aModule = [];
            include($path);
            if (isset($aModule['extend'])) {
                $chainExtendMap = array_merge($chainExtendMap, array_flip($aModule['extend']));
            }
        }
        return $chainExtendMap;
    }
}
