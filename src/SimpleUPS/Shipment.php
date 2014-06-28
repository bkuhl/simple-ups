<?php namespace SimpleUPS;

use \SimpleUPS\Shipper;

use \SimpleUPS\InstructionalAddress;

/**
 * A shipment is associated with a tracking number and is made up of 1 or more packages
 * @internal
 * @since 1.0
 */
class Shipment extends Model
{

    private
        /* @var Shipper $shipper */
        $shipper,

        /* @var InstructionalAddress $destination */
        $destination,

        /* @var Service $service */
        $service,

        /* @var Package[] $packages */
        $packages;

    /**
     * @internal
     *
     * @param Shipper $shipper
     *
     * @return Shipment
     */
    public function setShipper(Shipper $shipper)
    {
        $this->shipper = $shipper;
        return $this;
    }

    /**
     * Information about the shipper
     * @return Shipper
     */
    public function getShipper()
    {
        return $this->shipper;
    }

    /**
     * @internal
     *
     * @param InstructionalAddress $destination
     *
     * @return Shipment
     */
    public function setDestination(InstructionalAddress $destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Delivery destination
     * @return InstructionalAddress
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @internal
     *
     * @param Service $service
     *
     * @return Shipment
     */
    public function setService(Service $service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Shipping service used
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @internal
     *
     * @param Package $package
     *
     * @return Shipment
     */
    public function addPackage(Package $package)
    {
        if ($this->packages === null) {
            $this->packages = array();
        }

        $this->packages[] = $package;
        return $this;
    }

    /**
     * Packages in this shipment
     * @return Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @internal
     *
     * @param \DomDocument $dom
     *
     * @return \DOMElement
     */
    public function toXml(\DomDocument $dom)
    {
        $shipment = $dom->createElement('Shipment');

        if ($this->getShipper() != null) {
            $shipment->appendChild($this->getShipper()->toXml($dom));
        }

        if ($this->getDestination() != null) {
            $shipment->appendChild($shipFrom = $dom->createElement('ShipTo'));
            $shipFrom->appendChild($this->getDestination()->toXml($dom));
        }

        if ($this->getService() != null) {
            $shipment->appendChild($this->getService()->toXml($dom));
        }

        if ($this->getPackages() != null && count($this->getPackages()) > 0) {
            foreach ($this->getPackages() as $package) {
                $shipment->appendChild($package->toXml($dom));
            }
        }

        return $shipment;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Shipment
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $shipment = new Shipment();
        $shipment->setIsResponse();

        if (isset($xml->Shipper)) {
            $shipment->setShipper(Shipper::fromXml($xml->Shipper));
        }

        if (isset($xml->ShipTo->Address)) {
            $shipment->setDestination(InstructionalAddress::fromXml($xml->ShipTo->Address));
        }

        if (isset($xml->Package)) {
            foreach ($xml->Package as $package) {
                $shipment->addPackage(Package::fromXml($package));
            }
        }

        if (isset($xml->Service)) {
            $shipment->setService(Service::fromXml($xml->Service));
        }

        return $shipment;
    }
}