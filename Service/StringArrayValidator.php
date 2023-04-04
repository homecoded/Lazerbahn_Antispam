<?php

namespace Lazerbahn\Antispam\Service;

use Lazerbahn\Antispam\Exception\IllegalStringException;
use Magento\Framework\App\Config\ScopeConfigInterface;

class StringArrayValidator
{
    /** @var ScopeConfigInterface $config */
    protected $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /** @throws IllegalStringException */
    public function validate(array $stringArray)
    {
        $forbiddenStringsData = $this->config->getValue('lazerbahn/settings/invalid_strings');
        $forbiddenStrings = explode(PHP_EOL, $forbiddenStringsData);

        foreach ($forbiddenStrings as $entry) {
            $entry = trim($entry);

            foreach ($stringArray as $field) {
                if (empty($field) || empty($entry)) {
                    continue;
                }
                if (strpos($field, $entry) !== false) {
                    throw new IllegalStringException('Found illegal string');
                }
            }
        }
    }
}
