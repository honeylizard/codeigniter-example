<?php

/**
 * Class User_model
 */
class User_model extends CI_Model {

	/**
	 * User_model constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->load->database();
		$this->load->model('user_auth_model', 'auth_model');
		$this->load->model('user_meta_model', 'meta_model');
		$this->load->model('user_settings_model', 'settings_model');
		$this->load->model('user_reset_tokens_model', 'reset_tokens_model');
	}

	/**
	 * Gets a specific set of information regarding the user and stores it in the session.
	 *
	 * If the user does not exist, the function returns an empty array.
	 *
	 * @param int $id   The user's ID
	 *
	 * @return array
	 */
	public function create_user_session($id = 0) {
		$user_data = [];

		if ($id > 0) {
			// Get the user information
			$auth_data = $this->auth_model->read_user_auth($id);
			$meta_data = $this->meta_model->read_user_meta($id);
			$settings_data = $this->settings_model->read_user_settings($id);

			$user_data = [
				'id' => $id,
				'username' => $auth_data['email'],
				'first-name' => $meta_data['first_name'],
				'last-name' => $meta_data['last_name'],
				'language' => $settings_data['language'],
			];

			// Create a user session
			$session_data = [
				'user' => $user_data,
			];
			$this->session->set_userdata( $session_data );
		}

		return $user_data;
	}

	/**
	 * Removes the user session data.
	 */
	public function destroy_user_session() {
		$user_data = $this->session->userdata('user');
		if ($user_data) {
			$this->session->unset_userdata('user');
			$this->session->sess_destroy();
		}
	}

	/**
	 * Checks if a user exists based on an email address and password.
	 *
	 * If a user exists, returns the user id. Otherwise, it returns 0.
	 *
	 * @param array $data   The user information
	 *
	 * @return mixed
	 */
	public function confirm_login($data) {
		return $this->auth_model->confirm_login($data['email'], $data['password']);
	}

	/**
	 * Checks if a user exists based on an email address.
	 *
	 * If a user exists, returns the user id. Otherwise, it returns 0.
	 *
	 * @param string $email The user's email address
	 *
	 * @return int
	 */
	public function does_user_exist($email) {
		return $this->auth_model->get_id_from_email($email);
	}

	/**
	 * Creates a new user based on an email address and password.
	 *
	 * @param array $data   The user information
	 *
	 * @return bool
	 */
	public function create($data) {
		$user_id = $this->auth_model->create_user($data['email'], $data['password']);

		$results = FALSE;

		if ($user_id > 0) {
			$user_meta_created = $this->meta_model->create_user_meta($user_id);
			$user_settings_created = $this->settings_model->create_user_settings($user_id);

			if ($user_meta_created && $user_settings_created) {
				$results = TRUE;
			}
		}

		return $results;
	}

	/**
	 * Soft deletes the user's information in the database.
	 *
	 * @param int $user_id  The user's ID
	 * @param int $deleted_by   The ID of the user that is performing the user deletion.
	 *
	 * @return bool
	 */
	public function soft_delete($user_id = 0, $deleted_by = 0) {
		$meta_deleted = $this->meta_model->delete_row($user_id);
		$settings_deleted = $this->settings_model->delete_row($user_id);
		$user_deleted = $this->auth_model->delete_user($user_id, $deleted_by);

		return ($meta_deleted && $settings_deleted && $user_deleted);
	}

	/**
	 * Restores the user's information in the database from a soft delete.
	 *
	 * @param int $user_id  The user's ID
	 * @param int $restored_by   The ID of the user that is performing the user restoration.
	 *
	 * @return bool
	 */
	public function restore_user($user_id = 0, $restored_by = 0) {
		$meta_restored = $this->meta_model->restore_row($user_id);
		$settings_restored = $this->settings_model->restore_row($user_id);
		$user_restored = $this->auth_model->restore_user($user_id, $restored_by);

		return ($meta_restored && $settings_restored && $user_restored);
	}

