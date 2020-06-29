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
declare(strict_types=1);

namespace PrOOxxy\AdminSecurity\Plugin\Model;

use PrOOxxy\AdminSecurity\Model\EmailValidator;
use PrOOxxy\AdminSecurity\Model\EmailValidatorFactory;
use PrOOxxy\AdminSecurity\Model\ValidationPatternProvider;
use Psr\Log\LoggerInterface;
use Zend_Validate_Regex;

class UserValidationRulesPlugin
{

    /**
     * @var EmailValidatorFactory
     */
    private $emailValidatorFactory;

    /**
     * @var ValidationPatternProvider
     */
    private $patternProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EmailValidatorFactory $emailValidatorFactory,
        ValidationPatternProvider $patternProvider,
        LoggerInterface $logger
    ) {
        $this->emailValidatorFactory = $emailValidatorFactory;
        $this->patternProvider = $patternProvider;
        $this->logger = $logger;
    }

    public function afterAddUserInfoRules(
        \Magento\User\Model\UserValidationRules $subject, \Magento\Framework\Validator\DataObject $validator
    ): \Magento\Framework\Validator\DataObject
    {

        $emailFormatValidity = $this->getEmailValidator();

        $validator->addRule($emailFormatValidity, 'email');

        return $validator;
    }

    public function afterAddPasswordRules(
        \Magento\User\Model\UserValidationRules $subject,
        \Magento\Framework\Validator\DataObject $validator
    ): \Magento\Framework\Validator\DataObject
    {

        $passwordComplexity = $this->patternProvider->getPasswordValidationPattern();

        if ($passwordComplexity === null) {
            $this->logger->warning('Regexp pattern for validating admin user password is not valid');
            return $validator;
        }

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
