<?php

class ezcWebdavTransportTestMock extends ezcWebdavTransport
{
    protected function retreiveBody()
    {
        return $GLOBALS['EZC_WEBDAV_TRANSPORT_TEST_BODY'];
    }

    protected function sendResponse( ezcWebdavOutputResult $output )
    {
        $GLOBALS['EZC_WEBDAV_TRANSPORT_TEST_RESPONSE_HEADERS'] = $output->headers;
        $GLOBALS['EZC_WEBDAV_TRANSPORT_TEST_RESPONSE_BODY']    = $output->body;
    }
}

?>
