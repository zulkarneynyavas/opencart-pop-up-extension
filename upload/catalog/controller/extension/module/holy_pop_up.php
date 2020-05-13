<?php
class ControllerExtensionModuleHolyPopUp extends Controller {

	public function index($setting = null) {

		$this->load->language('extension/module/holy_pop_up');

		if ($setting && $setting['status']) {

			if (($setting['date_start'] == '0000-00-00' || strtotime($setting['date_start']) < time()) && 
				($setting['date_end'] == '0000-00-00' || strtotime($setting['date_end']) > time())) {

				$data = array();
				
				$data['image'] = 'image/' . $setting['image'];
				$data['url'] = $setting['url'];
				$data['bg_color'] = $setting['bg_color'];
				$data['padding'] = $setting['padding'];
				$data['token'] = $setting['token'];

				return $this->load->view('extension/module/holy_pop_up', $data);

			}
		}
	}
}