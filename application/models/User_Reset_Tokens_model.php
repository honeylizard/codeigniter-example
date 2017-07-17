<?php

/**
 * Class User_Reset_Tokens_model
 */
class User_Reset_Tokens_model extends MY_Model {

	/**
	 * @var string $table   The name of the database table.
	 */
	protected $table = 'user_reset_tokens';

	/**
	 * @var string $field_user_id    The column name for the table's user ID field.
	 */
	protected $field_user_id = 'user_id';

	/**
	 * @var string $field_token The column name for the table's token field.
	 */
	private $field_token = 'token';

	/**
	 * @var int $time_to_live   The token's maximum lifespan or TTL (time to live) in hours
	 */
	private $time_to_live = 24;

	/**
	 * User_Reset_Tokens_model constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->valid_fields = [
			$this->primary_key,
			$this->field_user_id,
			$this->field_token,
		];
	}

	/**
	 * Create a new user reset token based on a provided user ID, and token.
	 * Returns true if a user reset token was created. Returns false if nothing was created.
	 *
	 * @param int $user_id The user's ID
	 * @param string $token   The user's reset token
	 *
	 * @return bool
	 */
	public function create_token($user_id = 0, $token = '') {
		$result = FALSE;

		if ($user_id > 0 && ! empty($token)) {
			$data = [
				$this->field_user_id => $user_id,
				$this->field_token => $token,
			];

			$query = $this->db->insert($this->table, $data);
			if ($query) {
				$result = TRUE;
			}
		}

		return $result;
	}

	/**
	 * Deletes the token row in the table.
	 *
	 * @param int $user_id The user's ID
	 * @param string $token   The user's reset token
	 *
	 * @return bool
	 */
	public function delete_token($user_id = 0, $token = '') {
		$result = FALSE;

		if ($user_id > 0 && ! empty($token)) {
			$data = [
				$this->field_user_id => $user_id,
				$this->field_token => $token,
			];

			$query = $this->db->delete($this->table, $data);

			if ($query) {
				$result = TRUE;
			}
		}

		return $result;
	}

	/**
	 * Gets the user reset token information based on the user's ID.
	 *
	 * @param int $user_id   The user's ID
	 * @param string $token   The user's reset token
	 *
	 * @return array
	 */
	public function read_user_reset_token($user_id = 0, $token = '') {
		$tokens = [];

		$select_fields = 'user_id, token, created';

		if ($user_id > 0) {
			$this->db->select($select_fields);
			$this->db->from($this->table);
			$this->db->where($this->field_user_id, $user_id);
			$this->db->where($this->field_token, $token);

			/* @var $query CI_DB_result */
			$query = $this->db->get()->result_array();

			if ($query) {
				$tokens = $query[0];
			}
		}

		return $tokens;
	}

	/**
	 * Gets the token's TTL (time to live).
	 *
	 * @return int
	 */
	public function get_time_to_live() {
		return $this->time_to_live;
	}

}
