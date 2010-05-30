<?php
/*##################################################
 *                          InstallationServices.class.php
 *                            -------------------
 *   begin                : February 3, 2010
 *   copyright            : (C) 2010 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

class InstallationServices
{
	private static $token_file_content = '1';
	private static $min_php_version = '5.1.2';
	private static $phpboost_major_version = '3.1';

	/**
	 * @var File
	 */
	private $token;

	public function __construct()
	{
		$this->token = new File(PATH_TO_ROOT . '/.install_token');
	}

	public function create_phpboost_tables($dbms, $host, $port, $database, $login, $password, $tables_prefix, $create_db_if_needed = true)
	{
		$db_connection_data = $this->initialize_db_connection($dbms, $host, $port, $database, $login,
		$password, $tables_prefix, $create_db_if_needed);
		$this->create_tables();
		$this->write_connection_config_file($db_connection_data, $tables_prefix);
		$this->generate_installation_token();
	}

	public function configure_website($locale, $server_url, $server_path, $site_name, $site_desc = '', $site_keyword = '', $site_timezone = '')
	{
		$this->get_installation_token();

		$modules_to_install = $this->load_distribution_configuration($locale);
		$this->generate_website_configuration($locale, $server_url, $server_path, $site_name, $site_desc, $site_keyword, $site_timezone);
		$this->install_modules($modules_to_install);
		$this->add_menus();
		$this->generate_cache();

		// TODO remove it when the $CONFIG variable will be managed by the new config manager
		if (DISTRIBUTION_ENABLE_DEBUG_MODE)
		{
			Debug::enabled_debug_mode();
		}
		else
		{
			Debug::disable_debug_mode();
		}
		return true;
	}

	private function load_distribution_configuration($locale)
	{
		include PATH_TO_ROOT . '/install/distribution/' . $locale . '.php';
		return $DISTRIBUTION_MODULES;
	}

	private function generate_website_configuration($locale, $server_url, $server_path, $site_name, $site_desc = '', $site_keyword = '', $site_timezone = '')
	{
		$user = new User();
		$user->set_user_lang($locale);
		AppContext::set_user($user);
		$config = $this->build_configuration($locale, $server_url, $server_path, $site_name, $site_desc, $site_keyword, $site_timezone);
		$this->save_configuration($config, $locale);
		$this->configure_theme($config['theme'], $locale);
	}

	private function build_configuration($locale, $server_url, $server_path, $site_name, $site_desc = '', $site_keyword = '', $site_timezone = '')
	{
		$CONFIG = array();
		$CONFIG['server_name'] = $server_url;
		$CONFIG['server_path'] = '/' . ltrim($server_path, '/');
		$CONFIG['site_name'] = $site_name;
		$CONFIG['site_desc'] = $site_desc;
		$CONFIG['site_keyword'] = $site_keyword;
		$CONFIG['start'] = time();
		$CONFIG['version'] = self::$phpboost_major_version;
		$CONFIG['lang'] = $locale;
		$CONFIG['theme'] = DISTRIBUTION_THEME;
		$CONFIG['editor'] = 'bbcode';
		$CONFIG['timezone'] = !empty($site_timezone) ? $timezone : (int) date('I');
		$CONFIG['start_page'] = DISTRIBUTION_START_PAGE;
		$CONFIG['maintain'] = 0;
		$CONFIG['maintain_delay'] = 1;
		$CONFIG['maintain_display_admin'] = 1;
		$CONFIG['maintain_text'] = 'Maintain'; // TODO add localization $LANG['site_config_maintain_text'];
		$CONFIG['htaccess_manual_content'] = '';
		$CONFIG['rewrite'] = 0;
		$CONFIG['debug_mode'] = DISTRIBUTION_ENABLE_DEBUG_MODE;
		$CONFIG['com_popup'] = 0;
		$CONFIG['compteur'] = 0;
		$CONFIG['bench'] = DISTRIBUTION_ENABLE_BENCH;
		$CONFIG['theme_author'] = 0;
		$CONFIG['ob_gzhandler'] = 0;
		$CONFIG['site_cookie'] = 'session';
		$CONFIG['site_session'] = 3600;
		$CONFIG['site_session_invit'] = 300;
		$CONFIG['anti_flood'] = 0;
		$CONFIG['delay_flood'] = 7;
		$CONFIG['unlock_admin'] = '';
		$CONFIG['pm_max'] = 50;
		$CONFIG['search_cache_time'] = 30;
		$CONFIG['search_max_use'] = 100;
		$CONFIG['html_auth'] = array ('r2' => 1);
		$CONFIG['forbidden_tags'] = array ();

		return $CONFIG;
	}

	private function save_configuration($config, $locale)
	{
		$querier = PersistenceContext::get_querier();
		$querier->inject("UPDATE " . DB_TABLE_CONFIGS . " SET value=:config WHERE name='config'", array(
			'config' => serialize($config)));
		$querier->inject("INSERT INTO " . DB_TABLE_LANG . " (lang, activ, secure) VALUES (:config_lang, 1, -1)", array(
			'config_lang' => $config['lang']));
	}

	private function configure_theme($theme, $locale)
	{
		$info_theme = load_ini_file(PATH_TO_ROOT . '/templates/' . $theme . '/config/', $locale);
		PersistenceContext::get_querier()->inject(
			"INSERT INTO " . DB_TABLE_THEMES . " (theme, activ, secure, left_column, right_column)
			VALUES (:theme, 1, -1, :left_column, :right_column)", array(
				'theme' => $theme,
				'left_column' => $info_theme['left_column'],
				'right_column' => $info_theme['right_column']
		));
	}
	private function install_modules(array $modules_to_install)
	{
		ModulesConfig::load()->set_modules(array());
		foreach ($modules_to_install as $module_name)
		{
			ModulesManager::install_module($module_name, true);
		}
	}

	private function add_menus()
	{
		MenuService::enable_all(true);
		$modules_menu = MenuService::website_modules(LinksMenu::VERTICAL_MENU);
		MenuService::move($modules_menu, Menu::BLOCK_POSITION__LEFT, false);
		MenuService::change_position($modules_menu, -$modules_menu->get_block_position());
		MenuService::save($modules_menu);
	}

	private function generate_cache()
	{
		// TODO see if those lines are necessary
//		$Cache = new Cache();
//		$Cache->generate_all_files();
//		$Cache->load('themes', RELOAD_CACHE);
//		ModulesCssFilesCache::invalidate();
		AppContext::get_cache_service()->clear_phpboost_cache();
	}

	public function create_admin_account()
	{
		$this->get_installation_token();

		$this->delete_installation_token();
		return true;
	}

	private function initialize_db_connection($dbms, $host, $port, $database, $login, $password, $tables_prefix, $create_db_if_needed)
	{
		defined('PREFIX') or define('PREFIX', $tables_prefix);
		$db_connection_data = array(
			'dbms' => $dbms,
			'dsn' => 'mysql:host=' . $host . ';dbname=' . $database,
			'driver_options' => array(),
			'host' => $host,
			'login' => $login,
			'password' => $password,
			'database' => $database,
		);
		$this->connect_to_database($dbms, $db_connection_data, $database, $create_db_if_needed);
		return $db_connection_data;
	}

	private function connect_to_database($dbms, array $db_connection_data, $database, $create_db_if_needed)
	{
		DBFactory::init_factory($dbms);
		$connection = DBFactory::new_db_connection();
		try
		{
			$connection->connect($db_connection_data);
		}
		catch (UnexistingDatabaseException $exception)
		{
			if ($create_db_if_needed)
			{
				DBFactory::new_dbms_util(DBFactory::new_sql_querier($connection))->create_database($database);
				$connection->disconnect();
				$connection->connect($db_connection_data);
			}
			else
			{
				throw $exception;
			}
		}
		DBFactory::set_db_connection($connection);
	}

	private function create_tables()
	{
		$kernel = new KernelSetup();
		$kernel->install();
	}

	private function write_connection_config_file(array $db_connection_data, $tables_prefix)
	{
		$db_config_content = '<?php' . "\n" .
			'$db_connection_data = ' . var_export($db_connection_data, true) . ";\n\n" .
            'defined(\'PREFIX\') or define(\'PREFIX\' , \'' . $tables_prefix . '\');'. "\n" .
            'defined(\'PHPBOOST_INSTALLED\') or define(\'PHPBOOST_INSTALLED\', true);' . "\n" .
            'require_once PATH_TO_ROOT . \'/kernel/db/tables.php\';' . "\n" .
        '?>';

		$db_config_file = new File(PATH_TO_ROOT . '/kernel/db/config.php');
		$db_config_file->write($db_config_content);
		$db_config_file->close();
	}

	private function generate_installation_token()
	{
		$this->token->write(self::$token_file_content);
	}

	private function get_installation_token()
	{
		$is_token_valid = false;
		try
		{
			$is_token_valid = $this->token->exists() && $this->token->read() == self::$token_file_content;
		}
		catch (IOException $ioe)
		{
			$is_token_valid = false;
		}

		if (!$is_token_valid)
		{
			throw new TokenNotFoundException($this->token->get_path_from_root());
		}
	}

	private function delete_installation_token()
	{
		$this->token->delete();
	}
}
?>