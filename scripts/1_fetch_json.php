<?php
// layer list https://eland.cpami.gov.tw/geoserver/ows?service=wfs&version=1.0.0&request=GetCapabilities
// get json https://eland.cpami.gov.tw/geoserver/ows?service=wfs&version=1.0.0&request=getFeature&srsName=EPSG:4326&outputFormat=application/json&typeName=postgis%3Afu01006
$listFile = dirname(__DIR__) . '/list.xml';
if(!file_exists($listFile)) {
    $xml = exec("curl 'https://eland.cpami.gov.tw/geoserver/ows?service=wfs&version=1.0.0&request=GetCapabilities' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: en-US,en;q=0.5' --compressed -H 'Connection: keep-alive' -H 'Upgrade-Insecure-Requests: 1' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache'");
    file_put_contents($listFile, $xml);
    $xml = simplexml_load_string($xml);
} else {
    $xml = simplexml_load_file($listFile);
}
foreach($xml->FeatureTypeList->FeatureType AS $ft) {
    $jsonFile = dirname(__DIR__) . '/json/' . (string)$ft->Title . '.json';
    if(!file_exists($jsonFile)) {
        $url = urlencode((string)$ft->Name);
        $json = exec("curl 'https://eland.cpami.gov.tw/geoserver/ows?service=wfs&version=1.0.0&request=getFeature&srsName=EPSG:4326&outputFormat=application/json&typeName={$url}' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: en-US,en;q=0.5' --compressed -H 'Connection: keep-alive' -H 'Upgrade-Insecure-Requests: 1' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache'");
        file_put_contents($jsonFile, $json);
    } else {
        $check = exec('/usr/bin/jsonlint-php ' . $jsonFile);
        if(trim(substr($check, 0, 5)) !== 'Valid') {
            unlink($jsonFile);
            $json = exec("curl 'https://eland.cpami.gov.tw/geoserver/ows?service=wfs&version=1.0.0&request=getFeature&srsName=EPSG:4326&outputFormat=application/json&typeName={$url}' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: en-US,en;q=0.5' --compressed -H 'Connection: keep-alive' -H 'Upgrade-Insecure-Requests: 1' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache'");
            file_put_contents($jsonFile, $json);
        }
    }
}

