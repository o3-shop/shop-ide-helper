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
 * Class ModuleExtendClassMap: maps virtual module parent classes to releated shop class.
 */
class ModuleExtendClassMapProvider
{
    /**
     * @var ModuleMetadataParser
     */
    private $parser;

    /**
     * ModuleExtendClassMap constructor.
     *
     * @param ModuleMetadataParser $parser
     */
    public function __construct(ModuleMetadataParser $parser)
    {
       $this->parser = $parser;
    }

    /**
     * @return array
     */
    public function getModuleParentClassMap()
    {
        $map = [];
        $extends = $this->parser->getChainExtendedClasses();
        foreach ($extends as $key => $value) {
            $tmp = explode("\\", $key);
            $map[] = [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => array_pop($tmp) . '_parent',
                'parentClassName' => $value,
                'namespace'       => implode("\\", $tmp)
            ];
        }

        return $map;
    }
}
