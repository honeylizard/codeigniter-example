<?php

/**
 * Class User_Settings_model
 *
 * User Settings Information Model
 */
class User_Settings_model extends MY_Model {

	/**
	 * @var string $table   The name of the database table.
	 */
	protected $table = 'user_settings';

	/**
	 * @var string $primary_key    The column name for the table's primary key.
	 */
	protected $primary_key = 'user_id';

	/**
	 * @var string $field_language The column name for the table's language field.
	 */
	private $field_language = 'language';

	/**
	 * @var array $valid_languages The list of languages available for the application.
	 */
	private $valid_languages = [];

	/**
	 * @var string $default_language The default language for the user.
	 */
	private $default_language;

	/**
	 * User_Settings_model constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->valid_fields = [
			$this->primary_key,
			$this->field_language,
			$this->field_deleted,
		];

		$this->valid_languages = [
			'en-US', // Default
			'en-GB',
		];
		$this->default_language = $this->valid_languages[0];
	}

	/**
	 * Creates a new user settings based on a provided user ID, and language.
	 * Returns true if a user settings was created. Returns false if nothing was created.
	 *
	 * @param int $user_id  The user's ID
	 * @param string $language  The user's language for the application.
	 *
	 * @return bool
	 */
	public function create_user_settings($user_id = 0, $language = '') {
		$result = FALSE;

		if ($user_id > 0) {
			if (! in_array($language, $this->valid_languages)) {
				$language = $this->default_language;
			}

			$user_meta = [
				$this->primary_key => $user_id,
				$this->field_language => $language,
			];

			$this->db->insert($this->table, $user_meta);
			$result = $this->db->insert_id();
		}

		return $result;
	}

	/**
	 * Gets the user settings information based on the user's ID.
	 *
	 * @param int $user_id   The user's ID
	 *
	 * @return array
	 */
	public function read_user_settings($user_id = 0) {
		return $this->read_row($user_id, 'user_id, language');
	}

	/**
	 * Updates a specific user's display language.
	 * Returns true if a user was updated. Returns false if nothing was updated.
	 *
	 * @param int $user_id  The user's ID.
	 * @param string $new_language  The user's new language.
	 *
	 * @return bool
	 */
	public function update_language($user_id = 0, $new_language = '') {
		$results = FALSE;

		if (in_array($new_language, $this->valid_languages)) {
			$results = $this->update_field($user_id, $this->field_language, $new_language);
		}

		return $results;
	}

}
