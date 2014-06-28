<?php namespace SimpleUPS\Track\SmallPackage;

use \SimpleUPS\Api\MissingParameterException;
use \SimpleUPS\UPS;

/**
 * @internal
 */
class Request extends \SimpleUPS\Track\Request
{

    private
        /* @var string $trackingNumber */
        $trackingNumber;

    public function __construct($debug = null)
    {
        parent::__construct($debug);

        $this->responseClass = '\SimpleUPS\Track\SmallPackage\Response';
    }

    /**
     * Build the validate address request
     * @return string
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public function buildXml()
    {
        if ($this->getTrackingNumber() === null) {
            throw new MissingParameterException('Tracking lookup requires either a tracking number');
        }

        $dom = new \DomDocument('1.0');
        $dom->formatOutput = $this->getDebug();
        $dom->appendChild($trackingRequest = $dom->createElement('TrackRequest'));
        $addressRequestLang = $dom->createAttribute('xml:lang');
        $addressRequestLang->value = parent::getXmlLang();
        $trackingRequest->appendChild($request = $dom->createElement('Request'));
        $request->appendChild($transactionReference = $dom->createElement('TransactionReference'));
        $transactionReference->appendChild($dom->createElement('CustomerContext', $this->getCustomerContext()));

        $request->appendChild($dom->createElement('RequestAction', 'Track'));
        $request->appendChild($dom->createElement('RequestOption', '1'));

        $trackingRequest->appendChild($shipmentType = $dom->createElement('ShipmentType'));
        $shipmentType->appendChild(
            $shipmentType = $dom->createElement('Code', \SimpleUPS\Track\Request::TYPE_SMALL_PACKAGE)
        );

        if ($this->getTrackingNumber() !== null) {
            $trackingRequest->appendChild($dom->createElement('TrackingNumber', $this->getTrackingNumber()));
        }

        $xml = parent::buildAuthenticationXml() . $dom->saveXML();

        return $xml;
    }

    /**
     * @param string $trackingNumber
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }
}