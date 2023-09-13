<?php
namespace Opencart\Admin\Controller\Extension\Dotpay\Payment;

class Dotpay extends \Opencart\System\Engine\Controller
{
	private $error = [];

	public function index(): void
	{

		$this->load->language('extension/dotpay/payment/dotpay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('payment_dotpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['language_reload'])) {
				$this->response->redirect($this->url->link('payment/dotpay', 'user_token=' . $this->session->data['user_token'], true));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_payment'] = $this->language->get('text_payment');
		$data['text_success'] = $this->language->get('text_success');
//		$data['text_bitcoin'] = $this->language->get('text_bitcoin');
//		$data['text_litecoin'] = $this->language->get('text_litecoin');
//		$data['text_darkcoin'] = $this->language->get('text_darkcoin');
//		$data['text_freicoin'] = $this->language->get('text_freicoin');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

	$data['entry_engineurl'] = $this->language->get('entry_engineurl');
//		$data['entry_crypto_coin'] = $this->language->get('entry_crypto_coin');
		$data['entry_redirect_url'] = $this->language->get('entry_redirect_url');
		$data['entry_status'] = $this->language->get('entry_status');
//		$data['entry_merchantID'] = $this->language->get('entry_merchantID');
	$data['entry_display_name'] = $this->language->get('entry_display_name');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_callback_success_order_status'] = $this->language->get('entry_callback_success_order_status');
		$data['entry_callback_failed_order_status'] = $this->language->get('entry_callback_failed_order_status');

		$data['error_permission'] = $this->language->get('error_permission');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

//		$data['help_api_key_hint'] = $this->language->get('help_api_key_hint');
		$data['help_crypto_coin_hint'] = $this->language->get('help_crypto_coin_hint');
		$data['help_redirect_url_hint'] = $this->language->get('help_redirect_url_hint');
		$data['help_display_name_hint'] = $this->language->get('help_display_name_hint');
//		$data['help_merchantID_hint'] = $this->language->get('help_merchantID_hint');

		$data['tab_general'] = 'General';

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['engineurl'])) {
			$data['error_engineurl'] = $this->error['engineurl'];
		} else {
			$data['error_engineurl'] = '';
		}

		if (isset($this->error['display_name'])) {
			$data['error_display_name'] = $this->error['display_name'];
		} else {
			$data['error_display_name'] = '';
		}

//		if (isset($this->error['merchantID'])) {
//			$data['error_merchantID'] = $this->error['merchantID'];
//		} else {
//			$data['error_merchantID'] = '';
//		}

//		if (isset($this->error['crypto_coin'])) {
//			$data['error_crypto_coin'] = $this->error['crypto_coin'];
//		} else {
//			$data['error_crypto_coin'] = '';
//		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dotpay/payment/dotpay', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('extension/dotpay/payment/dotpay', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL');

		if (isset($this->request->post['payment_dotpay_display_name'])) {
			$data['dotpay_display_name'] = $this->request->post['payment_dotpay_display_name'];
		} else {
			$data['dotpay_display_name'] = $this->config->get('payment_dotpay_display_name');
		}

		if (isset($this->request->post['payment_dotpay_engineurl'])) {
			$data['dotpay_engineurl'] = $this->request->post['payment_dotpay_engineurl'];
		} else {
			$data['dotpay_engineurl'] = $this->config->get('payment_dotpay_engineurl');
		}

		if (isset($this->request->post['payment_dotpay_status'])) {
			$data['dotpay_status'] = $this->request->post['payment_dotpay_status'];
		} else {
			$data['dotpay_status'] = $this->config->get('payment_dotpay_status');
		}

		if (isset($this->request->post['payment_dotpay_crypto_coin'])) {
			$data['dotpay_crypto_coin'] = $this->request->post['payment_dotpay_crypto_coin'];
		} else {
			$data['dotpay_crypto_coin'] = $this->config->get('payment_dotpay_crypto_coin');
		}

		if (isset($this->request->post['dotpay_order_status_id'])) {
			$data['dotpay_order_status_id'] = $this->request->post['payment_dotpay_order_status_id'];
		} else {
			$data['dotpay_order_status_id'] = $this->config->get('payment_dotpay_order_status_id');
		}

		if (isset($this->request->post['payment_dotpay_callback_success_order_status_id'])) {
			$data['dotpay_callback_success_order_status_id'] = $this->request->post['payment_dotpay_callback_success_order_status_id'];
		} else {
			$data['dotpay_callback_success_order_status_id'] = $this->config->get('payment_dotpay_callback_success_order_status_id');
		}

		if (isset($this->request->post['payment_dotpay_callback_failed_order_status_id'])) {
			$data['dotpay_callback_failed_order_status_id'] = $this->request->post['payment_dotpay_callback_failed_order_status_id'];
		} else {
			$data['dotpay_callback_failed_order_status_id'] = $this->config->get('payment_dotpay_callback_failed_order_status_id');
		}

		if (isset($this->request->post['payment_dotpay_callback_expired_order_status_id'])) {
			$data['dotpay_callback_expired_order_status_id'] = $this->request->post['payment_dotpay_callback_expired_order_status_id'];
		} else {
			$data['dotpay_callback_expired_order_status_id'] = $this->config->get('payment_dotpay_callback_expired_order_status_id');
		}

		if (isset($this->request->post['payment_dotpay_callback_notenough_order_status_id'])) {
			$data['dotpay_callback_notenough_order_status_id'] = $this->request->post['payment_dotpay_callback_notenough_order_status_id'];
		} else {
			$data['dotpay_callback_notenough_order_status_id'] = $this->config->get('payment_dotpay_callback_notenough_order_status_id');
		}

		if (isset($this->request->post['payment_dotpay_callback_cancel_order_status_id'])) {
			$data['dotpay_callback_cancel_order_status_id'] = $this->request->post['payment_dotpay_callback_cancel_order_status_id'];
		} else {
			$data['dotpay_callback_cancel_order_status_id'] = $this->config->get('payment_dotpay_callback_cancel_order_status_id');
		}

