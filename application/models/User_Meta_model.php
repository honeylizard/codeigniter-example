<?php

/**
 * Class User_Meta_model
 *
 * User Meta Information Model
 */
class User_Meta_model extends MY_Model {

	/**
	 * @var string $table   The name of the database table.
	 */
	protected $table = 'user_meta';

	/**
	 * @var string $primary_key    The column name for the table's primary key.
	 */
	protected $primary_key = 'user_id';

	/**
	 * @var string $field_first_name    The column name for the table's first name field.
	 */
	private $field_first_name = 'first_name';

	/**
	 * @var string $field_last_name     The column name for the table's last name field.
	 */
	private $field_last_name = 'last_name';

	/**
	 * User_Meta_model constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->valid_fields = [
			$this->primary_key,
			$this->field_first_name,
			$this->field_last_name,
			$this->field_deleted,
		];
	}

	/**
	 * Creates a new user meta based on a provided user ID, first name, and last name.
	 * Returns true if a user meta was created. Returns false if nothing was created.
	 *
	 * @param int $user_id  The user's ID
	 * @param string $first_name  The user's first name
	 * @param string $last_name  The user's last name
	 *
	 * @return bool
	 */
	public function create_user_meta($user_id = 0, $first_name = '', $last_name = '') {
		$result = FALSE;

		if ($user_id > 0) {
			$user_meta = [
				$this->primary_key => $user_id,
				$this->field_first_name => $first_name,
				$this->field_last_name => $last_name,
			];

			$this->db->insert($this->table, $user_meta);
			$result = $this->db->insert_id();
		}

		return $result;
	}

	/**
	 * Gets the user's meta information based on the user's ID.
	 *
	 * @param int $user_id   The user's ID
	 *
	 * @return array
	 */
	public function read_user_meta($user_id = 0) {
		return $this->read_row($user_id, 'user_id, first_name, last_name');
	}

	/**
	 * Updates the user's meta information based on the user's ID and new meta information.
	 *
	 * @param int $user_id              The user's ID
	 * @param string $new_first_name    The user's new first name
	 * @param string $new_last_name     The user's new last name
	 *
	 * @return bool
	 */
	public function update_profile($user_id = 0, $new_first_name = '', $new_last_name = '') {
		$result = FALSE;

		if ($user_id > 0) {
			$user_meta = [
				$this->primary_key => $user_id,
				$this->field_first_name => $new_first_name,
				$this->field_last_name => $new_last_name,
			];

			$this->db->where($this->primary_key, $user_id);
			$result = $this->db->update($this->table, $user_meta);
		}

		return $result;
	}

}
