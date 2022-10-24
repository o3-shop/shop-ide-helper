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

namespace OxidEsales\EshopIdeHelper;

use \Webmozart\PathUtil\Path;
use OxidEsales\Facts\Facts;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;

/**
 * Class HelpFactory: assemble all needed objects
 */
class HelpFactory
{
    const SCAN_FOR_FILENAME = 'metadata.php';

    const SCAN_FOR_DIRECTORY = 'modules';

    /** @var Facts */
    private $facts;

    /**
     * @var UnifiedNameSpaceClassMapProvider
     */
    private $unifiedNameSpaceClassMapProvider;

    /**
     * @var BackwardsCompatibilityClassMapProvider
     */
    private $backwardsCompatibilityClassMapProvider;

    /**
     * @var ModuleExtendClassMapProvider
     */
    private $moduleExtendClassMapProvider;

    /**
     * @return Facts
     */
    public function getFacts()
    {
        if (!is_a($this->facts, Facts::class)) {
            $this->facts = new Facts();
        }

        return $this->facts;
    }

    /**
     * @return UnifiedNameSpaceClassMapProvider
     */
    public function getUnifiedNameSpaceClassMapProvider()
    {
        if (!is_a($this->unifiedNameSpaceClassMapProvider, UnifiedNameSpaceClassMapProvider::class)) {
            $this->unifiedNameSpaceClassMapProvider = new UnifiedNameSpaceClassMapProvider($this->getFacts());
        }

        return $this->unifiedNameSpaceClassMapProvider;
    }

    /**
     * @return BackwardsCompatibilityClassMapProvider
     */
    public function getBackwardsCompatibilityClassMapProvider()
    {
        if (!is_a($this->backwardsCompatibilityClassMapProvider, BackwardsCompatibilityClassMapProvider::class)) {
            $this->backwardsCompatibilityClassMapProvider = new BackwardsCompatibilityClassMapProvider($this->getFacts());
        }

        return $this->backwardsCompatibilityClassMapProvider;
    }

    /**
     * @return ModuleExtendClassMapProvider
     */
    public function getModuleExtendClassMapProvider()
    {
        if (!is_a($this->moduleExtendClassMapProvider, ModuleExtendClassMapProvider::class)) {
            $modulesDirectory = Path::join($this->facts->getSourcePath(), self::SCAN_FOR_DIRECTORY);
            $scanner = new DirectoryScanner(self::SCAN_FOR_FILENAME, $modulesDirectory);
            $parser = new ModuleMetadataParser($scanner);
            $this->moduleExtendClassMapProvider =  new ModuleExtendClassMapProvider($parser);
        }

        return $this->moduleExtendClassMapProvider;
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        $generator = new Generator(
            $this->getFacts(),
            $this->getUnifiedNameSpaceClassMapProvider(),
            $this->getBackwardsCompatibilityClassMapProvider(),
            $this->getModuleExtendClassMapProvider()
        );
        return $generator;
    }
}
