<?php namespace SimpleUPS\Api;


/**
 * A request either was not made, or an undesired response from the UPS API was received (ie - 500 Internal Server Error).
 * @since 1.0
 */
class RequestFailedException extends \Exception
{


}