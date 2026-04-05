<?php

function acore_soap_options_from_config() {
    if (!defined('SOAP_HOST') || !defined('SOAP_PORT') || !defined('SOAP_URI')) {
        return null;
    }
    $host = trim((string)SOAP_HOST);
    $port = (int)SOAP_PORT;
    $uri = trim((string)SOAP_URI);
    $user = defined('SOAP_USER') ? trim((string)SOAP_USER) : '';
    $pass = defined('SOAP_PASS') ? (string)SOAP_PASS : '';
    if ($host === '' || $port < 1 || $port > 65535 || $user === '' || $pass === '') {
        return null;
    }
    $location = 'http://' . $host . ':' . $port . '/';
    return [
        'location' => $location,
        'uri' => $uri,
        'login' => $user,
        'password' => $pass,
        'connection_timeout' => 8,
        'cache_wsdl' => 0,
    ];
}

function acore_soap_execute_command($command) {
    $opts = acore_soap_options_from_config();
    if ($opts === null) {
        return ['ok' => false, 'error' => 'config', 'result' => null];
    }
    try {
        $command = mb_convert_encoding((string)$command, 'UTF-8', mb_detect_encoding((string)$command, null, true) ?: 'UTF-8');
        $client = new SoapClient(null, $opts);
        $param = new SoapParam($command, 'command');
        $result = $client->executeCommand($param);
        return ['ok' => true, 'error' => null, 'result' => $result];
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage(), 'result' => null];
    }
}
