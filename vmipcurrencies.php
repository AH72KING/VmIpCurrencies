<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.VmIpCurrendies 
 * @copyright   Copyright (C) Creative Tech Solutions. All rights reserved.
 * @license     GNU General Public License version 2
 * @author      Ahsan Khan
 */

defined('JPATH_BASE') or die;

jimport('joomla.utilities.utility');

/**
 * Plugin class getting country code on basis of which currency of virturemart is selected
 *
 * @package Joomla.Plugin
 * @subpackage  System.logout
 */
class plgSystemVmIpCurrencies extends JPlugin
{
    /**
    * Object Constructor.
    *
    * @access   public
    * @param    object  The object to observe -- event dispatcher.
    * @param    object  The configuration object for the plugin.
    * @return   void
    * @since    3
    * @author  Ahsan Khan
    */
    function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }
    
    /*
     * At this trigger method load plugin settings 
     * and then set VirtueMart currency
     * 
     * @author Ahsan Khan
     */
    public function onAfterInitialise(){    
        // Check that we are in the site application.
        if (JFactory::getApplication()->isAdmin())
        {
            return true;
        }
        $app = JFactory::getApplication();
        $user_ip = $_SERVER['REMOTE_ADDR']; // to get current ip address && this will not work in localhost write static ip for that here

        $details = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$user_ip));
        $currencyCode =$details['geoplugin_currencyCode'];
	/*
	this is what is return from above url it can be used for other purpose but i am jus using currencyCode
	//$user_ip='203.101.184.7'; //pakistan's city ip address for test
	array (
	  'geoplugin_request' => '101.50.126.116',
	  'geoplugin_status' => 200,
	  'geoplugin_credit' => 'Some of the returned data includes GeoLite data created by MaxMind, available from http://www.maxmind.com.',
	  'geoplugin_city' => 'Islamabad',
	  'geoplugin_region' => 'Islāmābād',
	  'geoplugin_areaCode' => '0',
	  'geoplugin_dmaCode' => '0',
	  'geoplugin_countryCode' => 'PK',
	  'geoplugin_countryName' => 'Pakistan',
	  'geoplugin_continentCode' => 'AS',
	  'geoplugin_latitude' => '33.6957',
	  'geoplugin_longitude' => '73.0113',
	  'geoplugin_regionCode' => '08',
	  'geoplugin_regionName' => 'Islāmābād',
	  'geoplugin_currencyCode' => 'PKR',
	  'geoplugin_currencySymbol' => '₨',
	  'geoplugin_currencySymbol_UTF8' => '₨',
	  'geoplugin_currencyConverter' => '104.703',
	)
	*/
          
          if (!empty($currencyCode)){
              $ipBaseCurrency = $this->getCurrencyId($currencyCode);
              $app->setUserState( "virtuemart_currency_id", $ipBaseCurrency);
          }
    }
    
    /*
     * Method to get VM currency ID from currency code
     * 
     * @param string $currencyCode > 3-letter code like EUR or USD
     * @return int VirtueMart currency ID
     * @author Ahsan Khan
     */
    public function getCurrencyId($currencyCode)
    {
        $db = JFactory::getDBO();
        $q = $db->getQuery(true);
        $q->select('virtuemart_currency_id');
        $q->from('#__virtuemart_currencies');
        $q->where('currency_code_3 = ' . $db->quote($currencyCode));
        $db->setQuery($q);
        return (int)$db->loadResult();
    }

}
