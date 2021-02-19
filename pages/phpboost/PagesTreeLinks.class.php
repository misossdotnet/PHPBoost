<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2020 12 04
 * @since       PHPBoost 4.0 - 2016 11 25
 * @contributor xela <xela@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class PagesTreeLinks extends DefaultTreeLinks
{
	protected function get_module_additional_actions_tree_links(&$tree)
	{
		$module_id = 'pages';
		$current_user = AppContext::get_current_user()->get_id();

		$tree->add_link(new ModuleLink(LangLoader::get_message('my.items', 'common', $module_id), PagesUrlBuilder::display_member_items($current_user), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->write() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->contribution() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->moderation()));

	}
}
?>