	/**
	 * Checks if the token provided by the user is:
	 * 1. An actual token for the user
	 * 2. The token's creation is within the lifespan of a token.
	 *
	 * Note: This can be changed at a later time so that the token
	 * rows are deleted by a scheduled internal system scripts.
	 * This would remove the need to check for TTL since the row
	 * would not exist.
	 *
	 * @param string $user_token   The user's provided token
	 *
	 * @return bool
	 */
	public function is_token_valid($user_token = '') {
		$token = substr($user_token, 0, 30);
		$user_id = substr($user_token, 30);

		$token_data = $this->reset_tokens_model->read_user_reset_token($user_id, $token);

		$valid = FALSE;

		if ($token_data) {
			// Use Server Timezone To Get Current Timestamp
			$timezone = 'America/New_York';
			$date_time = new DateTime("now", new DateTimeZone($timezone));
			$date_time->setTimestamp(time());
			$now = $date_time->format('Y-m-d H:i:s');

			$valid = $this->compare_timestamps(
				$token_data['created'], $now, $this->reset_tokens_model->get_time_to_live()
			);
		}

		return $valid;
	}

	/**
	 * Create a new password reset token row in the database for the user.
	 *
	 * @param int $user_id  The user's ID
	 *
	 * @return string
	 */
	public function create_password_reset_token($user_id = 0) {
		$token = '';

		if ($user_id > 0) {
			// Generate the token
			$new_token = substr(sha1(rand()), 0, 30);

			$query = $this->reset_tokens_model->create_token($user_id, $new_token);

			if ($query) {
				// Make the user's front-end token a combination of
				// the backend token and their user ID.
				$token = $new_token . $user_id;
			}
		}

		return $token;
	}

	/**
	 * Updates a specific user's password.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * @param array $data  The user's information
	 *
	 * @return bool
	 */
	public function update_password($data = []) {
		$results = FALSE;

		if (isset($data['id']) && isset($data['password'])) {
			$results = $this->auth_model->update_password($data['id'], $data['password']);
		}

		return $results;
	}

	/**
	 * Get the user's information based on a password reset token.
	 *
	 * @param string $token     The password reset token.
	 *
	 * @return array
	 */
	public function get_user_from_token($token = '') {
		$user_id = substr($token, 30);

		$user = $this->auth_model->read_user_auth($user_id);

		return [
			'id' => $user_id,
			'email' => $user['email'],
		];
	}

	/**
	 * Removes the token from the database.
	 *
	 * @param string $user_token   The user's provided token
	 *
	 * @return mixed
	 */
	public function remove_token($user_token = '') {
		$token = substr($user_token, 0, 30);
		$user_id = substr($token, 30);

		return $this->reset_tokens_model->delete_token($user_id, $token);
	}

	/**
	 * Compares two timestamps against a TTL (time to live) duration.
	 * If the difference between the two timestamps is less than the TTL, returns true.
	 * Otherwise, returns false.
	 *
	 * @param string $start_timestamp   The starting timestamp
	 * @param string $end_timestamp     The ending timestamp
	 * @param string $ttl               The TTL (time to live)
	 *
	 * @return bool
	 */
	private function compare_timestamps($start_timestamp = '', $end_timestamp = '', $ttl = '') {
		$date1 = new DateTime($start_timestamp);
		$date2 = new DateTime($end_timestamp);

		$interval = $date2->diff($date1);
		$hours = $interval->format('%h');

		return $hours < $ttl;
	}

	/**
	 * Updates a specific user's settings.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * @param array $data  The user's information
	 *
	 * @return bool
	 */
	public function update_settings($data = []) {
		$results = FALSE;

		if (isset($data['id']) && isset($data['language'])) {
			$results = $this->settings_model->update_language($data['id'], $data['language']);

			// If the table is updated, update the session data
			if ($results) {
				$user_data = $this->session->userdata('user');
				$user_data['language'] = $data['language'];

				$this->session->set_userdata('user', $user_data);
			}
		}

		return $results;
	}

	/**
	 * Updates a specific user's profile.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * @param array $data  The user's information
	 *
	 * @return bool
	 */
	public function update_profile($data = []) {
		$results = FALSE;

		if (isset($data['id']) && isset($data['email'])) {
			$meta_results = $this->meta_model->update_profile($data['id'], $data['first_name'], $data['last_name']);
			$auth_results = $this->auth_model->update_email($data['id'], $data['email']);

			// If the table is updated, update the session data
			if ($auth_results && $meta_results) {
				$results = TRUE;
				$user_data = $this->session->userdata('user');
				$user_data['username'] = $data['email'];
				$user_data['first-name'] = $data['first_name'];
				$user_data['last-name'] = $data['last_name'];
				$this->session->set_userdata('user', $user_data);
			}
		}

		return $results;
	}

}
