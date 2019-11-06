<?php

/**
 * Class MY_Controller
 */
class MY_Controller extends CI_Controller {

	/**
	 * @var string
	 */
	protected $string_validation_rules = 'trim|alpha|max_length[100]|xss_clean';

	/**
	 * @var string $email_validation_rules The validation rules for an email field
	 */
	protected $email_validation_rules = 'trim|valid_email';

	/**
	 * @var string $password_validation_rules The validation rules for a password field
	 */
	protected $password_validation_rules = 'trim|min_length[3]';

	/**
	 * MY_Controller constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->load->helper([
			'url',
			'html',
			'form',
			'security',
		]);
		$this->load->library([
			'session',
			'form_validation',
			'template',
		]);
	}

	/**
	 * Checks if the user is logged into the application.
	 * If they are not, the system will redirect to the application's logged out home page.
	 *
	 * This is used for pages where a user should be logged in.
	 */
	protected function confirm_user_logged_in() {
		$user_data = $this->session->userdata('user');
		if (empty($user_data)) {
			// Redirect to the application's home page (user is not logged in)
			redirect('user/index');
		}
	}

	/**
	 * Checks if the user is logged into the application.
	 * If they are, the system will redirect to the application's logged in home page.
	 *
	 * This is used for pages where a user should not be logged in.
	 */
	protected function confirm_user_logged_out() {
		$user_data = $this->session->userdata('user');
		if ($user_data) {
			// Redirect to the application's home page (user is logged in)
			redirect('user/home');
		}
	}

	/**
	 * Generates CSS class names for an input field based on the form validation results.
	 *
	 * @param string $name  The name of the input field
	 * @param string $value The value of the input field
	 *
	 * @return string
	 */
	protected function get_input_validation_class($name = '', $value = '') {
		$class = '';

		$field_error = form_error($name);

		if (! empty($field_error)) {
			$class = 'has-error has-feedback';
		} else {
			if (! empty($value) && $name != 'email') {
				$class = 'has-success has-feedback';
			}
		}

		return $class;
	}

	/**
	 * Generates a result message for an input field based on the form validation results.
	 *
	 * @param string $name  The name of the input field
	 * @param string $value The value of the input field
	 * @param bool $include_icon TRUE - include error/success icon; FALSE - doesn't include error/success icon
	 *
	 * @return string
	 */
	protected function get_input_validation_message($name = '', $value = '', $include_icon = TRUE) {
		$message = '';
		$field_error = form_error($name);

		if (! empty($field_error)) {
			if ($include_icon) {
				$message .= '<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>'
				           . '<span class="sr-only">(error)</span>';
			}
			$message .= '<span id="' . $name . '-error-help" class="help-block text-danger">' . $field_error . '</span>';
		} else {
			if (! empty($value) && $name != 'email') {
				if ($include_icon) {
					$message = '<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>'
					           . '<span class="sr-only">(success)</span>';
				}
			}
		}

		return $message;
	}

	/**
	 * Generates the name of the result message tag for an input field based on the form validation results.
	 * This is used to add the aria-describedby attribute on the input field.
	 *
	 * @param string $name  The name of the input field
	 *
	 * @return string
	 */
	protected function get_input_validation_attributes($name = '') {
		$attributes = '';

		$field_error = form_error($name);

		if (! empty($field_error)) {
			$attributes = $name . '-error-help';
		}

		return $attributes;
	}

	/**
	 * Adds the variables that are relevant to an input field on a view.
	 *
	 * E.g. The label, input HTML tag, error message for the input, help text for the input.
	 *
	 * @param array $form_data      The array that contains the form variables
	 * @param string $name          The prefix for the form variables
	 * @param string $label         The label for the input
	 * @param string $input         The input itself
	 * @param string $value         The value of the input. Used to get the error message value
     * @param string $help          The help text for the input
	 * @param string $field_name    The input name/id attribute. If left blank, uses the $name.
	 *
	 * @return array
	 */
	protected function add_form_input_data(
		$form_data = [], $name = '', $label = '', $input = '', $value = '', $help = '', $field_name = ''
	) {
		$data = [];

		if (empty($field_name)) {
			$field_name = $name;
		}

		if (! empty($form_data) && ! empty($input)) {
			$data = [
				$name . '_id' => $field_name,
				$name . '_label' => $label,
				$name . '_input_tag' => $input,
				$name . '_error' => $this->get_input_validation_message($field_name, $value),
				$name . '_class' => $this->get_input_validation_class($field_name, $value),
				$name . '_help' => $help,
			];
		}

		return array_merge($data, $form_data);
	}

