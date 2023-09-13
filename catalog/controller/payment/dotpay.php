<?php
namespace Opencart\Catalog\Controller\Extension\Dotpay\Payment;

class Dotpay extends \Opencart\System\Engine\Controller
{
	public function index()
	{
		$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$order_info = "Session empty, please redo your order to proceed.";

		if (isset($this->session->data['order_id'])) {
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
			file_put_contents('/mnt/log_order_info.txt',print_r($order_info,1));
		} else {
			return $this->error($order_info);
		}

		try {
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
// ============== POST ==================

	if ( empty($this->config->get('payment_dotpay_engineurl'))) {
	    return error('CredentialsMissing');	// echo 'CredentialsMissing'; exit();
	}

	// Make operation
	$res = $this->EngineSend(json_encode(
	    array(
		'price'=>$this->request->post['price'],
		'WalletID'=>$this->request->post['WalletID'],
		'urrency'=>$this->request->post['currency'],
		'OrderID'=>$this->request->post['OrderID']
	    )
	));

	// Ошибки связи

	if (isset($res['error']) && !empty($res['error'])) {
	    // А секюрно ли палить url:port перед пользователем?
	    // для настройки пока оставим
	    // if($res['error']=='connect') return $this->error('Error: '.$res['error']);
	    return $this->error('Error: '.$res['error']
		    .(isset($res['error_message'])?'<p>'.$res['error_message']:'')
	    );
	}

	// Разбираем ответ
	if ($res['result'] == 'paid') { // OK
	    // $this->model_checkout_order->addHistory(
		// $_REQUEST['CustomerReferenceNr'],
		// $this->config->get('payment_dotpay_callback_success_order_status_id','Successfully Paid'),
		// 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to completed for OrderID #' . $_REQUEST['CustomerReferenceNr'],
		// false);
/*
	    return $this->ok("{text_success}"
."<br>url: ".$urlok
."<p>id: [".$this->session->data['order_id']."]"
."<p>pid: [".$this->config->get('payment_dot_approved_status_id')."]"
);
*/
            $this->model_checkout_order->addHistory(
		    $this->session->data['order_id'],
		    0, // $this->config->get('payment_dot_approved_status_id'),
		    'Dotpay Transaction #000 completed',
		    true
	    );
	    $urlok = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
            $this->response->redirect($urlok);
	    // return $this->ok("{text_success} url: ".$urlok);
	}

//	if ($_REQUEST['status'] == 'paid' && $_REQUEST['notenough'] == '1') {
// 		$this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_notenough_order_status_id'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to pending (underpaid) for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
//		$statusProcessed = 15;
//		$this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_notenough_order_status_id'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to pending (underpaid) for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
//		return $this->error('{text_notenough}' . $_REQUEST['CustomerReferenceNr'] . '{text_invoice_link} <a href="https://dotpay.com/invoice/' . $_REQUEST['ConfirmCode'] . '" target="_blank">invoice link</a>');
//	}

	if ($res['result'] == 'failed') {
		// $this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_failed_order_status_id', 'Transaction payment failed'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to failed for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
		return $this->error('{text_failed}');
	}

//	if ($res['result'] == 'expired') {
//		// $this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_expired_order_status_id'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to expired for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
//		return $this->error('{text_expired}');
//	}

	if ($res['result'] == 'underpaid') {
		// $this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_notenough_order_status_id'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to pending (' . $res['result'] . ') for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
		return $this->error('{text_notenough}' . $_REQUEST['CustomerReferenceNr'] . ' <a href="https://dotpay.com/invoice/' . $_REQUEST['ConfirmCode'] . '" target="_blank">invoice link</a>');
	}

//	if ($res['result'] == 'cancelled') {
//		// $this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_cancel_order_status_id'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to Canceled for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
//		return $this->error('{text_cancel}');
//	}

	if ($res['result'] == 'waiting') {
		return $this->error('{text_waiting}');
	}

	// $this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_cancel_order_status_id'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to Canceled for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
	return $this->error('{text_cancel}');
// ============== / POST ==================

			} else { // method GET
				$this->load->language('extension/dotpay/payment/dotpay');

				$data['action'] = $this->url->link('extension/dotpay/payment/dotpay', '', true);
				$data['price'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
				// $data['key'] = $this->config->get('payment_dotpay_api_key');
				$data['WalletID'] = $this->config->get('payment_dotpay_WalletID');
				// $data['crypto_coins'] = ''; // $this->getMerchantCoins($this->config->get('payment_dotpay_merchantID'));
				$data['OrderID'] = $this->session->data['order_id'];
				$data['currency'] = $order_info['currency_code'];
				$data['text_wallet'] = $this->language->get('text_wallet');

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/dotpay/payment/dotpay')) {
					return $this->load->view($this->config->get('config_template') . '/template/extension/dotpay/payment/dotpay', $data);
				} else {
					return $this->load->view('extension/dotpay/payment/dotpay', $data);
				}
			}

		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
			return;
		}
	}

	public function callback()
	{
	    file_put_contents('/mnt/log_callback.txt','yes!');
	}



