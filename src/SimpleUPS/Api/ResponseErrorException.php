<?php namespace SimpleUPS\Api;

/**
 * The UPS API responded successfully to the request, but stated
 * that it was unable to fulfill the request.  The exception description will
 * contain more detail.
 * @since 1.0
 */

class ResponseErrorException extends \ErrorException
{


}