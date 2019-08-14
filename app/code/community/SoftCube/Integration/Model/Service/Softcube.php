<?php

class SoftCube_Integration_Model_Service_Softcube extends SoftCube_Integration_Model_Service_Abstract
{

    const XML_PATH_SOFTCUBE_GUID = 'softcube/general/guid';

    const XML_PATH_SOFTCUBE_JS = 'softcube/general/js';

    const STORE_NAME = 'magento';

    /** @var string */
    protected $_endpointUrl = 'https://recommendapi.datasoftcube.com/staging/';

    /** @var string */
    protected $_guId = '';

    /** @var string */
    protected $_js = '';

    /** @var string */
    protected $_bridgeUrl;

    /** @var  string */
    protected $_storeKey;

    /** @var string */
    protected $_softCubeApiKey = 'EEA11385AC6D4A6BB529ED7B62BD380F';

    public function __construct()
    {
        $this->_guId = Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_GUID);
        $this->_js = Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_JS);
    }

    public function getStoreKey()
    {
        if ($this->_storeKey) {
            return $this->_storeKey;
        }

        $params = array(
            'api_key' => $this->_softCubeApiKey,
            'url' => $_SERVER['HTTP_HOST'],
            'store' => static::STORE_NAME,
        );

        $result = $this->doRequest('tenants', $params, 'PUT');

        if (is_array(json_decode($result, true))) {
            $result = json_decode($result, true);
            $this->_storeKey = $result['store_key'];
            $this->_bridgeUrl = $result['bridge_url'];
        } else {
            $this->_storeKey = $result;
        }

        $configModel = Mage::getModel('core/config');
        $configModel->saveConfig(static::XML_PATH_SOFTCUBE_STORE_KEY, $this->_storeKey);
        $configModel->cleanCache();

        return $this->_storeKey;
    }

    public function updateInformation($customParams = null)
    {
        $params = array(
            'api_key' => $this->_softCubeApiKey,
            'guid' => $this->_storeKey ? $this->_storeKey : Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_STORE_KEY),
            'name' => $customParams['name'] ? $customParams['name'] : Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_INFO_NAME),
            'email' => $customParams['email'] ? $customParams['email'] : Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_INFO_EMAIL),
            'phone' => $customParams['phone'] ? $customParams['phone'] : Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_INFO_PHONE)
        );

        if ($customParams) {
            $configModel = Mage::getModel('core/config');
            $configModel->saveConfig(static::XML_PATH_SOFTCUBE_INFO_NAME, $params['name']);
            $configModel->saveConfig(static::XML_PATH_SOFTCUBE_INFO_EMAIL, $params['email']);
            $configModel->saveConfig(static::XML_PATH_SOFTCUBE_INFO_PHONE, $params['phone']);
            $configModel->removeCache();
        }

        $this->doRequest('credentials/api2cart', $params, 'POST');

    }

    public function getBridgeUrl()
    {
        return $this->_bridgeUrl;
    }

    public function getJs()
    {
        if (!$this->_storeKey) {
            $this->_storeKey = Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_STORE_KEY);
            if (!$this->_storeKey) {
                $this->getStoreKey();
            }
        }

        if ($this->_js) {
            return $this->_js;
        }

        $params = array(
            'api_key' => $this->_softCubeApiKey
        );

        $this->_js = $this->doRequest('tenants/' . $this->_storeKey, $params);

        $configModel = Mage::getModel('core/config');
        $configModel->saveConfig(static::XML_PATH_SOFTCUBE_JS, $this->_js);
        $configModel->cleanCache();

        return $this->_js;
    }

    public function doRequest($method, $params, $httpMethod = 'GET')
    {
        $ch = curl_init();

        $url = $this->_endpointUrl;
        if ($method) {
            $url .= '/' . $method;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json',
        ));

        $response = trim(curl_exec($ch));

        if (curl_errno($ch) != CURLE_OK) {
            Mage::throwException(Mage::helper('core/translate')->__(curl_error($ch)));
        }
        curl_close($ch);

        if (!$response) {
            Mage::throwException(Mage::helper('core/translate')->__('Response is NULL'));
        }

        return $response;
    }
}