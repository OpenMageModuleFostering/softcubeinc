<?php

class SoftCube_Integration_Model_Zip extends Mage_Core_Model_Abstract
{
    /** @var string */
    protected $_string = '';

    /** @var string */
    protected $_tmpFileName = 'softcube-temp.zip';

    /** @var string */
    protected $_tmpFilePath = '';

    /** @var array */
    protected $_entitiesToExtract = array(
        'bridge2cart/',
        'bridge2cart/bridge.php',
        'bridge2cart/config.php',
    );

    public function extractString($string)
    {
        umask(0);
        $this->_string = $string;
        $this->_tmpFilePath = Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . $this->_tmpFileName;

        if (!$this->_writeToTempDir()) {
            Mage::throwException(Mage::helper('core/translate')->__(sprintf('Can\'t write file to %s', $this->_tmpFilePath)));
        }


        if (!$this->_extractTmpFile()) {
            Mage::throwException(Mage::helper('core/translate')->__(sprintf('Can\'t extract zip file %s', $this->_tmpFilePath)));
        }

        $this->_removeTmpFiles();

        return true;
    }

    private function _writeToTempDir()
    {
        $fp = fopen($this->_tmpFilePath, 'wb');
        $result = @fwrite($fp, $this->_string);
        fclose($fp);
        return $result;
    }

    private function _extractTmpFile()
    {
        $zip = new ZipArchive;
        $result = $zip->open($this->_tmpFilePath);

        if (!$result) {
            return false;
        }

        $result = $zip->extractTo(Mage::getBaseDir() . '/', $this->_entitiesToExtract);

        if (!$result) {
            return false;
        }

        $zip->close();

        return true;
    }

    private function _removeTmpFiles()
    {
        @unlink($this->_tmpFilePath);
    }
}