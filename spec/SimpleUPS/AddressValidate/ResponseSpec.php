<?php

namespace spec\SimpleUPS\AddressValidate;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleUPS\AddressValidate\Address;

class ResponseSpec extends ObjectBehavior
{
    function let(Address $address)
    {
        $this->beConstructedWith($address);
    }

    function it_populates_valid_response_from_xml()
    {
        $xml = new \SimpleXMLElement(file_get_contents(__DIR__.'/fixtures/AddressValidateResponse-Valid.xml'));

        $this->isAddressValid()->shouldReturn(null);
        $this->getCorrectedAddress()->shouldReturn(null);

        $this->fromXml($xml);

        $this->isAddressValid()->shouldReturn(true);
        $this->getCorrectedAddress()->shouldReturnAnInstanceOf('SimpleUPS\AddressValidate\Address');
    }
}
