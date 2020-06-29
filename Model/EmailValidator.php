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

class EmailValidator implements \Magento\Framework\Validator\ValidatorInterface
{

    /**
     * @var Config
     */
    private $config;

    private $messages = [];

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public const INVALID = 'regexInvalid';

    protected $messageTemplates = [
        self::INVALID   => 'Our security policy does not allow the provided email. Please contact an administrator for further information'
    ];

    public function isValid($email): bool
    {

        if (!$this->config->isEmailRestrictionsActive()) {
            return true;
        }

        if (!$this->isDomainAllowed($email)) {
            $this->addMessages([$this->messageTemplates[self::INVALID]]);
            return false;
        }

        return true;
    }

    private function isDomainAllowed(string $email): bool
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

    /**
     * Add messages
     *
     * @param array $messages
     * @return void
     */
    protected function addMessages(array $messages)
    {
        $this->messages = array_merge_recursive($this->messages, $messages);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getTranslator()
    {
        return null;
    }

    public function hasTranslator()
    {
        return false;
    }

    public function setTranslator($translator = null)
    {
        return;
    }
}
