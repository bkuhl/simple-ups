<?php namespace SimpleUPS\Rates;

/**
 * A shipment is made up of 1 or more packages
 * @since 1.0
 */
class Shipment extends \SimpleUPS\Shipment
{
    private
        /* @var bool $documentsOnly */
        $documentsOnly = false;

    /**
     * Indicate that this shipment contains only documents
     */
    public function documentsOnly()
    {
        $this->documentsOnly = true;
    }
}