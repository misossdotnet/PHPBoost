<?php
/**
 * @package     PHPBoost
 * @subpackage  Environment
 * @category    Framework
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Loic ROUCHON <horn@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2014 12 22
 * @since       PHPBoost 3.0 - 2010 06 01
*/

class PHPBoostNotInstalledException extends Exception
{
	public function __construct()
	{
		parent::__construct('PHPBoost is not installed');
	}
}
?>