	/**
	 * Adds the variables that are relevant to an input field on a view.
	 *
	 * E.g. The label, input HTML tag, error message for the input, help text for the input.
	 *
	 * @param array $form_data      The array that contains the form variables
	 * @param string $name          The prefix for the form variables
	 * @param string $label         The label for the input
	 * @param string $input         The input itself
	 * @param string $value         The value of the input. Used to get the error message value
	 * @param string $help          The help text for the input
	 * @param string $field_name    The input name/id attribute. If left blank, uses the $name.
	 *
	 * @return array
	 */
	protected function add_form_select_data (
		$form_data = [], $name = '', $label = '', $input = '', $value = '', $help = '', $field_name = ''
	) {
		$data = [];

		if (empty($field_name)) {
			$field_name = $name;
		}

		if (! empty($form_data) && ! empty($input)) {
			$data = [
				$name . '_label' => $label,
				$name . '_id' => $field_name,
				$name . '_select_tag' => $input,
				$name . '_error' => $this->get_input_validation_message($field_name, $value, FALSE),
				$name . '_class' => $this->get_input_validation_class($field_name, $value),
				$name . '_help' => $help,
			];
		}

		return array_merge($data, $form_data);
	}

	/**
	 * Generates the form input HTML for an email field.
	 *
	 * @param string $value     The value of the email field
	 * @param string $name      The name of the email field
	 * @param bool $required    TRUE - required field; FALSE - not required field
	 *
	 * @return string
	 */
	protected function get_email_input_field($value = '', $name = 'email', $required = TRUE) {
		return $this->get_input_field($value, $name, 'email', $required);
	}

	/**
	 * Generates the form input HTML for a password field.
	 *
	 * @param string $value     The value of the password field
	 * @param string $name      The name of the password field
	 * @param bool $required    TRUE - required field; FALSE - not required field
	 *
	 * @return string
	 */
	protected function get_password_input_field($value = '', $name = 'password', $required = TRUE) {
		return $this->get_input_field($value, $name, 'password', $required);
	}

	/**
	 * Generates the form select/dropdown HTML for a field.
	 *
	 * @param string $value     The value of the select field
	 * @param array $options    The list of choices inside the select field
	 * @param string $name      The name of the select field
	 * @param bool $required    TRUE - required field; FALSE - not required field
	 *
	 * @return string
	 */
	protected function get_select_field($value = '', $options = [], $name = '', $required = FALSE) {
		$select_attributes = [
			'name'             => $name,
			'id'               => $name,
			'class'            => 'form-control',
			'aria-describedby' => $this->get_input_validation_attributes($name),
		];

		if ($required) {
			$select_attributes['required'] = '';
		}

		return form_dropdown($name, $options, $value, $select_attributes);
	}

	/**
	 * Generates the form input HTML for a field.
	 *
	 * @param string $value     The value of the input field
	 * @param string $name      The name of the input field
	 * @param string $type      The type of input field (e.g. text, password, email)
	 * @param bool $required    TRUE - required field; FALSE - not required field
	 *
	 * @return string
	 */
	protected function get_input_field($value = '', $name = '', $type = 'text', $required = FALSE) {
		$input_attributes = [
			'name'             => $name,
			'id'               => $name,
			'type'             => $type,
			'class'            => 'form-control',
			'aria-describedby' => $this->get_input_validation_attributes($name),
			'value'            => $value,
		];

		if ($required) {
			$input_attributes['required'] = '';
		}

		return form_input($input_attributes);
	}

}
