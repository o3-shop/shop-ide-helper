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

namespace OxidEsales\EshopIdeHelper\tests\Integration;

use OxidEsales\Facts\Facts;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;
use \OxidEsales\UnifiedNameSpaceGenerator\Exceptions\OutputDirectoryValidationException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Webmozart\PathUtil\Path;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;

/**
 * Class GeneratorTest
 *
 * @package OxidEsales\EshopIdeHelper\tests\Unit
 */
class GeneratorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $vfsStreamDirectory = null;

    const ROOT_DIRECTORY = 'root';

    /**
     * @return array
     */
    public function providerClassMaps()
    {
        return [
            /**
             * Matching class maps, testing abstract, interface and class
             */
            ['Valid']
        ];
    }

    /**
     * @dataProvider providerClassMaps
     *
     * @param string $testCaseFolder
     */
    public function testGenerateValidCases($testCaseFolder)
    {
        $pathToUnifiedNameSpaceClassMap = Path::join($this->getPathToTestData(), $testCaseFolder, "UnifiedNameSpaceClassMap.php");
        $pathToBackwardsCompatibilityClassMap = Path::join($this->getPathToTestData(), $testCaseFolder, "BackwardsCompatibilityClassMap.php");
        $pathToIdeHelperOutput = Path::join($this->getPathToTestData(), $testCaseFolder, ".ide-helper.php");
        $pathToModuleExtendClassMap = Path::join($this->getPathToTestData(), 'Valid', "ModuleExtendClassMap.php");

        $generator = new \OxidEsales\EshopIdeHelper\Generator(
            $this->getFactsMock(0777),
            $this->getUnifiedNameSpaceClassMapProviderMock($pathToUnifiedNameSpaceClassMap),
            $this->getBackwardsCompatibilityClassMapProviderMock($pathToBackwardsCompatibilityClassMap),
            $this->getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap)
        );
        $generator->generate();

        $this->assertFileEquals($pathToIdeHelperOutput, Path::join($this->getVirtualOutputDirectory() , '.ide-helper.php'));
    }

    public function testGenerateOutputFileCanNotBeWritten()
    {
        $pathToUnifiedNameSpaceClassMap = Path::join($this->getPathToTestData(), 'Valid', "UnifiedNameSpaceClassMap.php");
        $pathToBackwardsCompatibilityClassMap = Path::join($this->getPathToTestData(), 'Valid', "BackwardsCompatibilityClassMap.php");
        $pathToModuleExtendClassMap = Path::join($this->getPathToTestData(), 'Valid', "ModuleExtendClassMap.php");

        $generator = new \OxidEsales\EshopIdeHelper\Generator(
            $this->getFactsMock(0555),
            $this->getUnifiedNameSpaceClassMapProviderMock($pathToUnifiedNameSpaceClassMap),
            $this->getBackwardsCompatibilityClassMapProviderMock($pathToBackwardsCompatibilityClassMap),
            $this->getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap, 'never')
        );
        $this->expectException(OutputDirectoryValidationException::class);
        $generator->generate();
    }

    public function testGeneratePhpStormIdeHelper()
    {
        $pathToUnifiedNameSpaceClassMap = Path::join($this->getPathToTestData(), 'Valid', "UnifiedNameSpaceClassMap.php");
        $pathToBackwardsCompatibilityClassMap = Path::join($this->getPathToTestData(), 'Valid', "BackwardsCompatibilityClassMap.php");
        $pathToModuleExtendClassMap = Path::join($this->getPathToTestData(), 'Valid', "ModuleExtendClassMap.php");

        $generator = new \OxidEsales\EshopIdeHelper\Generator(
            $this->getFactsMock(0777),
            $this->getUnifiedNameSpaceClassMapProviderMock($pathToUnifiedNameSpaceClassMap),
            $this->getBackwardsCompatibilityClassMapProviderMock($pathToBackwardsCompatibilityClassMap),
            $this->getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap)
        );
        $generator->generate();

        $this->assertFileExists(Path::join($this->getVirtualOutputDirectory() , '.phpstorm.meta.php/oxid.meta.php'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Facts
     */
    private function getFactsMock($permissionsForShopRootPath)
    {
        $factsMock = $this->getMockBuilder(Facts::class)
            ->setMethods(['getShopRootPath'])
            ->getMock();
        $factsMock->expects($this->any())
            ->method('getShopRootPath')
            ->will($this->returnValue($this->getVirtualOutputDirectory($permissionsForShopRootPath)));
        return $factsMock;
    }

    /**
     * @param string $pathToUnifiedNameSpaceClassMap
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|UnifiedNameSpaceClassMapProvider
     */
    private function getUnifiedNameSpaceClassMapProviderMock($pathToUnifiedNameSpaceClassMap)
    {
        $unifiedNamespaceClassMap = include $pathToUnifiedNameSpaceClassMap;

        $unifiedNameSpaceClassMapProviderMock = $this->getMockBuilder(UnifiedNameSpaceClassMapProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['getClassMap'])
            ->getMock();
        $unifiedNameSpaceClassMapProviderMock->expects($this->any())
            ->method('getClassMap')
            ->will($this->returnValue($unifiedNamespaceClassMap));

        return $unifiedNameSpaceClassMapProviderMock;
    }

    /**
     * @param string $pathToBackwardsCompatibilityClassMap
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|BackwardsCompatibilityClassMapProvider
     */
    private function getBackwardsCompatibilityClassMapProviderMock($pathToBackwardsCompatibilityClassMap)
    {
        $backwardsCompatibilityClassMap = include $pathToBackwardsCompatibilityClassMap;

        $backwardsCompatibilityClassMapProviderMock = $this->getMockBuilder(BackwardsCompatibilityClassMapProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['getClassMap'])
            ->getMock();
        $backwardsCompatibilityClassMapProviderMock->expects($this->once())
            ->method('getClassMap')
            ->will($this->returnValue(array_flip($backwardsCompatibilityClassMap)));

        return $backwardsCompatibilityClassMapProviderMock;
    }

    /**
     * @param string $pathToModuleExtendClassMap
     * @param string $expectationMethod
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ModuleExtendClassMapProvider
     */
    private function getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap, $expectationMethod = 'once')
    {
        $moduleExtendClassMap = include $pathToModuleExtendClassMap;

        $moduleExtendClassMapProviderMock = $this->getMockBuilder(ModuleExtendClassMapProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['getModuleParentClassMap'])
            ->getMock();
        $moduleExtendClassMapProviderMock->expects($this->$expectationMethod())
            ->method('getModuleParentClassMap')
            ->will($this->returnValue($moduleExtendClassMap));

        return $moduleExtendClassMapProviderMock;
    }

    private function getPathToTestData()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'testData' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get path to virtual output directory.
     *
     * @param int   $permissions Directory permissions
     * @param array $structure   Optional directory structure
     *
     * @return string
     */
    private function getVirtualOutputDirectory($permissions = 0777, $structure = null)
    {
        if (!is_array($structure)) {
            $structure = [];
        }

        vfsStream::create($structure, $this->getVfsStreamDirectory());
        $directory = $this->getVfsRootPath();
        chmod($directory, $permissions);

        return $directory;
    }

    /**
     * Test helper.
     * Getter for vfs stream directory.
     *
     * @return vfsStreamDirectory
     */
    private function getVfsStreamDirectory()
    {
        if (is_null($this->vfsStreamDirectory)) {
            $this->vfsStreamDirectory = vfsStream::setup(self::ROOT_DIRECTORY);
        }

        return $this->vfsStreamDirectory;
    }

    /**
     * Returns the root url. It should be treated as usual file path.
     *
     * @return string
     */
    private function getVfsRootPath()
    {
        return vfsStream::url(self::ROOT_DIRECTORY) . DIRECTORY_SEPARATOR;
    }

}
