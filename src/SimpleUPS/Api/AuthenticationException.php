<?php namespace SimpleUPS\Api;


/**
 * The library was successfully able to communicate with the UPS API, and the
 * API determined that the authentication information provided is invalid.
 * @see   \SimpleUPS\UPS::setAuthentication()
 * @since 1.0
 */
class AuthenticationException extends \Exception
{


}