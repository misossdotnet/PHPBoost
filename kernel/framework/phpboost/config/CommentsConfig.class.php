<?php
/**
 * @package     PHPBoost
 * @subpackage  Config
 * @category    Framework
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Benoit SAUTEL <ben.popeye@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2018 01 22
 * @since       PHPBoost 3.0 - 2010 07 08
 * @contributor Kevin MASSY <reidlos@phpboost.com>
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class CommentsConfig extends AbstractConfigData
{
	const COMMENTS_ENABLED             = 'comments';
	const COMMENTS_UNAUTHORIZED_MODULE = 'comments_unauthorized_modules';
	const AUTHORIZATIONS               = 'authorizations';
	const NUMBER_COMMENTS_DISPLAY      = 'number_comments_per_page';
	const FORBIDDEN_TAGS               = 'forbidden_tags';
	const MAX_LINKS_COMMENT            = 'max_links_comment';
	const ORDER_DISPLAY_COMMENTS       = 'order_display_comments';

	const ASC_ORDER                    = 'ASC';
	const DESC_ORDER                   = 'DESC';

	public function are_comments_enabled()
	{
		return $this->get_property(self::COMMENTS_ENABLED);
	}

	public function set_comments_enabled($enabled)
	{
		$this->set_property(self::COMMENTS_ENABLED, $enabled);
	}

	public function get_comments_unauthorized_modules()
	{
		return $this->get_property(self::COMMENTS_UNAUTHORIZED_MODULE);
	}

	public function set_comments_unauthorized_modules(array $modules)
	{
		$this->set_property(self::COMMENTS_UNAUTHORIZED_MODULE, $modules);
	}

	public function module_comments_is_enabled($module_id)
	{
		return $this->are_comments_enabled() && !in_array($module_id, $this->get_comments_unauthorized_modules());
	}

	public function get_authorizations()
	{
		return $this->get_property(self::AUTHORIZATIONS);
	}

	public function set_authorizations(Array $array)
	{
		$this->set_property(self::AUTHORIZATIONS, $array);
	}

	public function get_number_comments_display()
	{
		return $this->get_property(self::NUMBER_COMMENTS_DISPLAY);
	}

	public function set_number_comments_display($number)
	{
		$this->set_property(self::NUMBER_COMMENTS_DISPLAY, $number);
	}

	public function get_forbidden_tags()
	{
		return $this->get_property(self::FORBIDDEN_TAGS);
	}

	public function set_forbidden_tags(array $forbidden_tags)
	{
		$this->set_property(self::FORBIDDEN_TAGS, $forbidden_tags);
	}

	public function get_max_links_comment()
	{
		return $this->get_property(self::MAX_LINKS_COMMENT);
	}

	public function set_max_links_comment($number)
	{
		$this->set_property(self::MAX_LINKS_COMMENT, $number);
	}

	public function get_order_display_comments()
	{
		$order_display_comments = $this->get_property(self::ORDER_DISPLAY_COMMENTS);
		switch ($order_display_comments) {
			case self::ASC_ORDER:
			case self::DESC_ORDER:
				$valid_order = $order_display_comments;
			break;
			default:
				$valid_order = self::ASC_ORDER;
			break;
		}
		return $valid_order;
	}

	public function set_order_display_comments($order)
	{
		$this->set_property(self::ORDER_DISPLAY_COMMENTS, $order);
	}

	public function get_approbation_comments()
	{
		return $this->get_property(self::APPROBATION_COMMENTS);
	}

	public function set_approbation_comments($approbation)
	{
		$this->set_property(self::APPROBATION_COMMENTS, $approbation);
	}

	public function get_default_values()
	{
		return array(
			self::COMMENTS_ENABLED             => true,
			self::COMMENTS_UNAUTHORIZED_MODULE => array(),
			self::AUTHORIZATIONS               => array('r1' => 7, 'r0' => 3, 'r-1' => 3),
			self::NUMBER_COMMENTS_DISPLAY      => 15,
			self::FORBIDDEN_TAGS               => array(),
			self::MAX_LINKS_COMMENT            => 2,
			self::ORDER_DISPLAY_COMMENTS       => self::DESC_ORDER
		);
	}

	/**
	 * Returns the configuration.
	 * @return CommentsConfig
	 */
	public static function load()
	{
		return ConfigManager::load(__CLASS__, 'kernel', 'comments-config');
	}

	/**
	 * Saves the configuration in the database. Has it become persistent.
	 */
	public static function save()
	{
		ConfigManager::save('kernel', self::load(), 'comments-config');
	}
}
?>
