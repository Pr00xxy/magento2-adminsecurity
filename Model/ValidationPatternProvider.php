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

namespace PrOOxxy\AdminSecurity\Model;

use Magento\Framework\Validator\Regex;

class ValidationPatternProvider
{

    protected const PASSWORD_PATTERN = '/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%?=*&]).{8,})/';

    public function getPasswordValidationPattern(): ?Regex
    {
        return preg_match(self::PASSWORD_PATTERN, "") === false ? null : New Regex(self::PASSWORD_PATTERN);
    }

}
