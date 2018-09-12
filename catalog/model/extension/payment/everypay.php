<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class ModelExtensionPaymentEverypay extends Model
{
    public function getMethod($address, $total)
    {
        $this->language->load('extension/payment/everypay');

        return array(
            'code' => 'everypay',
            'title' => $this->language->get('text_title'),
            'terms' => '',
            'sort_order' => $this->config->get('everypay_sort_order'),
        );
    }
}
