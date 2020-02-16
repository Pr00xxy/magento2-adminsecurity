<?php
/**
 *
 * EmailValidator.php
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

declare(strict_types=1);

namespace PrOOxxy\AdminSecurity\Model;

use Magento\Framework\Validator\ValidatorInterface;

class EmailValidator extends \Zend_Validate_Abstract implements ValidatorInterface
{

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public const INVALID = 'regexInvalid';

    protected $_messageTemplates = [
        self::INVALID   => 'Invalid Email'
    ];

    public function isValid($email): bool
    {
        if (!$this->isDomainAllowed($email)) {
            $this->_error(self::INVALID);
            return false;
        }

        return true;
    }

    public function isDomainAllowed(string $email): bool
    {

        $domains = $this->config->getAllowedEmailMatches();

        if (empty($domains)) {
            return true;
        }

        foreach ($domains as $filter) {

            if (empty(\trim($filter))) {
                continue;
            }

            if (\strpos($filter, '@') === false) {
                continue;
            }

            $expression = '/' . \trim(\str_replace('*', '(.*?)', \str_replace('.', '\.', $filter))) . '/';
            if (\preg_match($expression, $email)) {
                return true;
            }
        }

        return false;
    }
}
