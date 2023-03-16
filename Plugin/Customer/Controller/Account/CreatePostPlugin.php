<?php

namespace Lazerbahn\Antispam\Plugin\Customer\Controller\Account;

use Lazerbahn\Antispam\Exception\IllegalStringException;
use Lazerbahn\Antispam\Service\StringArrayValidator;
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CreatePostPlugin
{
    /** @var StringArrayValidator $stringArrayValidator */
    protected $stringArrayValidator;

    /** @var ScopeConfigInterface $config */
    protected $config;

    public function __construct(StringArrayValidator $stringArrayValidator, ScopeConfigInterface $config)
    {
        $this->stringArrayValidator = $stringArrayValidator;
        $this->config = $config;
    }

    public function aroundExecute(CreatePost $subject, callable $proceed)
    {
        if (!$this->config->getValue('lazerbahn/settings/enable_module')) {
            return $proceed();
        }

        $postData = [
            $subject->getRequest()->getPost('firstname'),
            $subject->getRequest()->getPost('lastname')
        ];

        try {
            $this->stringArrayValidator->validate($postData);
        } catch (IllegalStringException $exception) {
            return;
        }

        return $proceed();
    }
}
