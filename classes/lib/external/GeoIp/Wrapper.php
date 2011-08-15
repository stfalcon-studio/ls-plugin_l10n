<?php

require_once 'geoip.php';

/**
 * Wrapper to functions for working with geoip .dat file
 */
class GeoIp_Wrapper
{

    /**
     * Object GeoIP
     * @var GeoIP
     */
    protected $_gi;

    /**
     * Open .dat file and create object GeoIP
     *
     * @param string $filename
     * @param integer $flags
     * @return void
     */
    public function __construct($filename = null, $flags = null)
    {
        $this->_setGi(geoip_open($filename, $flags));
    }

    /**
     * Destructor
     *
     * @return boolean
     */
    public function __descruct()
    {
        return geoip_close($this->_getGi());
    }

    /**
     * Set GeoIP object
     *
     * @param GeoIP $gi
     * @return void
     */
    private function _setGi(GeoIP $gi)
    {
        $this->_gi = $gi;
    }

    /**
     * Get GeoIP object
     *
     * @return GeoIP
     */
    private function _getGi()
    {
        return $this->_gi;
    }

    /**
     * Get country code by IP address in lowecase
     *
     * @param string $addr
     * @return mixed
     */
    public function getCountryCodeByAddr($addr)
    {
        // @todo strlower с многобайтными кодировками не работает ведь... но у нас только cp1251?
        return strtolower(geoip_country_code_by_addr($this->_getGi(), $addr));
    }

}