// =====================================
/*

namespace Opencart\Catalog\Controller\Extension\Dotpay\Payment;

class Dotpay extends \Opencart\System\Engine\Controller
{
  public function index()
  {
    $data = $this->session->data; // array();
    $data['button_confirm'] = $this->language->get('button_confirm');

    $this->load->model('checkout/order');

    $order_info = "Session empty, please redo your order to proceed.";

    if (isset($this->session->data['order_id'])) {
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
    } else {
        return error($order_info);
    }
  }
*/

	/*
	$data1 = array();
	$this->load->language('extension/dotpay/payment/dotpay_invoice');

	if (!isset($res->error) || empty($res->error) || $res->error == '' ) {
		$this->model_checkout_order->addHistory('$res->CustomerReferenceNr', $this->config->get('payment_dotpay_order_status_id'), 'Transaction #' . '$res->TransactionID' . ' Created on dotpay for OrderID #' . '$res->CustomerReferenceNr' . ' with status Processing', false);
		//print_r($res);
		$data1['TransactionID'] = $res->TransactionID;
		$data1['WalletID'] = $res->WalletID;
		$data1['coinAddress'] = $res->coinAddress;
		$data1['Amount'] = $res->Amount;
		$data1['CoinName'] = $res->CoinName;
		$data1['QRCodeURL'] = $res->QRCodeURL;
		$data1['RedirectURL'] = $res->RedirectURL;
		$data1['ExpiryTime'] = $res->ExpiryTime;
		// $data1['MerchantID'] = $res->MerchantID;
		$data1['engineurl'] = $res->engineurl;
		$data1['ConfirmCode'] = $res->Security;
		$data1['inputCurrency'] = $res->inputCurrency;
		$data1['CalExpiryTime'] = date("m/d/Y h:i:s T", strtotime($res->ExpiryTime));
		if (property_exists($res, 'Tag')) {
			if (!empty($res->Tag)) {
				$data1['CtpTag'] = $res->Tag;
			}
		}
		$data1['OrderID'] = $this->session->data['order_id'];
		$data1['CustomerReferenceNr'] = $res->CustomerReferenceNr;
		$data1['status'] = $res->Status;
		$data1['text_title'] = $this->language->get('text_title');
		$data1['text_transaction_id'] = $this->language->get('text_transaction_id');
		$data1['text_address'] = $this->language->get('text_address');
		$data1['text_amount'] = $this->language->get('text_amount');
		$data1['text_coinname'] = $this->language->get('text_coinname');
		$data1['text_checkout_number'] = $this->language->get('text_checkout_number');
		$data1['text_expiry'] = $this->language->get('text_expiry');
		$data1['text_pay_with_other'] = $this->language->get('text_pay_with_other');
		$data1['text_clickhere'] = $this->language->get('text_clickhere');

	} else {
		$data1['error'] = $res->error;
	}


		if (isset($this->session->data['order_id'])) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int) $this->session->data['order_id'] . "' AND order_status_id > 0");

			if ($query->num_rows) {
				$this->cart->clear();
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['guest']);
				unset($this->session->data['comment']);
				unset($this->session->data['order_id']);
				unset($this->session->data['coupon']);
				unset($this->session->data['reward']);
				unset($this->session->data['voucher']);
				unset($this->session->data['vouchers']);
			}
		}
		$data1['footer'] = $this->load->controller('common/footer');
		$data1['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/dotpay/payment/dotpay_invoice')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/extension/dotpay/payment/dotpay_invoice', $data1));
		} else {
			$this->response->setOutput($this->load->view('extension/dotpay/payment/dotpay_invoice', $data1));
		}

			} else {
				$this->load->language('extension/dotpay/payment/dotpay');

				$data['action'] = $this->url->link('extension/dotpay/payment/dotpay', '', true);

				$data['price'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
				$data['WalletID'] = $this->config->get('payment_dotpay_WalletID');
				$data['OrderID'] = $this->session->data['order_id'];
				$data['currency'] = $order_info['currency_code'];

				$data['text_wallet'] = $this->language->get('text_wallet');

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/dotpay/payment/dotpay')) {
					return $this->load->view($this->config->get('config_template') . '/template/extension/dotpay/payment/dotpay', $data);
				} else {
					return $this->load->view('extension/dotpay/payment/dotpay', $data);
				}
			}
*/

