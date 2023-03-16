<?php

namespace Lazerbahn\Antispam\Plugin\Api;

use Lazerbahn\Antispam\Exception\IllegalStringException;
use Lazerbahn\Antispam\Service\AddressValidator;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Setup\Exception;

class GuestPaymentInformationManagement
{
    /** @var AddressValidator $addressSanitizer */
    protected $addressSanitizer;

    /** @var ScopeConfigInterface $config */
    protected $config;

    public function __construct(AddressValidator $addressSanitizer, ScopeConfigInterface $config)
    {
        $this->addressSanitizer = $addressSanitizer;
        $this->config = $config;
    }

    /** @throws Exception */
    public function beforeSavePaymentInformation(
        GuestPaymentInformationManagementInterface $subject,
                                                   $cartId,
                                                   $email,
        PaymentInterface                           $paymentMethod,
        AddressInterface                           $billingAddress = null
    ): array
    {
        $return = [$cartId, $email, $paymentMethod, $billingAddress];

        if (is_null($billingAddress) || !$this->config->isSetFlag('lazerbahn/settings/enable_module')) {
            return $return;
        }

        try {
            $this->addressSanitizer->validate($billingAddress);
        } catch (IllegalStringException $exception) {
            throw new Exception('Billing address is not set.');
        }

        return $return;
    }
}
