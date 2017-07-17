<?php

/**
 * Class User
 */
class User extends MY_Controller {

	/**
	 * User constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->load->library([
			'email',
		]);
		$this->load->database();
		$this->load->model('user_model');
	}

	/**
	 * Displays the initial page for the application.
	 */
	public function index() {
		$this->confirm_user_logged_out();

		$data = [
			'title' => lang('home'),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents' , 'home', $data);
	}

	/**
	 * Displays the initial page a user sees when they log into the application.
	 */
	public function home() {
		$this->confirm_user_logged_in();

		$data = [
			'title' => lang('welcome'),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents' , 'app/home', $data);
	}

	/**
	 * Displays the user forgot password page
	 */
	public function forgot() {
		$this->confirm_user_logged_out();

		$email_value = $this->input->post('email');

		$flash_message = $this->session->flashdata('flash_message');
		$message = isset($flash_message) ? $flash_message : '';

		if (! empty($email_value)) {
			// set form validation rules
			$this->form_validation->set_rules(
				'email', lang('email_address'), $this->email_validation_rules . '|required'
			);

			$form_validated = $this->form_validation->run();
			if ($form_validated) {
				if ($this->user_model->does_user_exist($email_value)) {
					$alert_data = [
						'title' => lang('password_reset_sent_alert_title'),
						'message' => lang('password_reset_sent_alert_body'),
					];
					$message = $this->load->view('alerts/success_title.php', $alert_data, TRUE);
					$this->send_password_reset_email($email_value);
				}
			} else {
				$alert_data = [
					'message' => lang('error_occurred'),
				];
				$message = $this->load->view('alerts/error.php', $alert_data, TRUE);
			}
		}

		$this->get_forgot_view($email_value, $message);
	}

	/**
	 * Displays the user password reset page.
	 *
	 * @param string $encoded_token     The encoded token from the URL
	 */
	public function reset_password($encoded_token = '') {
		$this->confirm_user_logged_out();

		$token_valid = FALSE;

		if (! empty($encoded_token)) {
			$decoded_token = base64_decode(urldecode($encoded_token));
			$token_valid = $this->user_model->is_token_valid($decoded_token);
			$this->session->set_flashdata('flash_token', $encoded_token);
		}

		if ($token_valid) {
			$password_value = $this->input->post('password');
			$confirm_password_value = $this->input->post('confirm-password');
			$message = '';

			$user_data = $this->user_model->get_user_from_token($decoded_token);
			$user_id = $user_data['id'];
			$user_email = $user_data['email'];

			if (! empty($password_value) && ! empty($confirm_password_value)) {
				$this->form_validation->set_rules(
					'password', lang('new_password'), $this->password_validation_rules . '|required'
				);
				$this->form_validation->set_rules(
					'confirm-password', lang('confirm_new_password'), 'trim|required|matches[password]'
				);

				$form_validated = $this->form_validation->run();

				$alert_data = [
					'message' => lang('error_occurred'),
				];
				$message = $this->load->view('alerts/error.php', $alert_data, TRUE);

				if ($form_validated) {
					$data = [
						'id'       => $user_id,
						'email'    => $user_email,
						'password' => $password_value,
					];

					$user_exists = $this->user_model->does_user_exist($data['email']);

					if ($user_exists > 0) {
						$is_user_updated = $this->user_model->update_password($data);

						if ($is_user_updated) {
							// Add email to session temporarily so that the login page can use the email
							$this->session->set_flashdata('temp_email', $data['email']);

							$alert_data = [
								'title' => lang('password_updated'),
								'message' => lang('password_updated_alert_body'),
								'url' => base_url('user/login'),
								'url_text' => lang('login_now'),
							];
							$message    = $this->load->view('alerts/success_title_link.php', $alert_data, TRUE);

							// Remove the token from the system since it has been used
							$this->user_model->remove_token($decoded_token);

							$password_value         = '';
							$confirm_password_value = '';
						}
					}

				}
			}

			$this->get_password_reset_view($password_value, $confirm_password_value, $message);
		} else {
			$alert_data = [
				'title' => lang('oops'),
				'message' => lang('expired_password_link_alert_body'),
			];
			$this->session->set_flashdata('flash_message', $this->load->view('alerts/error_title.php', $alert_data, TRUE));
			redirect('user/forgot');
		}
	}

	/**
	 * Displays the user registration page
	 */
	public function register() {
		$this->confirm_user_logged_out();

		$email_value = $this->input->post('email');
		$password_value = $this->input->post('password');
		$confirm_password_value = $this->input->post('confirm-password');
		$message = '';

		if (! empty($email_value)
		    && ! empty($password_value)
		    && ! empty($confirm_password_value)
		) {
			// set form validation rules
			$this->form_validation->set_rules(
				'email', lang('email_address'), $this->email_validation_rules . '|required'
			);
			$this->form_validation->set_rules(
				'password', lang('password'), $this->password_validation_rules . '|required'
			);
			$this->form_validation->set_rules(
				'confirm-password', lang('confirm_password'), 'trim|required|matches[password]'
			);

			$form_validated = $this->form_validation->run();

			$alert_data = [
				'message' => lang('error_occurred'),
			];
			$message = $this->load->view('alerts/error.php', $alert_data, TRUE);

			if ($form_validated) {
				$data = [
					'email'      => $email_value,
					'password'   => $password_value
				];

				$user_exists = $this->user_model->does_user_exist($data['email']);

				if (! $user_exists) {
					$is_user_created = $this->user_model->create($data); // Create the user

					if ($is_user_created > 0) {
						$alert_data = [
							'title' => lang('registered_alert_title'),
							'message' => lang('registered_alert_body'),
							'url' => base_url('user/login'),
							'url_text' => lang('login_now'),
						];
						$message = $this->load->view('alerts/success_title_link.php', $alert_data, TRUE);

						// Add email to session temporarily so that the login page can use the email
						$this->session->set_flashdata('temp_email', $data['email']);

						$email_value = '';
						$password_value = '';
						$confirm_password_value = '';
					}
				}
			}
		}

		$this->get_register_view(
			$email_value, $password_value, $confirm_password_value, $message
		);
	}

	/**
	 * Displays the user login page
	 */
	public function login() {
		$this->confirm_user_logged_out();

		$provided_email = $this->session->flashdata('temp_email');

		$email_value = isset($provided_email) ? $provided_email : $this->input->post('email');
		$password_value = $this->input->post('password');
		$message = '';

		if (! empty($email_value) && ! empty($password_value)) {
			// set form validation rules
			$this->form_validation->set_rules(
				'email', lang('email_address'), $this->email_validation_rules . '|required'
			);
			$this->form_validation->set_rules(
				'password', lang('password'), $this->password_validation_rules . '|required'
			);

			$form_validated = $this->form_validation->run();
			if ($form_validated) {
				$data = [
					'email'    => $email_value,
					'password' => $password_value
				];

				// Check if the email is already used by a user
				$user_id = $this->user_model->confirm_login($data);
				if ($user_id) {
					$this->user_model->create_user_session($user_id);
					redirect('user/home', 'refresh'); // Redirect to the user's logged in home page
				} else {
					$alert_data = [
						'title' => lang('login_error_alert_title'),
						'message' => lang('login_error_alert_body'),
					];
					$message = $this->load->view('alerts/error_title.php', $alert_data, TRUE);
				}
			} else {
				$alert_data = [
					'title' => lang('login_error_alert_title'),
					'message' => lang('login_error_alert_body'),
				];
				$message = $this->load->view('alerts/error_title.php', $alert_data, TRUE);
			}
		}

		$this->get_login_view($email_value, $password_value, $message);
	}

	/**
	 * Destroys a user's session and redirects them to the logged out home page.
	 */
	public function logout() {
		// Destroy the user session
		$this->user_model->destroy_user_session();

		// Redirect to the application's home page (user is not logged in)
		redirect('user/index', 'refresh');
	}

	/**
	 * Generates the register HTML form view.
	 *
	 * @param string $password_value            The value of the password field
	 * @param string $confirm_password_value    The value of the confirm password field
	 * @param string $message                   The message for the form
	 */
	private function get_password_reset_view($password_value = '', $confirm_password_value = '', $message = '') {
		$password_input = $this->get_password_input_field($password_value);

		$confirm_password_input = $this->get_password_input_field($confirm_password_value, 'confirm-password');

		$form_data = [
			'message' => $message,

			'form_tag' => form_open('user/reset_password/' . $this->session->flashdata('flash_token'), [
				'id' => 'password-reset-form',
			]),

			'submit_label' => lang('change_password'),
		];

		$form_data = $this->add_form_input_data(
			$form_data, 'password', lang('new_password'), $password_input, $password_value, lang('character_length_3')
		);
		$form_data = $this->add_form_input_data(
			$form_data, 'confirm_password', lang('confirm_new_password'), $confirm_password_input,
			$confirm_password_value, lang('must_match_password'), 'confirm-password'
		);

		$data = [
			'title'         => lang('password_reset'),
			'password_reset_form' => $this->load->view('forms/password_reset.php', $form_data, TRUE),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents', 'password_reset', $data);
	}

	/**
	 * Generates the register HTML form view.
	 *
	 * @param string $email_value
	 * @param string $password_value
	 * @param string $confirm_password_value
	 * @param string $message
	 */
	private function get_register_view(
		$email_value = '', $password_value = '', $confirm_password_value = '', $message = ''
	) {
		$email_input = $this->get_email_input_field($email_value);

		$password_input = $this->get_password_input_field($password_value);

		$confirm_password_input = $this->get_password_input_field($confirm_password_value, 'confirm-password');

		$form_data = [
			'message' => $message,
			'form_tag' => form_open('user/register', [
				'id' => 'register-form',
			]),
			'submit_label' => lang('create_account'),
		];

		$form_data = $this->add_form_input_data(
			$form_data, 'email', lang('email'), $email_input, $email_value, lang('we_value_privacy')
		);
		$form_data = $this->add_form_input_data(
			$form_data, 'password', lang('password'), $password_input, $password_value, lang('character_length_3')
		);
		$form_data = $this->add_form_input_data(
			$form_data, 'confirm_password', lang('confirm_password'), $confirm_password_input,
			$confirm_password_value, lang('must_match_password'), 'confirm-password'
		);

		$data = [
			'title'         => lang('register'),
			'register_form' => $this->load->view('forms/register.php', $form_data, TRUE),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents', 'register', $data);
	}

	/**
	 * Generates the forgot password HTML form view.
	 *
	 * @param string $email_value   The value of the email input field
	 * @param string $message       A message that will be displayed above the form
	 */
	private function get_forgot_view($email_value = '', $message = '') {
		$email_input = $this->get_email_input_field($email_value);

		$form_data = [
			'message' => $message,

			'form_tag' => form_open('user/forgot', [
				'id' => 'forgot-password-form',
			]),

			'description' => lang('please_enter_email'),

			'submit_label' => lang('email_password_reset'),
		];

		$form_data = $this->add_form_input_data($form_data, 'email', lang('email'), $email_input, $email_value);

		$data = [
			'title'                => lang('forgot_password'),
			'forgot_password_form' => $this->load->view('forms/forgot.php', $form_data, TRUE),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents', 'forgot', $data);
	}

	/**
	 * Generates the login HTML form view.
	 *
	 * @param string $email_value   The value of the email input field
	 * @param string $password_value    The value of the password input field
	 * @param string $message   A message that will be displayed above the form
	 */
	private function get_login_view($email_value = '', $password_value = '', $message = '') {
		$email_input = $this->get_email_input_field($email_value);

		$password_input = $this->get_password_input_field($password_value);

		$form_data = [
			'message' => $message,

			'form_tag' => form_open('user/login', [
				'id' => 'login-form',
			]),

			'forgot_password_label' => lang('forgot_password'),
			'forgot_password_url'   => base_url('user/forgot'),

			'submit_label' => lang('login'),
		];

		$form_data = $this->add_form_input_data($form_data, 'email', lang('email'), $email_input, $email_value);

		$form_data = $this->add_form_input_data($form_data, 'password', lang('password'), $password_input, $password_value);

		$data = [
			'title' => lang('login'),
			'login_form' => $this->load->view('forms/login.php', $form_data, TRUE),
		];

		$this->template->set('meta_title', 'CodeIgniter Tutorial');
		$this->template->set('meta_description', '');
		$this->template->set('meta_keywords', '');

		$this->template->load('default', 'contents', 'login', $data);
	}

	/**
	 * Generates a password reset token and sends an email to the user (if they exist in the system).
	 *
	 * The email contains a link to the password reset form that includes the token as a parameter.
	 *
	 * @param string $email The user's email address
	 */
	private function send_password_reset_email($email = '') {
		$user_id = $this->user_model->does_user_exist($email);

		$token = $this->user_model->create_password_reset_token($user_id);

		if ($token) {
			$encoded_token = urlencode(base64_encode($token));

			$email_data = [
				'first_name' => '',
				'application_name' => $this->config->item('application_name'),
				'reset_url' => base_url('user/reset_password/') . $encoded_token,
			];

			$email_body = $this->load->view('email/forgot.php', $email_data, TRUE);

			$this->email->from($this->config->item('default_email'), $this->config->item('application_name'));
			$this->email->to($email);
			$this->email->subject(lang('password_reset'));
			$sent = $this->email->message($email_body);

			if ($sent) {
				// Email was successfully sent
			} else {
				// Could not email the user
			}

			// TODO: Until an email server is configured, dumping the email reset url
			var_dump($email_data['reset_url']);
		}
	}

}
