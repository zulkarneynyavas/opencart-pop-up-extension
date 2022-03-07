<?php
class ControllerExtensionModulePopUp extends Controller {

	private $error = array();
	
	const DEFAULT_MODULE_SETTINGS = [
		'name' => 'Pop-Up',
		'image' => '',
		'url' => 'http://www.example.com/',
		'bg_color' => '#F50203',
		'padding' => '5px',
		'token' => '',
		'date_start' => '',
		'date_end' => '',
		'status' => 1
	];

	public function index() {
		if (isset($this->request->get['module_id'])) {
			$this->configure($this->request->get['module_id']);
		} else {
			$this->load->model('setting/module');
			$this->model_setting_module->addModule('pop_up', self::DEFAULT_MODULE_SETTINGS);
			$this->response->redirect($this->url->link('extension/module/pop_up','&user_token='.$this->session->data['user_token'].'&module_id='.$this->db->getLastId()));
		}
	}

	public function generateRandomString($length = 16) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	protected function configure($module_id) {

		$this->load->model('setting/module');
		$this->load->language('extension/module/pop_up');
		
		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {

			if ($this->request->post['token'] == '') {
				$this->request->post['token'] = $this->generateRandomString();
			}

			if ($this->request->post['date_start'] == '') {
				$this->request->post['date_start'] = date('Y-m-d');
			}

			if ($this->request->post['date_end'] == '') {
				$this->request->post['date_end'] = date('Y-m-d', strtotime('+7 days'));
			}

			if ($this->request->post['image']) {
				$this->request->post['image'] = $this->request->post['image'];
			} 
			
			$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/pop_up', 'user_token=' . $this->session->data['user_token'], true)
		);

		$module_setting = $this->model_setting_module->getModule($module_id);

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else {
			$data['name'] = $module_setting['name'];
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} else {
			$data['image'] = $module_setting['image'];
		}

		if (isset($this->request->post['url'])) {
			$data['url'] = $this->request->post['url'];
		} else {
			$data['url'] = $module_setting['url'];
		}

		if (isset($this->request->post['bg_color'])) {
			$data['bg_color'] = $this->request->post['bg_color'];
		} else {
			$data['bg_color'] = $module_setting['bg_color'];
		}

		if (isset($this->request->post['padding'])) {
			$data['padding'] = $this->request->post['padding'];
		} else {
			$data['padding'] = $module_setting['padding'];
		}

		if (isset($this->request->post['token'])) {
			$data['token'] = $this->request->post['token'];
		} else {
			$data['token'] = $module_setting['token'];
		}

		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} else {
			$data['date_start'] = $module_setting['date_start'];
		}

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		} else {
			$data['date_end'] = $module_setting['date_end'];
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else {
			$data['status'] = $module_setting['status'];
		}



		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . $module_setting['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($module_setting['image'], 100, 100);
			$data['image'] = $module_setting['image'];
		} else {
			
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			$data['image'] = $module_setting['image'];
		}



		
		$data['action']['cancel'] = $this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'].'&type=module');
		$data['action']['save'] = "";

		$data['error'] = $this->error;	
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/pop_up', $data));
	}

	public function validate() {

		if (!$this->user->hasPermission('modify', 'extension/module/pop_up')) {
			$this->error['permission'] = true;
			return false;
		}

		if (!utf8_strlen($this->request->post['name'])) {
			$this->error['name'] = true;
		}
		
		if (!utf8_strlen($this->request->post['image'])) {
			$this->error['image'] = true;
		}

		if (!utf8_strlen($this->request->post['url'])) {
			$this->error['url'] = true;
		}

		if (!utf8_strlen($this->request->post['bg_color'])) {
			$this->error['bg_color'] = true;
		}

		if (!utf8_strlen($this->request->post['padding'])) {
			$this->error['padding'] = true;
		}
		
		return empty($this->error);
	}
	
	public function install() {
		$this->load->model('setting/setting');
		$this->load->model('setting/module');

		$this->model_setting_setting->editSetting('module_pop_up', ['module_pop_up_status'=>1]);
		$this->model_setting_module->addModule('pop_up', self::DEFAULT_MODULE_SETTINGS); 
	}
	
	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_pop_up');
	}
}
