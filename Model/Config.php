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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Config
{

    protected const ALLOWED_EMAIL_MATCHES_CONFIG_PATH = 'prooxxy/adminsecurity/email_matches';
    protected const IS_EMAIL_RESTRICTIONS_ACTIVE_CONFIG_PATH = 'prooxxy/adminsecurity/is_email_restriction_active';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function isEmailRestrictionsActive(): bool
    {
        return (bool) $this->config->getValue(self::IS_EMAIL_RESTRICTIONS_ACTIVE_CONFIG_PATH, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function getAllowedEmailMatches(): array
    {
        $rawValue = $this->config->getValue(self::ALLOWED_EMAIL_MATCHES_CONFIG_PATH, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        $array = \explode(',', $rawValue);

        foreach ($array as $key => $pattern) {
            $expression = '/' . \trim(\str_replace('*', '(.*?)', \str_replace('.', '\.', $pattern))) . '/';
            if (@preg_match($expression, "") === false) {
                $this->logger->warning(sprintf("%s is not a valid regex pattern, Removing from the pattern array", $expression));
                array_remove($array, $key);
            }
        }

        return $array;
    }
}
