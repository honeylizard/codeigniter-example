<?php

/**
 * Class User_Auth_model
 *
 * User Authentication Information Model
 */
class User_Auth_model extends MY_Model {

	/**
	 * @var string $table   The name of the database table.
	 */
	protected $table = 'user_auth';

	/**
	 * @var string $field_email The column name for the table's email field.
	 */
	private $field_email = 'email';

	/**
	 * @var string $field_password The column name for the table's password field.
	 */
	private $field_password = 'password';

	/**
	 * @var string $field_deleted_timestamp The column name for the table's deleted timestamp field.
	 */
	private $field_deleted_timestamp = 'deleted_on';

	/**
	 * User_Auth_model constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->valid_fields = [
			$this->primary_key,
			$this->field_email,
			$this->field_password,
			$this->field_deleted,
			$this->field_deleted_timestamp,
		];
	}

	/**
	 * Creates a new user based on a provided email address and password.
	 * Returns true if a user was created. Returns false if nothing was created.
	 *
	 * @param string $email     The new user's email address
	 * @param string $password  The new user's password
	 *
	 * @return bool
	 */
	public function create_user($email, $password) {
		$result = FALSE;

		if (! empty($email) && ! empty($password)) {
			$user = [
				$this->field_email => $email,
				$this->field_password => $this->create_password_hash($password),
			];

			$this->db->insert($this->table, $user);
			$result = $this->db->insert_id();
		}

		return $result;
	}

	/**
	 * Gets the user's information based on the user's ID.
	 *
	 * @param int $id   The user's ID
	 *
	 * @return array
	 */
	public function read_user_auth($id = 0) {
		return $this->read_row($id, 'id, email');
	}

	/**
	 * Gets the user's ID based on the user's email.
	 * If a user cannot be found, a zero will be returned.
	 *
	 * @param string $email     The user's email address
	 *
	 * @return int
	 */
	public function get_id_from_email($email = '') {
		$id = 0;

		if (! empty($email)) {
			$this->db->select($this->primary_key);
			$this->db->from($this->table);
			$this->db->where($this->field_email, $email);

			/* @var $query CI_DB_result */
			$query = $this->db->get();

			if ($query->num_rows() >= 1) {
				$id = $query->row($this->primary_key);
			}
		}

		return $id;
	}

	/**
	 * Gets the user's ID based on the user's email and password.
	 * If a user cannot be found, a zero will be returned.
	 *
	 * @param string $email     The user's email address
	 * @param string $password  The user's password
	 *
	 * @return int
	 */
	public function confirm_login($email = '', $password = '') {
		$id = 0;

		if (! empty($email) && ! empty($password)) {
			$this->db->select('id, password');
			$this->db->from($this->table);
			$this->db->where($this->field_email, $email);
			$this->db->where($this->field_deleted, 0);

			/* @var $query CI_DB_result */
			$query = $this->db->get()->result_array();

			if ($query) {
				$result = $query[0];

				if ($result) {
					$hash = $result[$this->field_password];
					$password_matches = $this->verify_password_hash($password, $hash);

					$user_id = $result[$this->primary_key];
					if ($user_id > 0 && $password_matches) {
						$id = $user_id;
					}
				}
			}
		}

		return $id;
	}

	/**
	 * Updates a specific user's password.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * @param int $user_id  The user's ID.
	 * @param string $new_password  The user's new password.
	 *
	 * @return bool
	 */
	public function update_password($user_id = 0, $new_password = '') {
		return $this->update_field($user_id, $this->field_password, $this->create_password_hash($new_password));
	}

	/**
	 * Updates a specific user's email if the email address is not already in use.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * @param int $user_id  The user's ID.
	 * @param string $new_email  The user's new email.
	 *
	 * @return bool
	 */
	public function update_email($user_id = 0, $new_email = '') {
		$results = FALSE;

		$associated_user = $this->get_id_from_email($new_email);

		// Check if the new email is already used by any user in the system
		if ($associated_user == 0) {
			$results = $this->update_field($user_id, $this->field_email, $new_email);
		} else if ($associated_user == $user_id) {
			// we don't need to update the table since there is no change
			$results = TRUE;
		}

		return $results;
	}

	/**
	 * Turns the delete flag 'on' on a specific user and adds a deleted timestamp.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * Table row deletion will be handled by scheduled internal system scripts.
	 *
	 * @param int $user_id  The user's ID
	 * @param int $deleted_by   The ID of the user that is performing the user deletion.
	 *
	 * @return bool
	 */
	public function delete_user($user_id = 0, $deleted_by = 0) {
		$result = FALSE;

		if ($user_id > 0) {
			$user = [
				$this->primary_key => $user_id,
				$this->field_deleted => 1,
				$this->field_deleted_timestamp => date("Y-m-d H:i:s"),
			];

			$this->db->where($this->primary_key, $user_id);
			$result = $this->db->update($this->table, $user);
		}

		return $result;
	}

	/**
	 * Turns the delete flag 'off' on a specific user and clears the deleted timestamp.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * @param int $user_id  The user's ID
	 * @param int $restored_by   The ID of the user that is performing the user restoration.
	 *
	 * @return bool
	 */
	public function restore_user($user_id = 0, $restored_by = 0) {
		$result = FALSE;

		if ($user_id > 0) {
			$user = [
				$this->primary_key => $user_id,
				$this->field_deleted => 0,
				$this->field_deleted_timestamp => NULL,
			];

			$this->db->where($this->primary_key, $user_id);
			$result = $this->db->update($this->table, $user);
		}

		return $result;
	}

	/**
	 * Generates a hashed password.
	 *
	 * @param string $password  The password that needs to be hashed.
	 *
	 * @return string
	 */
	private function create_password_hash($password = '') {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	/**
	 * Checks the provided password against the hash.
	 * If it matches, returns true. If it does not match, returns false.
	 *
	 * @param string $password  The password to verify
	 * @param string $hash      The hash to check against
	 *
	 * @return bool
	 */
	private function verify_password_hash($password = '', $hash = '') {
		return password_verify($password, $hash);
	}

}
