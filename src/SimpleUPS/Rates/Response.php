<?php namespace SimpleUPS\Rates;

class Response extends \SimpleUPS\Api\Response
{
    private
        /* @var ShippingMethod[] $shippingMethods */
        $shippingMethods;

    /**
     * @internal
     *
     * @param ShippingMethod $shippingMethod
     *
     * @return Response
     */
    public function addShippingMethod(ShippingMethod $shippingMethod)
    {
        if ($this->shippingMethods === null) {
            $this->shippingMethods = array();
        }

        $this->shippingMethods[] = $shippingMethod;
        return $this;
    }

    /**
     * @return ShippingMethod[]|null
     */
    public function getShippingMethods()
    {
        return $this->shippingMethods;
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Response
     */
    public function fromXml(\SimpleXMLElement $xml)
    {
        foreach ($xml->RatedShipment as $ratedShipment) {
            $this->addShippingMethod(ShippingMethod::fromXml($ratedShipment));
        }

        return $this;
    }
}