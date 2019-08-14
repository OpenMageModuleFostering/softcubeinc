<?php

class SoftCube_Integration_Model_Service_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * URL to API API2Cart
     *
     * @var string A2C_URL
     */
    const A2C_URL = 'https://api.api2cart.com/v1.0/';

    const XML_PATH_SOFTCUBE_API_KEY = 'softcube/general/api_key';

    const XML_PATH_SOFTCUBE_STORE_KEY = 'softcube/general/store_key';

    const XML_PATH_SOFTCUBE_INFO_NAME = 'softcube/information/name';

    const XML_PATH_SOFTCUBE_INFO_EMAIL = 'softcube/information/email';

    const XML_PATH_SOFTCUBE_INFO_PHONE = 'softcube/information/phone';

    const DEFAULT_RESPONSE_FORMAT = 'json';

    const DEFAULT_FILE_FORMAT = 'file';

    protected $_fileResultMethods = array(
        'bridge.download'
    );

    /**
     * @var array $_params
     */
    protected $_params = array();

    /** @var string */
    protected $_serviceName;

    public function __construct()
    {
        $this->_params['api_key'] = Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_API_KEY);
        if ($storeKey = Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_STORE_KEY)) {
            $this->_params['store_key'] = $storeKey;
        }
    }


    /**
     * Send request to API2Cart
     *
     * @param string $method Method's name
     * @param array $params Method's params list
     * @return stdClass
     * @throws Exception
     */
    public function doRequest($method, $params = array(), $customUrl = null)
    {
        if (strpos($method, '.') === false) {
            $method = $this->_serviceName . '.' . $method;
        }

        $params = array_merge($this->_params, $params);

        $ch = curl_init();

        $format = static::DEFAULT_RESPONSE_FORMAT;

        if (in_array($method, $this->_fileResultMethods)) {
            $format = static::DEFAULT_FILE_FORMAT;
        }

        if (!$customUrl) {
            $url = self::A2C_URL . $method . '.' . $format . '?' . http_build_query($params);
        } else {
            $url = $customUrl;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; " .
            "rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: */*",
        ));

        if ($format == static::DEFAULT_FILE_FORMAT) {
            $response = curl_exec($ch);
        } else {
            $response = trim(curl_exec($ch));
        }

        if (curl_errno($ch) != CURLE_OK) {
            Mage::throwException(Mage::helper('core/translate')->__(curl_error($ch)));
        }
        curl_close($ch);
        if ($format != static::DEFAULT_FILE_FORMAT) {
            return $this->validate($response);
        } else {
            return $response;
        }
    }

    /**
     * Received response validation
     *
     * @param string $response Method's response
     * @return stdClass
     * @throws Exception
     */
    private function validate($response)
    {
        $result = json_decode($response);

        if ($result === null) {
            Mage::throwException(Mage::helper('core/translate')->__('Response is NULL'));
        }

        if ($result->return_code != 0) {
            Mage::throwException(Mage::helper('core/translate')->__($result->return_message));
        }

        return $result->result;
    }
}