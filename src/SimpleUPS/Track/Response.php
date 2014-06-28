<?php namespace SimpleUPS\Track;

use SimpleUPS\Shipment;

/**
 * @internal
 */
abstract class Response extends \SimpleUPS\Api\Response
{

    private
        /* @var Shipment $shipments */
        $shipments;

    /**
     * @param Shipment $shipment
     *
     * @return Response
     */
    public function addShipment(Shipment $shipment)
    {
        if ($this->shipments === null) {
            $this->shipments = array();
        }

        $this->shipments[] = $shipment;
        return $this;
    }

    /**
     * @return Shipment[]|null
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    abstract public function fromXml(\SimpleXMLElement $xml);
}