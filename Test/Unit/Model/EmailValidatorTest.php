<?php
/**
 * Copyright © Hampus Westman 2020
 * See LICENCE provided with this module for licence details
 *
 * @author     Hampus Westman <hampus.westman@gmail.com>
 * @copyright  Copyright (c) 2020 Hampus Westman
 * @license    MIT License https://opensource.org/licenses/MIT
 * @link       https://github.com/Pr00xxy
 *
 */

namespace PrOOxxy\AdminSecurity\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PrOOxxy\AdminSecurity\Model\Config;

class EmailValidatorTest extends TestCase
{

    /**
     * @var $objectManager
     */
    private $objectManager;

    /**
     * @var $model \PrOOxxy\AdminSecurity\Model\EmailValidator
     */
    private $model;

    private $config;

    public function setup()
    {
        parent::setUp();

        $this->objectManager = new ObjectManager($this);

        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllowedEmailMatches', 'isEmailRestrictionsActive'])
            ->getMock();

        $this->model = $this->objectManager->getObject(
            \PrOOxxy\AdminSecurity\Model\EmailValidator::class,
            [
                'config' => $this->config
            ]
        );
    }

    /**
     * @test
     * @dataProvider domainDataProvider
     */
    public function isValid(bool $result, string $email, array $domains): void
    {

        $this->config->method('getAllowedEmailMatches')->willReturn($domains);
        $this->config->method('isEmailRestrictionsActive')->willReturn(true);
        $this->assertEquals($result, $this->model->isValid($email));

    }

    /**
     * @test
     * @dataProvider domainDataProvider
     * @testdox Always return true if the module is turned off
     */
    public function isValidWithModuleTurnedOff(bool $result, string $email, array $domains): void
    {
        $this->config->method('isEmailRestrictionsActive')->willReturn(false);
        $this->assertEquals(true, $this->model->isValid($email));
    }

    public function domainDataProvider(): array
    {

        return [
            'wildcard partial domain allowed' => ['result' => true, 'email' => 'something@domain.test', 'domains' => ['*@*.test']],
            'wildcard full domain allowed' => ['result' => true, 'email' => 'something@test.com', 'domains' => ['*@*.*']],
            'strict not allowed' => ['result' => false, 'email' => 'something@not_strict.com', 'domains' => ['*@strict.com']],
            'no domains blocked' => ['result' => true, 'email' => 'something@anything.com', 'domains' => []],
            'literal allowed' => ['result' => true, 'email' => 'local@domain.com', 'domains' => ['local@domain.com']],
            'literal blocked' => ['result' => false, 'email' => 'local@domain.se', 'domains' => ['local@domain.com']],
            'strict local allowed' => ['result' => true, 'email' => 'first_last@anything.com', 'domains' => ['*_*@anything.com']],
            'strict local blocked' => ['result' => false, 'email' => 'something@anything.com', 'domains' => ['*_*@anything.com']]
        ];
    }
}
