<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2020 05 14
 * @since       PHPBoost 4.0 - 2014 08 24
*/

class DownloadSearchable extends DefaultSearchable
{
	public function __construct()
	{
		$module_id = 'download';
		parent::__construct($module_id);
		$this->read_authorization = DownloadAuthorizationsService::check_authorizations()->read();

		$this->table_name = DownloadSetup::$download_table;

		$this->authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, DownloadConfig::load()->is_summary_displayed_to_guests(), $module_id);

		$this->use_keywords = true;

		$this->field_title = 'name';
		$this->field_rewrited_title = 'rewrited_name';
		$this->field_content = 'contents';

		$this->has_summary = true;
		$this->field_summary = 'short_contents';

		$this->field_published = 'approbation_type';

		$this->has_validation_period = true;
		$this->field_validation_start_date = 'start_date';
		$this->field_validation_end_date = 'end_date';
	}
}
?>
