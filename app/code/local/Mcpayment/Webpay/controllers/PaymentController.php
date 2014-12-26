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
 
class Mcpayment_Webpay_PaymentController extends Mage_Core_Controller_Front_Action {

	public function redirectAction()
	{
        $mc = Mage::getModel('webpay/webpay');
        
        $fields = $mc->getFormFields();

        $form = new Varien_Data_Form();
        $form->setAction( $mc->getUrl() )
            ->setId('mcpayment_checkout')
            ->setName('mcpayment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
			
		foreach ($fields as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $html = '<html><body>';
        $html.= $this->__('You will be directed to the MCPayment website in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("mcpayment_checkout").submit();</script>';
        $html.= '</body></html>';

        echo $html;
	}
	
	public function successAction()
	{
		$mc_id = Mage::getStoreConfig( 'payment/webpay/mc_id' );
    	$mc_securekey = Mage::getStoreConfig( 'payment/webpay/mc_securekey' );
		
		$mid = $_POST['mid'];
		$ref = $_POST['ref'];
		$cur = $_POST['cur'];
		$amt = $_POST['amt'];
		$shop = $_POST['shop'];
		$buyer = $_POST['buyer'];
		$tel = $_POST['tel'];
		$email = $_POST['email'];
		$product = $_POST['product'];
		$lang = $_POST['lang'];
		$param1 = $_POST['param1'];
		$param2 = $_POST['param2'];
		$param3 = $_POST['param3'];
		
		
		$transid = $_POST['transid'];
		$rescode = $_POST['rescode'];
		$resmsg = $_POST['resmsg'];
		$authcode = $_POST['authcode'];
		$cardco = $_POST['cardco'];
		$resdt = $_POST['resdt'];
		$fgkey = $_POST['fgkey'];
		
		//when rescode is '0000', checking fgkey
		if($rescode == "0000"){
		
			$linkBuf = $mc_securekey. "?mid=" . $mid ."&ref=" . $ref ."&cur=" .$cur ."&amt=" .$amt ."&rescode=" .$rescode ."&transid=" .$transid;
			
			$newFgkey = md5($linkBuf);

			if(strtolower($fgkey) != $newFgkey){
				$rescode = "ERROR";
				$resmsg = "Invalid transaction";
			}
		}
		
		if($rescode == "0000"){
			//success apply your database or file system.
			//$this->_redirect('checkout/onepage/success');

			$html = '<html><body>';
			$html.= '<script type="text/javascript">window.top.location.href="'.Mage::getUrl('checkout/onepage/success').'";</script>';
			$html.= '</body></html>';
	
			echo $html;
		} else {
			//fail transaction
			//$this->_redirect('checkout/cart');
			
			//canceled payment
			if($fgkey == '' && $transid == ''){
				$orderNumber = $ref;
				$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
				$order->cancel();
        		$order->addStatusToHistory($order->getStatus(), 'Payment was canceled by user.');
				$order->save();
			}
			$html = '<html><body>';
			$html.= '<script type="text/javascript">window.top.location.href="'.Mage::getUrl('checkout/onepage/failure').'";</script>';
			$html.= '</body></html>';
	
			echo $html;
		}
	}
	
	public function notifyAction()
	{
		$mc_id = Mage::getStoreConfig( 'payment/webpay/mc_id' );
    	$mc_securekey = Mage::getStoreConfig( 'payment/webpay/mc_securekey' );
		
		$mid = $_POST['mid'];
		$ref = $_POST['ref'];
		$cur = $_POST['cur'];
		$amt = $_POST['amt'];
		$shop = $_POST['shop'];
		$buyer = $_POST['buyer'];
		$tel = $_POST['tel'];
		$email = $_POST['email'];
		$product = $_POST['product'];
		$lang = $_POST['lang'];
		$param1 = $_POST['param1'];
		$param2 = $_POST['param2'];
		$param3 = $_POST['param3'];
		
		
		$transid = $_POST['transid'];
		$rescode = $_POST['rescode'];
		$resmsg = $_POST['resmsg'];
		$authcode = $_POST['authcode'];
		$cardco = $_POST['cardco'];
		$resdt = $_POST['resdt'];
		$fgkey = $_POST['fgkey'];
		
		//when rescode is '0000', checking fgkey
		if($rescode == "0000"){
		
			$linkBuf = $mc_securekey. "?mid=" . $mid ."&ref=" . $ref ."&cur=" .$cur ."&amt=" .$amt ."&rescode=" .$rescode ."&transid=" .$transid;
			
			$newFgkey = md5($linkBuf);

			if(strtolower($fgkey) != $newFgkey){
				$rescode = "ERROR";
				$resmsg = "Invalid transaction";
			}
		}
		
		if($rescode == "0000"){
			//success apply your database or file system.
			$orderNumber = $ref;
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
				
			$order->getPayment()->registerCaptureNotification( $order->getBaseGrandTotal() );
			$order->getPayment()->setTransactionId($transid);
			
			$order->addStatusToHistory($order->getStatus(), 'Transaction ID: ' . $transid);
				
			$order->save();
		} else {
			//fail transaction
		}
    	exit;
	}
	
	public function recurringAction()
	{
		$mc_id = Mage::getStoreConfig( 'payment/webpay/mc_id' );
    	$mc_securekey = Mage::getStoreConfig( 'payment/webpay/mc_securekey' );
		
		$mid = $_POST['mid'];
		$ref = $_POST['ref'];
		$cur = $_POST['cur'];
		$amt = $_POST['amt'];
		$shop = $_POST['shop'];
		$buyer = $_POST['buyer'];
		$tel = $_POST['tel'];
		$email = $_POST['email'];
		$product = $_POST['product'];
		$lang = $_POST['lang'];
		$param1 = $_POST['param1'];
		$param2 = $_POST['param2'];
		$param3 = $_POST['param3'];
		
		
		$transid = $_POST['transid'];
		$rescode = $_POST['rescode'];
		$resmsg = $_POST['resmsg'];
		$authcode = $_POST['authcode'];
		$cardco = $_POST['cardco'];
		$resdt = $_POST['resdt'];
		$fgkey = $_POST['fgkey'];
		
		$recurringID = $_POST['recurringID'];
		$recurringRetry = $_POST['recurringRetry'];
		$recurringNext = $_POST['recurringNext'];
		
		//when rescode is '0000', checking fgkey
		if($rescode == "0000"){
		
			$linkBuf = $mc_securekey. "?mid=" . $mid ."&ref=" . $ref ."&cur=" .$cur ."&amt=" .$amt ."&rescode=" .$rescode ."&transid=" .$transid;
			
			$newFgkey = md5($linkBuf);

			if(strtolower($fgkey) != $newFgkey){
				$rescode = "ERROR";
				$resmsg = "Invalid transaction";
			}
		}
		
		if($rescode == "0000"){
			//success apply your database or file system.
			$orderNumber = $ref;
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
			
			$text = 'Recurring ID: ' . $recurringID . '<br />';
			$text .= 'Recurring Next: ' . $recurringNext . '<br />';
				
			$order->addStatusToHistory($order->getStatus(), $text);
				
			$order->save();
		} else {
			//fail transaction
		}
    	exit;
	}
}