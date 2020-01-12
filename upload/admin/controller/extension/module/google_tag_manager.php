<?php

class ControllerExtensionModuleGoogleTagManager extends Controller
{
    private $error = array();

    public function index()
    {

        $this->load->language('extension/module/google_tag_manager');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('google_tag_manager', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_status'] = $this->language->get('entry_status');
		$data['gtm_id'] = $this->language->get('gtm_id');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['token'] = $this->session->data['token'];


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/google_tag_manager', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/google_tag_manager', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        if (isset($this->request->post['google_tag_manager_status'])) {
            $data['google_tag_manager_status'] = $this->request->post['google_tag_manager_status'];
        } else {
            $data['google_tag_manager_status'] = $this->config->get('google_tag_manager_status');
        }
		
		if (isset($this->request->post['google_tag_manager_gtm_id'])) {
            $data['google_tag_manager_gtm_id'] = $this->request->post['google_tag_manager_gtm_id'];
        } else {
            $data['google_tag_manager_gtm_id'] = $this->config->get('google_tag_manager_gtm_id');
        }
		
	
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/google_tag_manager.tpl', $data));

    }
	
	    public function install()
    {
        $this->load->language('extension/module/google_tag_manager');
        $this->load->model('extension/extension');
        $this->load->model('setting/setting');
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/google_tag_manager');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/google_tag_manager');

        if (!in_array('google_tag_manager', $this->model_extension_extension->getInstalled('module'))) {
            $this->model_extension_extension->install('module', 'google_tag_manager');
        }

        $this->session->data['success'] = $this->language->get('text_success_install');
    }
	
	    public function uninstall()
    {
        $this->load->model('extension/extension');
        $this->load->model('setting/setting');

        $this->model_extension_extension->uninstall('module', 'google_tag_manager');
        $this->model_setting_setting->deleteSetting('google_tag_manager');
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/google_tag_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
		
		if (utf8_strlen(trim($this->request->post['google_tag_manager_gtm_id'])=='')){
			$this->error['warning'] = $this->language->get('error_gtm');
		}

        return !$this->error;
    }
}