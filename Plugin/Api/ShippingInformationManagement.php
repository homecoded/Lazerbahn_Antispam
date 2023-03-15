<?php

namespace Lazerbahn\Antispam\Plugin\Api;

use Lazerbahn\Antispam\Exception\IllegalStringException;
use Lazerbahn\Antispam\Service\AddressValidator;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\AddressInterface;

class ShippingInformationManagement
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

    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
                                               $cartId,
        ShippingInformationInterface           $shippingInformation
    ): array
    {
        $return = [$cartId, $shippingInformation];

        if (!$this->config->isSetFlag('lazerbahn/settings/enable_module')) {
            return $return;
        }

        $shippingInformation->setShippingAddress(
            $this->sanitizeAddress($shippingInformation->getShippingAddress())
        );
        $shippingInformation->setBillingAddress(
            $this->sanitizeAddress($shippingInformation->getBillingAddress())
        );

        return $return;
    }


    /**
     * @param AddressInterface $address
     * @return AddressInterface
     */
    function sanitizeAddress(AddressInterface $address): AddressInterface
    {
        try {
            $this->addressSanitizer->validate($address);
        } catch (IllegalStringException $exception) {
            $address->setData([
                'firstname' => '',
                'lastname' => '',
                'company' => '',
                'city' => ''
            ]);
        }

        return $address;
    }
}
