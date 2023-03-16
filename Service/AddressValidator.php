<?php

namespace Lazerbahn\Antispam\Service;

use Lazerbahn\Antispam\Exception\IllegalStringException;
use Magento\Quote\Api\Data\AddressInterface;

class AddressValidator
{
    /** @var StringArrayValidator $stringArrayValidator */
    protected $stringArrayValidator;

    public function __construct(StringArrayValidator $stringArrayValidator)
    {
        $this->stringArrayValidator = $stringArrayValidator;
    }

    /** @throws IllegalStringException */
    public function validate(AddressInterface $address): AddressInterface
    {
        $this->stringArrayValidator->validate($address->getData());

        return $address;
    }
}
