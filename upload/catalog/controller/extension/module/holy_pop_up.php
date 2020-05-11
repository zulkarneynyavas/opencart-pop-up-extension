<?php
class ControllerExtensionModuleHolyPopUp extends Controller {

	public function index($setting = null) {

		$this->load->language('extension/module/holy_pop_up');

		if ($setting && $setting['status']) {

			$data = array();
			
			$data['image'] = $setting['image'];
			$data['url'] = $setting['url'];
			$data['bg_color'] = $setting['bg_color'];
			$data['padding'] = $setting['padding'];

			return $this->load->view('extension/module/holy_pop_up', $data);
		}
	}
}