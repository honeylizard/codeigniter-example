<?php

/**
 * Class Settings
 */
class Settings extends MY_Controller {

	/**
	 * @var array $languages    The key/value pair of languages available for the application.
	 */
	private $languages = [];

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('user_model');

		$this->languages = [
			'en-US' => lang('en_us'),
			//'en-GB' => lang('en_gb'),
		];
	}

	/**
	 * Displays the user's settings page.
	 */
	public function index() {
		$this->confirm_user_logged_in();

		$initial_language_value = $this->input->post('language');

		$flash_message = $this->session->flashdata('flash_message');
		$message = isset($flash_message) ? $flash_message : '';

		if (! empty($initial_language_value)) {
			// set form validation rules
			$this->form_validation->set_rules('language', lang('display_language'), 'callback__language_check');

			$form_validated = $this->form_validation->run();

			if ($form_validated) {
				$user = $this->session->userdata('user');
				$data = [
					'id'       => $user['id'],
					'language' => $initial_language_value,
				];

				$updated = $this->user_model->update_settings($data);
				if ($updated) {
					$alert_data = [
						'title' => lang('settings_updated_alert_title'),
						'message' => lang('settings_updated_alert_body'),
					];
					$message = $this->load->view('alerts/success_title.php', $alert_data, TRUE);
				}
			} else {
				$alert_data = [
					'message' => lang('error_occurred'),
				];
				$message = $this->load->view('alerts/error.php', $alert_data, TRUE);
			}
		}

		$this->get_settings_view($initial_language_value, $message);
	}

	/**
	 * Displays the user's profile page.
	 */
	public function profile() {
		$this->confirm_user_logged_in();

		$user_data = $this->session->userdata('user');
		$email_value = $this->input->post('email');
		$first_name_value = $this->input->post('first-name');
		$last_name_value = $this->input->post('last-name');
		$submitted = $this->input->post();

		if (isset($user_data['username']) && empty($email_value) && empty($submitted)) {
			$email_value = $user_data['username'];
		}

		if (isset($user_data['first-name']) && empty($first_name_value) && empty($submitted)) {
			$first_name_value = $user_data['first-name'];
		}

		if (isset($user_data['last-name']) && empty($last_name_value) && empty($submitted)) {
			$last_name_value = $user_data['last-name'];
		}

		$input_values = [
			'email' => $email_value,
			'first_name' => $first_name_value,
			'last_name' => $last_name_value,
		];

		$flash_message = $this->session->flashdata('flash_message');
		$message = isset($flash_message) ? $flash_message : '';

		if ($submitted) {
			// set form validation rules
			$this->form_validation->set_rules(
				'email', lang('email'), $this->email_validation_rules . '|required|callback__username_check'
			);
			$this->form_validation->set_rules(
				'first-name', lang('first_name'), $this->string_validation_rules
			);
			$this->form_validation->set_rules(
				'last-name', lang('last_name'), $this->string_validation_rules
			);

			$form_validated = $this->form_validation->run();
			if ($form_validated) {
				$data = [
					'id'       => $user_data['id'],
					'email'    => $input_values['email'],
					'first_name' => $input_values['first_name'],
					'last_name' => $input_values['last_name'],
				];

				$updated = $this->user_model->update_profile($data);

				if ($updated) {
					$alert_data = [
						'title' => lang('profile_updated_alert_title'),
						'message' => lang('profile_updated_alert_body'),
					];
					$message = $this->load->view('alerts/success_title.php', $alert_data, TRUE);
				}
			} else {
				if (form_error('email') || form_error('first-name') || form_error('last-name')) {
					$alert_data = [
						'message' => lang('error_occurred'),
					];
					$message = $this->load->view('alerts/error.php', $alert_data, TRUE);
				}
			}
		}

		$this->get_profile_view($input_values, $message);
	}

	/**
	 * Form Validation callback for a language select field.
	 * Checks if the value provided is in the list of available languages.
	 * If not, generates a form error message.
	 *
	 * Note: This function is not publicly available via url access
	 *
	 * @param string $value     The value of the form field.
	 *
	 * @return bool
	 */
	public function _language_check($value) {
		$valid = array_key_exists($value, $this->languages);

		if (! $valid) {
			$this->form_validation->set_message('_language_check', lang('language_error'));
		}

		return $valid;
	}

	/**
	 * Form Validation callback for a user email input field.
	 * Checks if the value provided is associated with an existing user.
	 * If it is (and is not the current user), generates a form error message.
	 *
	 * Note: This function is not publicly available via url access
	 *
	 * @param string $value     The value of the form field.
	 *
	 * @return bool
	 */
	public function _username_check($value) {
		$associated_user = $this->user_model->does_user_exist($value);
		$user_data = $this->session->userdata('user');

		$valid = TRUE;

		if ($user_data['id'] != $associated_user && $associated_user != 0) {
			$valid = FALSE;
			$this->form_validation->set_message('_username_check', lang('username_error'));
		}

		return $valid;
	}

	/**
	 * Generates the settings HTML form view.
	 *
	 * @param string $language_value    The value of the language field
	 * @param string $message           The alert message for the form
	 */
	private function get_settings_view($language_value = '', $message = '') {
		$language_select = $this->get_select_field($language_value, $this->languages, 'language');

		$form_data = [
			'message' => $message,

			'form_tag' => form_open('settings/index', [
				'id' => 'user-settings-form',
			]),

			'submit_label' => lang('save_settings'),
		];

		$form_data = $this->add_form_select_data($form_data, 'language', lang('display_language'), $language_select, $language_value);


		$data = [
			'title' => lang('settings'),
			'settings_form' => $this->load->view('forms/settings.php', $form_data, TRUE),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents' , 'app/settings', $data);
	}

	/**
	 * Generates the profile HTML form view.
	 *
	 * @param array $data           The values from the form
	 * @param string $message       The alert message for the form
	 */
	private function get_profile_view($data = [], $message = '') {
		$email_input = $this->get_email_input_field($data['email']);

		$first_name_input = $this->get_input_field($data['first_name'], 'first-name');

		$last_name_input = $this->get_input_field($data['last_name'], 'last-name');

		$form_data = [
			'message' => $message,
			'form_tag' => form_open('settings/profile', [
				'id' => 'user-profile-form',
			]),
			'account_legend' => lang('account_info'),
			'personal_legend' => lang('personal_info'),
			'submit_label' => lang('save_profile'),
		];

		$form_data = $this->add_form_input_data(
			$form_data, 'email', lang('email'), $email_input, $data['email'], lang('we_value_privacy')
		);

		$form_data = $this->add_form_input_data(
			$form_data, 'first_name', lang('first_name'), $first_name_input, $data['first_name'], '', 'first-name'
		);

		$form_data = $this->add_form_input_data(
			$form_data, 'last_name', lang('last_name'), $last_name_input, $data['last_name'], '', 'first-name'
		);


		$data = [
			'title' => lang('profile'),
			'profile_form' => $this->load->view('forms/profile.php', $form_data, TRUE),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents' , 'app/profile', $data);
	}

}
