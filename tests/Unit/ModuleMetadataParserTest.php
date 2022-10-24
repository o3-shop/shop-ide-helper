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

use \Webmozart\PathUtil\Path;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;

class ModuleMetadataParserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Success case.
     */
    public function testGetExtendedClasses()
    {
        $testData = [
            Path::join([__DIR__, 'testData', 'example_1.php']),
            Path::join([__DIR__, 'testData', 'example_2.php']),
            Path::join([__DIR__, 'testData', 'example_3.php']),
        ];

        $scanner = $this->getMockBuilder(DirectoryScanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilePaths'])
            ->getMock();
        $scanner->expects($this->any())
            ->method('getFilePaths')
            ->will($this->returnValue($testData));

        $parser = new ModuleMetadataParser($scanner);

        $expected = [
            'OxidEsales\TestModule\Core\Header'        => 'OxidEsales\Eshop\Core\Header',
            'OxidEsales\TestModule\Core\ShopControl'   => 'OxidEsales\Eshop\Core\ShopControl',
            'OxidEsales\TestModule\Core\WidgetControl' => 'OxidEsales\Eshop\Core\WidgetControl',
            'nonamespace_testmodule_header'            => 'OxidEsales\Eshop\Core\Header',
        ];
        $this->assertEquals($expected , $parser->getChainExtendedClasses());
    }

    /**
     * Success case.
     */
    public function testNoClasses()
    {
        $testData = [
            Path::join([__DIR__, 'testData', 'example_2.php']),
        ];

        $scanner = $this->getMockBuilder(DirectoryScanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilePaths'])
            ->getMock();
        $scanner->expects($this->any())
            ->method('getFilePaths')
            ->will($this->returnValue($testData));

        $parser = new ModuleMetadataParser($scanner);

        $this->assertEquals([], $parser->getChainExtendedClasses());
    }
}
