<?php
/**
 *
 * Config.php
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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{

    private const ALLOWED_EMAIL_MATCHES_CONFIG_PATH = 'prooxxy/adminsecurity/email_matches';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function getAllowedEmailMatches(): array
    {
        $rawValue = $this->config->getValue(self::ALLOWED_EMAIL_MATCHES_CONFIG_PATH, ScopeInterface::SCOPE_WEBSITE);
        return \explode(',', $rawValue);
    }
}
