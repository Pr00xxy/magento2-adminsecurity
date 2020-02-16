<?php
/**
 *
 * EmailValidatorTest.php
 *
 * This file is part of Foobar.
 *
 * AdminSecurity is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * AdminSecurity is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AdminSecurity.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @category   Pr00xxy
 * @package    AdminSecurity
 * @author     Hampus Westman <hampus.westman@gmail.com>
 * @copyright  Copyright (c) 2020 Hampus Westman
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GPLv3.0
 *
 */

namespace PrOOxxy\AdminSecurity\Test\Model;

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
            ->setMethods(['getAllowedEmailMatches'])
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
    public function isDomainAllowed(bool $result, string $email, array $domains): void
    {

        $this->config->method('getAllowedEmailMatches')->willReturn($domains);
        $this->assertEquals($result, $this->model->isDomainAllowed($email));

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
