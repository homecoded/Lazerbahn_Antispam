<?php

namespace Lazerbahn\Antispam\Plugin\Api;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ShippingInformationManagement
{
    /** @var ScopeConfigInterface $config */
    protected $config;

    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
                                               $cartId,
        ShippingInformationInterface           $shippingInformation
    ) {
        $return = [$cartId, $shippingInformation];

        $isEnabled = $this->config->getValue('lazerbahn/settings/enable_module');
        if (!$isEnabled) {
            return $return;
        }

        /** @var Magento\Quote\Api\Data\AddressInterface $shippingAddress */
        $shippingAddress = $shippingInformation->getShippingAddress();
        $shippingAddress = $this->sanitizeAddress($shippingAddress);
        $billingAddress = $shippingInformation->getBillingAddress();
        $billingAddress = $this->sanitizeAddress($billingAddress);

        $shippingInformation->setShippingAddress($shippingAddress);
        $shippingInformation->setBillingAddress($billingAddress);

        return $return;
    }


    /**
     * @param Magento\Quote\Api\Data\AddressInterface $shippingAddress
     * @return Magento\Quote\Api\Data\AddressInterface
     */
    function sanitizeAddress($shippingAddress)
    {
        $data = $shippingAddress->getData();
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
            $shippingAddress->setData([
                'firstname' => '',
                'lastname' => '',
                'company' => '',
                'city' => ''
            ]);
        }
        return $shippingAddress;
    }

}
