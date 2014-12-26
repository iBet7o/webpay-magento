<?php
/**
 * MCPayment WebPay Magento Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mcpayment
 * @package    Mcpayment_Webpay
 * @copyright  Copyright (c) 2012 Mobile Credit Payment Pte Ltd
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mcpayment_Webpay_Model_Webpay extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'webpay';
    protected $_canUseForMultishipping  = false;
	
	protected $_testUrl	= 'https://webpaytest.mcpayment.net/post/submit';
    protected $_liveUrl	= 'https://webpay.mcpayment.net/post/submit';

    /**
     * Return Order place direct url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('webpay/payment/redirect', array('_secure' => true));
    }
	
	public function getUrl()
    {
		if ($this->getConfigData('transaction_mode') == 'live')
			return $this->_liveUrl;
		return $this->_testUrl;
    }
    
    public function getFormFields()
    {
    	$mc_id = Mage::getStoreConfig( 'payment/webpay/mc_id' );
    	$mc_securekey = Mage::getStoreConfig( 'payment/webpay/mc_securekey' );
		$recurring = Mage::getStoreConfig( 'payment/webpay/recurring' );

        $checkout = Mage::getSingleton('checkout/session');
        $orderIncrementId = $checkout->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        
        $currency   = $order->getOrderCurrencyCode();
        
        $BAddress = $order->getBillingAddress();
        
        //$paymentAmount = $order->getBaseGrandTotal();
        $paymentAmount = $order->getGrandTotal();
      
	    $product = '';
		
	    $items = $order->getAllItems();
		if ($items)
        {
            $i = 1;
            foreach($items as $item)
            {
            	if ($item->getParentItem()) continue;
				$product .= $item->getName() . '; ';
            }
        }
		$product = rtrim($product, '; ');
		
		$linkBuf = $mc_securekey. "?mid=" . $mc_id ."&ref=" . $orderIncrementId ."&cur=" .$currency ."&amt=" .$paymentAmount;
		$fgkey = md5($linkBuf);
		$params = 	array(
	    				'mid'		=>	$mc_id,
	    				'ref'		=>	$orderIncrementId,
	    				'fgkey'		=>	$fgkey,
	    				'cur'		=>	$currency,
						'amt'		=>	$paymentAmount,
						'product'		=>	$product,
						'param1'		=>	'',
						'param2'		=>	'',
						'param3'		=>	'',
						'partnercode'	=>	'MCP',
						'buyer'		=>	$BAddress->getFirstname() . ' ' . $BAddress->getLastname(),
						'tel'		=>	$BAddress->getTelephone(),
						'email'		=>	$order->getCustomerEmail(),
						'shop'		=>	Mage::app()->getStore()->getName(),
						'lang'		=>	'EN',
						'returnurl'		=>	Mage::getUrl('webpay/payment/success'),
						'statusurl'		=>	Mage::getUrl('webpay/payment/notify'),
						'directToReturn'		=>	'Y'
    				);
					
		if($recurring == 1){
			$params['recurringPayment'] = 'Y';
			$params['recurringAmount'] = $paymentAmount;
			$params['recurringURL'] = Mage::getUrl('webpay/payment/recurring');
		}
        return $params;
    }

    /**
     * Return true if the method can be used at this time
     *
     * @return bool
     */
    public function isAvailable($quote=null)
    {
        return true;
    }
}
