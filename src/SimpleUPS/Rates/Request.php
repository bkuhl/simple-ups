<?php namespace SimpleUPS\Rates;

use \SimpleUPS\Api\MissingParameterException;

use \SimpleUPS\UPS;

/**
 * @internal
 * @since 1.0
 */
class Request extends \SimpleUPS\Api\Request
{
    private
        /* @var string $pickupType */
        $pickupType = PickupType::DAILY_PICKUP,
        /* @var string $rateType */
        $rateType,

        /* @var Shipment $shipment */
        $shipment;

    public function __construct()
    {
        parent::__construct();

        $this->responseClass = '\SimpleUPS\Rates\Response';
    }

    /**
     * Determine which API call will be made
     * @internal
     * @return string
     */
    public function getUrl()
    {
        return $this->getDebug() ? 'https://wwwcie.ups.com/ups.app/xml/Rate' : 'https://onlinetools.ups.com/ups.app/xml/Rate';
    }

    /**
     * Build the validate address request
     * @internal
     * @return string
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public function buildXml()
    {
        if ($this->getShipment()->getDestination() == null) {
            throw new MissingParameterException('Shipment destination is missing');
        }

        $dom = new \DomDocument('1.0');
        $dom->formatOutput = $this->getDebug();
        $dom->appendChild($ratingRequest = $dom->createElement('RatingServiceSelectionRequest'));
        $addressRequestLang = $dom->createAttribute('xml:lang');
        $addressRequestLang->value = parent::getXmlLang();
        $ratingRequest->appendChild($request = $dom->createElement('Request'));
        $request->appendChild($transactionReference = $dom->createElement('TransactionReference'));
        $transactionReference->appendChild($dom->createElement('CustomerContext', $this->getCustomerContext()));

        $request->appendChild($dom->createElement('RequestAction', 'Rate'));
        $request->appendChild(
            $dom->createElement('RequestOption', 'Shop')
        ); //@todo test with "Rate" as setting to determine difference

        $ratingRequest->appendChild($pickupType = $dom->createElement('PickupType'));
        $pickupType->appendChild($shipmentType = $dom->createElement('Code', $this->getPickupType()));

        if ($this->getRateType() != null) {
            $ratingRequest->appendChild($customerClassification = $dom->createElement('CustomerClassification'));
            $customerClassification->appendChild($dom->createElement('Code', $this->getRateType()));
        }
        // Shipment
        $shipment = $this->getShipment();
        $shipment->setShipper(UPS::getShipper());

        $ratingRequest->appendChild($shipment->toXml($dom));
        $xml = parent::buildAuthenticationXml() . $dom->saveXML();

        return $xml;
    }

    /**
     * How the shipment will be picked up
     * Default value is PickupType::DAILY_PICKUP
     * @see PickupType
     *
     * @param string $pickupType
     *
     * @throws \SimpleUPS\Api\InvalidParameterException
     * @return Request
     */
    public function setPickupType($pickupType)
    {
        $reflectionClass = new \ReflectionClass('SimpleUPS\Rates\PickupType');
        if (!in_array($pickupType, $reflectionClass->getConstants())) {
            throw new \SimpleUPS\Api\InvalidParameterException(
                'PickupType is invalid, refer to SimpleUPS\Rates\PickupType for valid pickup types'
            );
        }

        $this->pickupType = $pickupType;
        return $this;
    }

    /**
     * @return string
     */
    private function getPickupType()
    {
        return $this->pickupType;
    }

    /**
     * How the shipment will be quoted
     * Defaults:
     * <ul>
     *  <li>RateType::DAILY_RATES when pickup type is PickupType::DAILY_PICKUP</li>
     *  <li>RateType::RETAIL_RATES when pickup type is PickupType::ONE_TIME_PICKUP, PickupType::ON_CALL_AIR, PickupType::LETTER_CENTER or PickupType::AIR_SERVICE_CENTER</li>
     * </ul>
     * @see PickupType
     * @see RateType
     *
     * @param string $rateType
     *
     * @throws \SimpleUPS\Api\InvalidParameterException
     * @return Request
     */
    public function setRateType($rateType)
    {
        $reflectionClass = new \ReflectionClass('SimpleUPS\Rates\RateType');
        if (!in_array($rateType, $reflectionClass->getConstants())) {
            throw new \SimpleUPS\Api\InvalidParameterException(
                'RateType is invalid, refer to SimpleUPS\Rates\RateType for valid rate types'
            );
        }

        $this->rateType = $rateType;
        return $this;
    }

    /**
     * @return string
     */
    private function getRateType()
    {
        return $this->rateType;
    }

    /**
     * @param Shipment $shipment
     *
     * @return Request
     */
    public function setShipment(Shipment $shipment)
    {
        $this->shipment = $shipment;
        return $this;
    }

    /**
     * @return Shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }
}