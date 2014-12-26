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

class Mcpayment_Webpay_Model_Source_TransactionMode
{
    public function toOptionArray()
    {
        $options =  array();       ;
        $options[] = array(
            	   'value' => 'test',
            	   'label' => 'Test'
         );
		 $options[] = array(
            	   'value' => 'live',
            	   'label' => 'Live'
         );

        return $options;
    }
}