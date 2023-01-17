<?php

namespace Lazerbahn\Antispam\Plugin\Api;

use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Setup\Exception;

class GuestPaymentInformationManagement
{
    /** @var ScopeConfigInterface $config */
    protected $config;

    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
                                                                         $cartId,
                                                                         $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $return = [$cartId, $email, $paymentMethod, $billingAddress];

        $isEnabled = $this->config->getValue('lazerbahn/settings/enable_module');
        if (!$isEnabled) {
            return $return;
        }

        /** @var Magento\Quote\Api\Data\AddressInterface $billingAddress */
        $this->sanitizeAddress($billingAddress);
        return $return;
    }


    /**
     * @param Magento\Quote\Api\Data\AddressInterface $address
     * @return Magento\Quote\Api\Data\AddressInterface
     */
    function sanitizeAddress($address)
    {
        $data = $address->getData();
        $forbiddenStringsData = $this->config->getValue('lazerbahn/settings/invalid_strings');
        $forbiddenStrings = explode(PHP_EOL, $forbiddenStringsData);

        $isSpam = false;

        foreach ($forbiddenStrings as $entry) {
            $entry = trim($entry);
            if ($isSpam){
                break;
            }
            foreach ($data as $field) {
                if (strpos($field, $entry) !== false) {
                    $isSpam = true;
                    break;
                }
            }
        }
        if ($isSpam)  {
            throw new Exception('Billing address is not set.');
        }
        return $address;
    }

}
