<?php namespace SimpleUPS;

/**
 * @internal
 */
class Model
{

    private $isResponse = false;

    /**
     * @internal
     */
    public function setIsResponse()
    {
        $this->isResponse = true;
    }

    /**
     * @internal
     */
    public function isResponse()
    {
        return $this->isResponse;
    }
}