// =================================================================
	function EngineSend($json,$post=false)
	{
		$url = $this->config->get('payment_dotpay_engineurl');
		$ch = curl_init( $url );
		curl_setopt_array($ch, array(
			CURLOPT_POSTFIELDS => $json,
			CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FAILONERROR => true,
			CURLOPT_CONNECTTIMEOUT => 3, // only spend 3 seconds trying to connect
			CURLOPT_TIMEOUT => 30, // 30 sec waiting for answer
		    )
		);
		$result = curl_exec($ch);
		if (curl_errno($ch)) return array( 'error' => 'connect', 'error_message' => curl_error($ch) );
		$array = json_decode($result);
		if(empty($array)) return array( 'error' => 'json', 'error_message' => 'Wrong json format' );
		curl_close($ch);
		return (array) $array;
	}

/*
	function page($text) {
		$data=$this->session->data;
		$this->load->language('extension/dotpay/payment/dotpay');
		$this->load->model('checkout/order');
		$data['action'] = $this->url->link('extension/dotpay/payment/dotpay', '', true);
			//$data['price'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			//$data['WalletID'] = $this->config->get('payment_dotpay_WalletID');
			//$data['OrderID'] = $this->session->data['order_id'];
			//$data['currency'] = $order_info['currency_code'];
		// $data['text_wallet'] = $this->language->get('text_wallet');
		return $this->tpl('extension/dotpay/payment/dotpay', $data);
	}
*/
	function error($text) {
		$data=$this->session->data;
		$this->load->language('extension/dotpay/payment/dotpay_invoice');
		$this->load->model('checkout/order');
		$text=preg_replace_callback("/\{([^\s\{\}]+)\}/s",function($t) { return $this->language->get($t[1]); },$text);
		$data['text_failed'] = $text; // $this->language->get('text_failed');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		return $this->tpl('extension/dotpay/payment/dotpay_failed', $data);
	}

	function tpl($tpl, $data) {
	    $c = $this->config->get('config_template') . '/template/' . $tpl;
	    if( !file_exists(DIR_TEMPLATE . $c) ) $c = $tpl;
	    return $this->response->setOutput($this->load->view($c, $data));
	}

	function ok($text) {
		$data=$this->session->data;
		$this->load->language('extension/dotpay/payment/dotpay_invoice');
		$this->load->model('checkout/order');
		// $this->model_checkout_order->addHistory($_REQUEST['CustomerReferenceNr'], $this->config->get('payment_dotpay_callback_success_order_status_id', 'Successfully Paid'), 'Dotpay Transaction #' . $_REQUEST['TransactionID'] . ' Set to completed for OrderID #' . $_REQUEST['CustomerReferenceNr'], false);
		$text=preg_replace_callback("/\{([^\s\{\}]+)\}/s",function($t) { return $this->language->get($t[1]); },$text);
		$data['text_success'] = $text;
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		return $this->tpl('extension/dotpay/payment/dotpay_success',$data);
	}
}