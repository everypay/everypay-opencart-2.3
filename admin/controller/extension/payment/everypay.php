<?php

class ControllerExtensionPaymentEverypay extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->addScript('view/javascript/everypay/js/mustache.min.js');
        $this->document->addScript('view/javascript/everypay/js/installments.js');
        $this->language->load('extension/payment/everypay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('everypay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_installment_amount_from'] = $this->language->get('text_installment_amount_from');
        $data['text_installment_amount_to'] = $this->language->get('text_installment_amount_to');
        $data['text_installment_number'] = $this->language->get('text_installment_number');
        $data['text_installments'] = $this->language->get('text_installments');

        $data['entry_public_key'] = $this->language->get('entry_public_key');
        $data['entry_secret_key'] = $this->language->get('entry_secret_key');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_sandbox'] = $this->language->get('entry_sandbox');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['help_key_id'] = $this->language->get('help_key_id');
        $data['help_order_status'] = $this->language->get('help_order_status');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['everypay_public_key'])) {
            $data['error_public_key'] = $this->error['everypay_public_key'];
        } else {
            $data['error_public_key'] = '';
        }

        if (isset($this->error['everypay_key_secret'])) {
            $data['error_secret_key'] = $this->error['everypay_secret_key'];
        } else {
            $data['error_secret_key'] = '';
        }

        if (isset($this->error['everypay_installments'])) {
            $data['error_installments'] = $this->error['everypay_installments'];
        } else {
            $data['error_installments'] = '';
        }

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/everypay', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/everypay', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

        if (isset($this->request->post['everypay_public_key'])) {
            $data['everypay_public_key'] = $this->request->post['everypay_public_key'];
        } else {
            $data['everypay_public_key'] = $this->config->get('everypay_public_key');
        }

        if (isset($this->request->post['everypay_secret_key'])) {
            $data['everypay_secret_key'] = $this->request->post['everypay_secret_key'];
        } else {
            $data['everypay_secret_key'] = $this->config->get('everypay_secret_key');
        }

        if (isset($this->request->post['everypay_order_status_id'])) {
            $data['everypay_order_status_id'] = $this->request->post['everypay_order_status_id'];
        } else {
            $data['everypay_order_status_id'] = $this->config->get('everypay_order_status_id');
        }

        if (isset($this->request->post['everypay_installments'])) {
            $data['everypay_installments'] = $this->request->post['everypay_installments'];
        } else {
            $data['everypay_installments'] = $this->config->get('everypay_installments');
        }

        if (isset($this->request->post['everypay_sandbox'])) {
            $data['everypay_sandbox'] = $this->request->post['everypay_sandbox'];
        } else {
            $data['everypay_sandbox'] = $this->config->get('everypay_sandbox');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['everypay_status'])) {
            $data['everypay_status'] = $this->request->post['everypay_status'];
        } else {
            $data['everypay_status'] = $this->config->get('everypay_status');
        }

        if (isset($this->request->post['everypay_sort_order'])) {
            $data['everypay_sort_order'] = $this->request->post['everypay_sort_order'];
        } else {
            $data['everypay_sort_order'] = $this->config->get('everypay_sort_order');
        }

        $this->template = 'extension/payment/everypay.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/everypay.tpl', $data));
    }

    protected function validate()
    {
		if (!$this->user->hasPermission('modify', 'extension/payment/everypay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
        if (empty($this->request->post['everypay_public_key'])) {
            $this->error['everypay_public_key'] = $this->language->get('error_public_key');
        }

        if (empty($this->request->post['everypay_secret_key'])) {
            $this->error['everypay_secret_key'] = $this->language->get('error_secret_key');
        }

        if (!empty($this->request->post['everypay_installments'])) {
            $this->validateInstallments($this->request->post['everypay_installments']);
        }

        return !$this->error;
    }

    private function validateInstallments($json)
    {
        $json = htmlspecialchars_decode($json);
        if ($data = json_decode($json, true)) {
            foreach ($data as $item) {
                $result = filter_var_array($item, FILTER_VALIDATE_FLOAT);
                foreach ($result as $valid) {
                    if (false === $valid) {
                        $this->error['everypay_installments'] = $this->language->get('error_installments');

                        return;
                    }
                }
            }

            return;
        }

        $this->error['everypay_installments'] = $this->language->get('error_installments');
    }
}
