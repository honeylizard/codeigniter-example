<?php

/**
 * Class MY_Model
 */
class MY_Model extends CI_Model {

	/**
	 * @var string $table   The name of the database table.
	 */
	protected $table = '';

	/**
	 * @var string $primary_key    The column name for the table's primary key.
	 */
	protected $primary_key = 'id';

	/**
	 * @var array $valid_fields The list of table fields.
	 */
	protected $valid_fields = [];

	/**
	 * @var string $field_deleted   The column name for the table's deleted flag field.
	 */
	protected $field_deleted = 'deleted';

	/**
	 * MY_Model constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Gets the row information based on the row's ID.
	 *
	 * @param int $primary_key   The row's ID
	 * @param string $select_fields The table fields to return (e.g. 'id, name')
	 *
	 * @return array
	 */
	public function read_row($primary_key = 0, $select_fields = '*') {
		$user = [];

		if ($primary_key > 0) {
			$this->db->select($select_fields);
			$this->db->from($this->table);
			$this->db->where($this->primary_key, $primary_key);

			/* @var $query CI_DB_result */
			$query = $this->db->get()->result_array();

			if ($query) {
				$user = $query[0];
			}
		}

		return $user;
	}

	/**
	 * Updates a specific row.
	 * Returns true if a row was updated. Returns false if nothing was updated.
	 *
	 * @param int $primary_key      The row's ID.
	 * @param array $new_row   The row's new information.
	 *
	 * @return bool
	 */
	public function update_fields($primary_key = 0, $new_row = []) {
		$result = FALSE;

		if ($primary_key > 0 && ! empty($new_row)) {
			$row_data = [
				$this->primary_key => $primary_key,
			];

			foreach ($new_row as $key => $value) {
				if (in_array($key, $this->valid_fields)) {
					$row_data[$key] = $value;
				}
			}

			$this->db->where($this->primary_key, $primary_key);
			$result = $this->db->update($this->table, $row_data);
		}

		return $result;
	}

	/**
	 * Updates a specific field in a specific row.
	 * Returns true if a row was updated. Returns false if nothing was updated.
	 *
	 * @param int $primary_key  The row's ID.
	 * @param string $field     The field's name.
	 * @param string $new_value  The field's new value.
	 *
	 * @return bool
	 */
	public function update_field($primary_key = 0, $field = '', $new_value = '') {
		$result = FALSE;

		if ($primary_key > 0 && in_array($field, $this->valid_fields) && ! empty($new_value)) {
			$user = [
				$this->primary_key => $primary_key,
				$field => $new_value,
			];

			$this->db->where($this->primary_key, $primary_key);
			$result = $this->db->update($this->table, $user);
		}

		return $result;
	}

	/**
	 * Turns the delete flag 'on' on a specific row.
	 * Returns true if a row was updated. Returns false if nothing was updated.
	 *
	 * Table row deletion will be handled by scheduled internal system scripts.
	 *
	 * @param int $primary_key  The row's ID
	 *
	 * @return bool
	 */
	public function delete_row($primary_key = 0) {
		$result = FALSE;

		if ($primary_key > 0 && in_array($this->field_deleted, $this->valid_fields)) {
			$row_data = [
				$this->primary_key => $primary_key,
				$this->field_deleted => 1,
			];

			$this->db->where($this->primary_key, $primary_key);
			$result = $this->db->update($this->table, $row_data);
		}

		return $result;
	}

	/**
	 * Turns the delete flag 'off' on a specific row.
	 * Returns true if a row was updated. Returns false if nothing was updated.
	 *
	 * @param int $primary_key  The row's ID
	 *
	 * @return bool
	 */
	public function restore_row($primary_key = 0) {
		$result = FALSE;

		if ($primary_key > 0 && in_array($this->field_deleted, $this->valid_fields)) {
			$row_data = [
				$this->primary_key => $primary_key,
				$this->field_deleted => 0,
			];

			$this->db->where($this->primary_key, $primary_key);
			$result = $this->db->update($this->table, $row_data);
		}

		return $result;
	}

}
