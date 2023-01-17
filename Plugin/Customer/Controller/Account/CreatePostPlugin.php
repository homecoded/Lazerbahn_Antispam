<?php

namespace Lazerbahn\Antispam\Plugin\Customer\Controller\Account;

use Magento\Framework\App\Config\ScopeConfigInterface;

class CreatePostPlugin
{
    /** @var ScopeConfigInterface $config */
    protected $config;

    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function aroundExecute(\Magento\Customer\Controller\Account\CreatePost $subject, callable $proceed)
    {
        $isEnabled = $this->config->getValue('lazerbahn/settings/enable_module');
        if (!$isEnabled) {
            return $proceed();
        }

        $postData = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\RequestInterface')
            ->getPost();

        $forbiddenStringsData = $this->config->getValue('lazerbahn/settings/invalid_strings');
        $forbiddenStrings = explode(PHP_EOL, $forbiddenStringsData);

        if (!$forbiddenStrings) {
            return $proceed();
        }

        $formFieldsToCheck = array(
            'firstname',
            'lastname'
        );

        $isSpam = false;

        foreach ($forbiddenStrings as $entry) {
            $entry = trim($entry);
            foreach ($formFieldsToCheck as $field) {
                if (strpos($postData[$field], $entry) !== false) {
                    $isSpam = true;
                    break;
                }
            }
            if ($isSpam) break;
        }

        if (!$isSpam) {
            return $proceed();
        }
    }

}
