<?php
/**
 * @package     PHPBoost
 * @subpackage  Menu\extension-point
 * @category    Framework
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2014 12 22
 * @since       PHPBoost 3.0 - 2011 10 08
*/

interface MenusExtensionPoint extends ExtensionPoint
{
	const EXTENSION_POINT = 'menus';

	/**
	 * @return array instance Menu class
	 */
	function get_menus();
}
?>
