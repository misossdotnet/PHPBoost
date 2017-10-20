<?php
/*##################################################
 *                             NewsSetup.class.php
 *                            -------------------
 *   begin                : May 29, 2010
 *   copyright            : (C) 2010 Kevin MASSY
 *   email                : kevin.massy@phpboost.com
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

/**
 * @author Kevin MASSY <kevin.massy@phpboost.com>
 */
class NewsSetup extends DefaultModuleSetup
{
	public static $news_table;
	public static $news_cats_table;

	/**
	 * @var string[string] localized messages
	 */
	private $messages;

	public static function __static()
	{
		self::$news_table = PREFIX . 'news';
		self::$news_cats_table = PREFIX . 'news_cats';
	}

	public function install()
	{
		$this->drop_tables();
		$this->create_tables();
		$this->insert_data();
	}

	public function uninstall()
	{
		$this->drop_tables();
		ConfigManager::delete('news', 'config');
		NewsService::get_keywords_manager()->delete_module_relations();
	}

	private function drop_tables()
	{
		PersistenceContext::get_dbms_utils()->drop(array(self::$news_table, self::$news_cats_table));
	}

	private function create_tables()
	{
		$this->create_news_table();
		$this->create_news_cats_table();
	}

	private function create_news_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'id_category' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'name' => array('type' => 'string', 'length' => 250, 'notnull' => 1, 'default' => "''"),
			'rewrited_name' => array('type' => 'string', 'length' => 250, 'default' => "''"),
			'contents' => array('type' => 'text', 'length' => 65000),
			'short_contents' => array('type' => 'text', 'length' => 65000),
			'creation_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'updated_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'approbation_type' => array('type' => 'integer', 'length' => 1, 'notnull' => 1, 'default' => 0),
			'start_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'end_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'number_view' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'top_list_enabled' => array('type' => 'boolean', 'notnull' => 1, 'default' => 0),
			'picture_url' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'author_custom_name' => array('type' =>  'string', 'length' => 255, 'default' => "''"),
			'author_user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'sources' => array('type' => 'text', 'length' => 65000),
		);
		$options = array(
			'primary' => array('id'),
			'indexes' => array(
				'id_category' => array('type' => 'key', 'fields' => 'id_category'),
				'title' => array('type' => 'fulltext', 'fields' => 'name'),
				'contents' => array('type' => 'fulltext', 'fields' => 'contents'),
				'short_contents' => array('type' => 'fulltext', 'fields' => 'short_contents')
		));
		PersistenceContext::get_dbms_utils()->create_table(self::$news_table, $fields, $options);
	}

	private function create_news_cats_table()
	{
		RichCategory::create_categories_table(self::$news_cats_table);
	}
		
	private function insert_data()
	{
        $this->messages = LangLoader::get('install', 'news');
		$this->insert_news_cats_data();
		$this->insert_news_data();
	}

	private function insert_news_cats_data()
	{
		PersistenceContext::get_querier()->insert(self::$news_cats_table, array(
			'id' => 1,
			'id_parent' => 0,
			'c_order' => 1,
			'auth' => '',
			'rewrited_name' => Url::encode_rewrite($this->messages['cat.name']),
			'name' => $this->messages['cat.name'],
			'description' => $this->messages['cat.description'],
			'image' => '/news/news.png'
		));
	}
	
	private function insert_news_data()
	{
		PersistenceContext::get_querier()->insert(self::$news_table, array(
			'id' => 1,
			'id_category' => 1,
			'name' => $this->messages['news.title'],
			'rewrited_name' => Url::encode_rewrite($this->messages['news.title']),
			'contents' => $this->messages['news.content'],
			'short_contents' => '',
			'creation_date' => time(),
			'updated_date' => 0,
			'approbation_type' => News::APPROVAL_NOW,
			'start_date' => 0,
			'end_date' => 0,
			'number_view' => 0,
			'top_list_enabled' => 0,
			'picture_url' => '',
			'author_custom_name' => '',
			'author_user_id' => 1,
			'sources' => TextHelper::serialize(array())
		));
	}
}
?>