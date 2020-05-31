<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2020 05 31
 * @since       PHPBoost 3.0 - 2011 10 08
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class StatsModuleMiniMenu extends ModuleMiniMenu
{
	public function get_default_block()
	{
		return self::BLOCK_POSITION__LEFT;
	}

	public function get_menu_id()
	{
		return 'module-mini-stats';
	}

	public function get_menu_title()
	{
		$lang = LangLoader::get('common', 'stats');
		return $lang['stats.module.title'];
	}

	public function is_displayed()
	{
		return StatsAuthorizationsService::check_authorizations()->read();
	}

	public function get_menu_content()
	{
		$lang = LangLoader::get('common', 'stats');
		$main_lang = LangLoader::get('main');

		$view = new FileTemplate('stats/stats_mini.tpl');
		$view->add_lang($lang);
		$view->add_lang($main_lang);
		MenuService::assign_positions_conditions($view, $this->get_block());

		$stats_cache = StatsCache::load();
		$l_member_registered = ($stats_cache->get_stats_properties('nbr_members') > 1) ? $lang['member.registered.s'] : $lang['member.registered'];

		$group_color = User::get_group_color($stats_cache->get_stats_properties('last_member_groups'), $stats_cache->get_stats_properties('last_member_level'));

		$view->put_all(array(
			'L_USER_REGISTERED' => sprintf($l_member_registered, $stats_cache->get_stats_properties('nbr_members')),
			'U_LINK_LAST_USER' => '<a href="' . UserUrlBuilder::profile($stats_cache->get_stats_properties('last_member_id'))->rel() . '" class="' . UserService::get_level_class($stats_cache->get_stats_properties('last_member_level')) . '"' . (!empty($group_color) ? ' style="color:' . $group_color . '"' : '') . '>' . $stats_cache->get_stats_properties('last_member_login') . '</a>'
		));

		return $view->render();
	}
}
?>
