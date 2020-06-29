<?php
/**
 * Copyright © Hampus Westman 2020
 * See LICENCE provided with this module for licence details
 *
 * @author     Hampus Westman <hampus.westman@gmail.com>
 * @copyright  Copyright (c)  {year} Hampus Westman
 * @license    MIT License https://opensource.org/licenses/MIT
 * @link       https://github.com/Pr00xxy
 *
 */

namespace PrOOxxy\AdminSecurity\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Prophecy\Argument;

class ConfigTest extends TestCase
{

    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    public function setup()
    {
        parent::setUp();

        $this->objectManager = new ObjectManager($this);
    }

    /**
     * @test
     * @testdox Email restriction should return false if disabled
     */
    public function emailRestrictionEnabled()
    {
        $configMock = $this->prophesize(ScopeConfigInterface::class);
        $configMock->getValue('prooxxy/adminsecurity/is_email_restriction_active', Argument::any())->willReturn(1);
        $this->assertTrue($this->getTestClass(['config' => $configMock->reveal()])->isEmailRestrictionsActive());
    }

    /**
     * @test
     * @testdox Email restriction should return true if enabled
     */
    public function emailRestrictionDisabled()
    {
        $configMock = $this->prophesize(ScopeConfigInterface::class);
        $configMock->getValue('prooxxy/adminsecurity/is_email_restriction_active', Argument::any())->willReturn(0);

        $this->assertFalse($this->getTestClass(['config' => $configMock->reveal()])->isEmailRestrictionsActive());
    }

    /**
     * @test
     * @testdox Function should return array of valid regex patterns
     * @dataProvider emailMatchesDataProvider
     */
    public function getAllowedEmailMatches(string $input, array $output)
    {
        $configMock = $this->prophesize(ScopeConfigInterface::class);
        $configMock->getValue('prooxxy/adminsecurity/email_matches', Argument::any())->willReturn($input);
        $class = $this->getTestClass(['config' => $configMock->reveal()]);

        $this->assertEquals($output, $class->getAllowedEmailMatches());
    }

    public function emailMatchesDataProvider(): array
    {
        return [
            'Remove one invalid regex' => [
                'input' => "*_admin@gmail.com,this_that@*.test,value@gmail.com,invalid/@gmail.com",
                'output' => ['*_admin@gmail.com', 'this_that@*.test','value@gmail.com']
            ]
        ];
    }

    private function getTestClass(array $dependencies): Config
    {
        return $this->objectManager->getObject(Config::class, $dependencies);
    }
}
