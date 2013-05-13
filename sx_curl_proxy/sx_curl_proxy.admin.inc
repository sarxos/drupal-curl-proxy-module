<?php


function sx_curl_proxy_admin() {

	$form = Array();

	$form['empty'] = array(
		'#type' => 'markup',
		'#value' => "<p></p>",
	);

	$form['sx_curl_proxy_use'] = array(
		'#type' => 'checkbox',
		'#title' => t('Use cURL Proxy'),
		'#description' => t('Whether or not cURL proxy shall be used.'),
		'#default_value' => variable_get('sx_curl_proxy_use', false),
	);

	$form['sx_curl_proxy_host'] = array(
		'#type' => 'textfield',
		'#title' => t('Proxy Host'),
		'#description' => t('This is proxy host used by cURL (in some situations can be leaved empty)'),
		'#default_value' => variable_get('sx_curl_proxy_host', ''),
	);

	$form['sx_curl_proxy_port'] = array(
		'#type' => 'textfield',
		'#title' => t('Proxy Port Number'),
		'#description' => t('This is proxy port number used by cURL'),
		'#default_value' => variable_get('sx_curl_proxy_port', ''),
		'#regexp' => "/^\\d+$/",
	);

	return system_settings_form($form);
}

function sx_curl_proxy_admin_validate($form, &$form_state) {

	$host = &$form_state['values']['sx_curl_proxy_host'];
	$port = &$form_state['values']['sx_curl_proxy_port'];

	if (!empty($host)) {
		// remove slash char from the end of proxy host
		if (substr($host, -1) == '/') {
			$host = substr($host, 0, strlen($host) - 1);
		}
		if (strpos($host, "http://") != 0) {
			$host = "http://" . $host;
		}
	}

	if (!preg_match("/^\d+$/", $port)) {
		form_set_error('sx_curl_proxy_port', t("Proxy port must be a number"), true);
	}
}
