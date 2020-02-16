<?php
/**
 *
 * UserValidationRulesPlugin.php
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

namespace PrOOxxy\AdminSecurity\Plugin\Model;

use Magento\Framework\Validator\Regex;
use PrOOxxy\AdminSecurity\Model\EmailValidator;
use PrOOxxy\AdminSecurity\Model\EmailValidatorFactory;
use Zend_Validate_Regex;

class UserValidationRulesPlugin
{

    /**
     * @var EmailValidatorFactory
     */
    private $emailValidatorFactory;

    public function __construct(
        EmailValidatorFactory $emailValidatorFactory
    )
    {
        $this->emailValidatorFactory = $emailValidatorFactory;
    }

    public function afterAddUserInfoRules(\Magento\User\Model\UserValidationRules $subject, $validator)
    {
        $emailValidity = New Regex('/(.*[a-zA-Z0-9])(?:@)(.*[a-z])/');
        $emailValidity->setMessage(
            __('Our security policy does not allow the provided email. Please contact an administrator for further information'),
            EmailValidator::INVALID
        );

        $emailFormatValidity = $this->getEmailValidator();
        $emailFormatValidity->setMessage(
            __('Our security policy does not allow the provided email. Please contact an administrator for further information'),
            EmailValidator::INVALID
        );

        $validator->addRule($emailValidity, 'email');
        $validator->addRule($emailFormatValidity, 'email');

        return $validator;
    }

    public function afterAddPasswordRules(\Magento\User\Model\UserValidationRules $subject, $validator)
    {

        $passwordComplexity = new Regex('/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%?=*&]).{8,})/');
        $passwordComplexity->setMessage(
            __('Password does not meet the security requirements. Please contact an administrator for further information'),
            Zend_Validate_Regex::NOT_MATCH
        );

        $validator->addRule(
            $passwordComplexity,
            'password'
        );

        return $validator;
    }

    private function getEmailValidator(): EmailValidator
    {
        return $this->emailValidatorFactory->create();
    }
}
