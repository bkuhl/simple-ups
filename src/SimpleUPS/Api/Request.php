<?php namespace SimpleUPS\Api;

use SimpleUPS\UPS;

/**
 * @internal
 */
abstract class Request
{
    const
        REQUEST_SUCCESSFUL = 1,
        REQUEST_FAIL = 0;

    private
        $debug = false,
        $timeout = 4;

    protected
        $xmlLang = 'en-US',
        $xpciVersion = '1.0001',
        $customerContext = 'SimpleUPS',
        $responseClass,

        $accessLicenseNumber,
        $userId,
        $password;

    public function __construct($debug = null)
    {
        if ($debug == null) {
            $this->debug = UPS::getDebug();
        }

        $loadedExtensions = get_loaded_extensions();
        if (!in_array('curl', $loadedExtensions)) {
            throw new \Exception('CURL extension must be installed in order to use ' . UPS::$libraryName);
        }
        if (!in_array('dom', $loadedExtensions)) {
            throw new \Exception('DOM extension must be installed in order to use ' . UPS::$libraryName);
        }
        if (!in_array('SimpleXML', $loadedExtensions)) {
            throw new \Exception('SimpleXML extension must be installed in order to use ' . UPS::$libraryName);
        }
        if (!in_array('date', $loadedExtensions)) {
            throw new \Exception('Date extension must be installed in order to use ' . UPS::$libraryName);
        }
        if (version_compare(PHP_VERSION, '5.3.0') < 1) {
            throw new \Exception(UPS::$libraryName . ' requires at least PHP version 5.3');
        }
        unset($loadedExtensions);
        $this->setAccessLicenseNumber(UPS::getAccessLicenseNumber());
        $this->setUserId(UPS::getUserId());
        $this->setPassword(UPS::getPassword());

        libxml_use_internal_errors(true);
    }

    /**
     * Build the request XML
     * @return string
     */
    abstract public function buildXml();

    /**
     * Get the URL for this request
     * @return string
     */
    abstract public function getUrl();

    /**
     * Build the authentication XML for a UPS API request
     * @throws AuthenticationException
     * @return string
     */
    protected function buildAuthenticationXml()
    {
        if (strlen($this->getAccessLicenseNumber()) == 0 || $this->getAccessLicenseNumber() === null) {
            throw new AuthenticationException('Authenticated requests require an access license number');
        }

        if (strlen($this->getUserId()) == 0 || $this->getUserId() === null) {
            throw new AuthenticationException('Authenticated requests require a user id');
        }

        if (strlen($this->getPassword()) == 0 || $this->getPassword() === null) {
            throw new AuthenticationException('Authenticated requests require a password');
        }

        //@todo What about account number?

        $dom = new \DomDocument('1.0');
        $dom->formatOutput = $this->getDebug();
        $dom->appendChild($accessRequest = $dom->createElement('AccessRequest'));
        $accessRequest->appendChild($request = $dom->createElement('AccessLicenseNumber', $this->accessLicenseNumber));
        $accessRequest->appendChild($request = $dom->createElement('UserId', $this->userId));
        $accessRequest->appendChild($request = $dom->createElement('Password', $this->password));

        return $dom->saveXML();
    }

    /**
     * Perform the request and get the response XML for a given request
     * @return Response|\SimpleXMLElement Returns SimpleXMLElement when response class is omitted
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public function sendRequest()
    {
        try {
            $xml = $this->buildXml();
        } catch (\SimpleUPS\Api\MissingParameterException $e) {
            throw new \SimpleUPS\Api\MissingParameterException($e->getMessage());
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \SimpleUPS\Api\RequestFailedException('#' . curl_errno($ch) . ': ' . curl_error($ch));
        } else {
            $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($returnCode != 200) {
                throw new \SimpleUPS\Api\RequestFailedException('Request returned header: ' . $returnCode);
            }
        }

        curl_close($ch);

        if ($this->getDebug()) {
            UPS::$request = $this;

            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;

            //if xml fails to load, still record the response even if it's not formatted
            if ($dom->loadXML($result)) {
                UPS::$response = $dom->saveXML();
            } else {
                UPS::$response = $result;
            }
        }

        try {
            $xml = new \SimpleXMLElement($result);
            if ($xml === false) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw new \SimpleUPS\Api\InvalidResponseException('Unable to parse XML response');
        }

        $this->handleResponseErrors($xml);

        if ($this->responseClass === false) {
            return $xml;
        }

        $response = new $this->responseClass();
        $response->fromXml($xml);

        return $response;
    }

    /**
     * Throw any errors if needed based on the response
     *
     * @param \SimpleXML $xml
     *
     * @throws ResponseErrorException
     */
    public function handleResponseErrors(\SimpleXMLElement $xml)
    {
        if ($xml->Response->ResponseStatusCode == Request::REQUEST_FAIL) {
            throw new \SimpleUPS\Api\ResponseErrorException(
                $xml->Response->Error->ErrorDescription,
                (int)$xml->Response->Error->ErrorCode,
                (int)$xml->Response->Error->ErrorSeverity
            );
        }
    }

    protected function getCustomerContext()
    {
        return $this->customerContext;
    }

    protected function getXmlLang()
    {
        return $this->xmlLang;
    }

    protected function getXpciVersion()
    {
        return $this->xpciVersion;
    }

    public function setAccessLicenseNumber($accessLicenseNumber)
    {
        $this->accessLicenseNumber = $accessLicenseNumber;
    }

    public function getAccessLicenseNumber()
    {
        return $this->accessLicenseNumber;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function __destruct()
    {
        libxml_use_internal_errors(false);
    }
}