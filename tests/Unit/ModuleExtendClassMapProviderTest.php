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

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;

class ModuleExtendClassMapProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Success case.
     */
    public function testGetClassMap()
    {
        $testData = [
            'OxidEsales\TestModule\Core\Header'        => 'OxidEsales\Eshop\Core\Header',
            'OxidEsales\TestModule\Core\ShopControl'   => 'OxidEsales\Eshop\Core\ShopControl',
            'nonamespace_testmodule_header'            => 'OxidEsales\Eshop\Core\Header',
        ];

        $parser = $this->getMockBuilder(ModuleMetadataParser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getChainExtendedClasses'])
            ->getMock();
        $parser->expects($this->any())
            ->method('getChainExtendedClasses')
            ->will($this->returnValue($testData));

        $classMap = new ModuleExtendClassMapProvider($parser);

        $expected = [
            [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => 'Header_parent',
                'parentClassName' => 'OxidEsales\\Eshop\\Core\\Header',
                'namespace'       => 'OxidEsales\\TestModule\\Core',
            ],
            [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => 'ShopControl_parent',
                'parentClassName' => 'OxidEsales\\Eshop\\Core\\ShopControl',
                'namespace'       => 'OxidEsales\\TestModule\\Core',
            ],
            [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => 'nonamespace_testmodule_header_parent',
                'parentClassName' => 'OxidEsales\\Eshop\\Core\\Header',
                'namespace'       => '',
            ]
        ];

        $this->assertEquals($expected , $classMap->getModuleParentClassMap());
    }
}
