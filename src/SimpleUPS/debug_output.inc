<?php

use SimpleUPS\UPS;

$requestXml = UPS::$request->buildXml();
$responseXml = UPS::$response;

/*
 *  ---------------- Browser display ----------------
 */

echo '
<style type="text/css">
a.simpleupslink { color: #0055AB }
a.simpleupslink:hover { color: #3772AD }
span.simpleupsXmlTag { color: #4c4cbf }
span.simpleupsXmlAttribute { color: #008080 }
span.simpleupsXmlAttributeValue { color: #d14 }
#simpleUpsDebug p { margin: 4px 0; }
</style>
<fieldset id="simpleUpsDebug" style="font-family: Arial; font-size: 80%; border: 1px solid #ccc; background-color: #FCFCFC; padding: 1em; color: #3B3B3B">
    <legend style="margin: 0 auto; font-weight: bold">SimpleUPS Debug Output</legend>
    <div style="float: right;">
        <img src="http://simpleups.io/images/icons/download.png"/> Download Debug Data:
        <a href="#" class="simpleupslink" onClick="downloadRawData(); return false;" title="Debug data will include UPS API authentication information (recommended if this is being sent to UPS)">Raw</a> &middot;
        <a href="#" class="simpleupslink" onClick="downloadSafeData(); return false;" title="Debug data will NOT include UPS API authentication information">Safe</a>
    </div>
    <p><strong>PHP Version:</strong> ' . PHP_VERSION . '</p>
    <p><strong>Request URL:</strong> ' . UPS::$request->getUrl() . ' ' . (strstr(
        UPS::$request->getUrl(),
        'wwwcie'
    ) ? '<span style="color: #CC7700;">(debug environment, NY and CA addresses only)</span>' : '') . '</p>
    <div style="height: 1px; background-color: #ededed; margin-top: .5em"></div>
    <div style="width:48%; display: inline-block; margin-top: .5em">
        <strong>Request:</strong> <div style="height: 350px; overflow: scroll; color: #000"><pre>' . highlightXml(
        $requestXml
    ) . '</pre></div>
    </div>
    <div style="width:48%; display: inline-block; margin-top: .5em; margin-left: 2em">
        <strong>Response*:</strong> <div style="height: 350px; overflow: scroll; color: #000"><pre>' . highlightXml(
        $responseXml
    ) . '</pre></div>
    </div>
    <p style="font-size: 80%; font-style:italic; margin-bottom: 0">Note: You should remove the authentication details in the request XML above if providing the request XML to anyone other than <a href="http://www.ups.com/content/us/en/resources/techsupport/index.html?WT.svl=SubNav" class="simpleupslink" target="_blank">UPS Developer support</a> or <a href="http://www.simpleups.io/support" class="simpleupslink" target="_blank">SimpleUPS support</a>.</p>
    <p style="font-size: 80%; font-style:italic; margin-bottom: 0">*SimpleUPS is designed to make the UPS API easier to use and therefore does not necessarily provide access to all XML data above.  Should you need access to this data, please contact support or extend the library.</p>
</fieldset>';

/*
 *  ---------------- Downloadable data files ----------------
 */

$currentDateTime = new \DateTime();
$dateGenerated = $currentDateTime->format("Y-m-d H:i:s");
$dataFileContents = 'This file contains debug data for communication with the UPS XML API.

Generated: ' . $dateGenerated . '

OS: ' . PHP_OS . '
PHP Version: ' . PHP_VERSION . '
Server API: ' . PHP_SAPI . '
Loaded Extensions: ' . implode(',', get_loaded_extensions(false)) . '

Request URL: ' . UPS::$request->getUrl() . '

-------------------- Request XML --------------------
' . $requestXml . '

-------------------- Response XML --------------------
' . $responseXml . '

';

echo '
<script type="text/javascript">
    window.URL = window.URL || window.webkitURL;

    var data = ' . json_encode($dataFileContents) . ',
        safeData = null;

    function downloadData(data, filename) {
        var a = document.createElement("a"),
            blob = new Blob([data], {
                "type": "application\/octet-stream"
            });

        a.href = window.URL.createObjectURL(blob);
        a.download = filename;
        a.click();
    }

    function downloadRawData() {
        downloadData(data, "UPS_API_Debug-raw[' . $dateGenerated . '].txt");
    };

    function downloadSafeData() {
        if (safeData == null) {
            safeData = data
                .replace(new RegExp("<AccessLicenseNumber>(.+?)</AccessLicenseNumber>"), "<AccessLicenseNumber>*******</AccessLicenseNumber>")
                .replace(new RegExp("<UserId>(.+?)</UserId>"), "<UserId>*******</UserId>")
                .replace(new RegExp("<Password>(.+?)</Password>"), "<Password>*******</Password>");
        }

        downloadData(safeData, "UPS_API_Debug-safe[' . $dateGenerated . '].txt");
    };
</script>
';

function highlightXml($xml)
{
    $xml = str_replace(
        array(
            '>',
            '<',
            'version="1.0"'
        ),
        array(
            '&gt;',
            '&lt;',
            '<span class="simpleupsXmlAttribute">version=</span><span class="simpleupsXmlAttributeValue">"1.0"</span>'
        ),
        $xml
    );

    $replacements = array(
        '#(&lt;.+?&gt;)#i' => '<span class="simpleupsXmlTag">$1</span>'
    );

    $xml = preg_replace(array_keys($replacements), array_values($replacements), $xml);

    return $xml;
}