//		if (isset($this->request->post['payment_dotpay_merchantID'])) {
//			$data['dotpay_merchantID'] = $this->request->post['payment_dotpay_merchantID'];
//		} else {
//			$data['dotpay_merchantID'] = $this->config->get('payment_dotpay_merchantID');
//
//			$data['crypto_coins'] = $this->getMerchantCoins($this->config->get('payment_dotpay_merchantID'));
//		}

		if (isset($this->request->post['payment_dotpay_sort_order'])) {
			$data['dotpay_sort_order'] = $this->request->post['payment_dotpay_sort_order'];
		} else {
			$data['dotpay_sort_order'] = $this->config->get('payment_dotpay_sort_order');

		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['ctp_coints_url'] = $this->url->link('extension/dotpay/payment/dotpay|getMerchantCoinsByAjax', 'user_token=' . $this->session->data['user_token']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dotpay/payment/dotpay', $data));
	}

	private function validate()
	{

    file_put_contents('/mnt/log0.txt',print_r($_POST,1));

		if (!$this->user->hasPermission('modify', 'extension/dotpay/payment/dotpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_dotpay_engineurl']) {
			$this->error['engineurl'] = $this->language->get('error_engineurl');
		}

		if (!$this->request->post['payment_dotpay_display_name']) {
			$this->error['display_name'] = $this->language->get('error_display_name');
		}

//		if (!$this->request->post['payment_dotpay_merchantID']) {
//			$this->error['merchantID'] = $this->language->get('error_merchantID');
//		}

//		if (!$this->request->post['payment_dotpay_crypto_coin']) {
//			$this->error['crypto_coin'] = $this->language->get('error_crypto_coin');
//		}

		return !$this->error;
	}


	public function getMerchantCoinsByAjax(): void
	{
		if ($this->request->post['merchantId']) {
			$option = '<option value="">Select Default Coin</option>';
			$arr = $this->getMerchantCoins($this->request->post['merchantId']);
			foreach ($arr as $key => $value) {
				$option .= '<option value="' . $key . '">' . $value . '</option>';
			}
			echo $option;
		}
	}

	function getMerchantCoins($merchantId)
	{


return array();

		$url = 'https://lleo.me/dotpay.com/CloneMasterTransaction?MerchantID=' . $merchantId . '&output=json';
		//$url = 'https://dotpay.com/CloneMasterTransaction?MerchantID=232&output=json';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$output = curl_exec($ch);
		curl_close($ch);

		$php_arr = json_decode($output);
		$new_php_arr = array();

		if (!empty($php_arr)) {
			for ($i = 0; $i < count($php_arr) - 1; $i++) {
				if (($i % 2) == 0) {
					$new_php_arr[$php_arr[$i + 1]] = $php_arr[$i];
				}
			}
		}
		return $new_php_arr;
	}
	function getInputCurrencyList($merchantId)
	{

return array();

		$url = 'https://lleo.me/dotpay.com/v2REAPI?MerchantID=' . $merchantId . '&Call=inputCurrencyList&output=json&APIKey=_';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$output = curl_exec($ch);

		curl_close($ch);

		$php_arr = json_decode($output);
		$new_php_arr = array();

		if (!empty($php_arr)) {
			foreach ($php_arr as $c) {
				if (property_exists($c, 'ShortName')) {
					$new_php_arr['currency'][] = $c->ShortName;
				}

			}
		}

		return $new_php_arr;
	}




}