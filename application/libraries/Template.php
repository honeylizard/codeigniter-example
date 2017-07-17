<?php

/**
 * Class Template
 */
class Template {

	/**
	 * @var CI_Controller $ci_instance  The CodeIgniter instance
	 */
	private $ci_instance;

	/**
	 * @var array $template_data    The information that will be placed inside the template
	 */
	private $template_data = [];

	private $languages = [
		'en-US' => 'english',
	];

	/**
	 * Template constructor.
	 */
	public function __construct() {
		$this->ci_instance =& get_instance();

		// get the language based on the user's session language
		$session_language = $this->ci_instance->session->userdata('language');
		if (empty($session_language)) {
			// If there is no session language, use a default one instead.
			$session_language = 'en-US';
			//Store the default language in the session
			$session_data = [
				'language' => $session_language,
			];
			$this->ci_instance->session->set_userdata($session_data);
		}

		if (array_key_exists($session_language, $this->languages)) {
			$this->ci_instance->lang->load($session_language, $this->languages[$session_language]);
		}

		$this->template_data = [
			'language' => $session_language,
			'bootstrap_css' => base_url('assets/third_party/bootstrap/css/bootstrap.min.css'),
			'app_css' => base_url('assets/style.css'),
			'bootstrap_js' => base_url('assets/third_party/bootstrap/js/bootstrap.min.js'),
			'jquery_js' => base_url('assets/third_party/jquery/jquery-3.1.1.min.js'),
			'application_name' => $this->ci_instance->config->item('application_name'),
			'application_url' => base_url(),
		];

		if ($this->ci_instance->session->userdata('user')) {
			$this->set_user_nav_links();
		} else {
			$this->set_default_nav_links();
		}

	}

	/**
	 * Sets a specific piece of information to the template data.
	 *
	 * @param string $content_area  The key for the information
	 * @param string $value     The value of the information
	 */
	public function set($content_area, $value) {
		$this->template_data[$content_area] = $value;
	}

	/**
	 * Displays the template along with the template information and provided content.
	 *
	 * @param string $template  The name of the template to use
	 * @param string $name  The template information key where the view will be placed
	 * @param string $view  The name of the view for the content
	 * @param array $view_data  The information needed for the content view
	 */
	public function load($template = '', $name = '', $view = '' , $view_data = []) {
		if (! file_exists(APPPATH.'views/' . $view . '.php')) {
			show_404(); // Whoops, we don't have a page for that!
		}

		$this->set($name , $this->ci_instance->load->view($view, $view_data, TRUE));

		$this->ci_instance->load->view('layouts/' . $template, $this->template_data);
	}

	/**
	 * Sets the default navigation bar's links for the template.
	 *
	 * These links are specific for navigation where a user is not logged in yet.
	 */
	private function set_default_nav_links() {
		$data = [
			'login_label' => lang('login'),
			'login_url' => base_url('user/login'),
			'register_label' => lang('register'),
			'register_url' => base_url('user/register'),
		];
		$this->set('navigation_links', $this->ci_instance->load->view('nav/default', $data, TRUE));
	}

	/**
	 * Sets the user-specific navigation bar's links for the template.
	 *
	 * These links are specific for navigation where a user is logged into the application.
	 */
	private function set_user_nav_links() {
		$user_data = $this->ci_instance->session->userdata('user');

		$fullname = $user_data['first-name'] . ' ' . $user_data['last-name'];
		$data = [
			'name' => $fullname,
			'profile_url' => base_url('settings/profile'),
			'profile_label' => lang('profile'),
			'settings_url' => base_url('settings/index'),
			'settings_label' => lang('settings'),
			'logout_url' => base_url('user/logout'),
			'logout_label' => lang('logout'),
		];
		$this->set('navigation_links', $this->ci_instance->load->view('nav/user', $data, TRUE));
	}

}
