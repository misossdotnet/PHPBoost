<?php
/*##################################################
 *                        AbstractHTMLTableModel.class.php
 *                            -------------------
 *   begin                : February 25, 2010
 *   copyright            : (C) 2010 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
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
 * @author loic rouchon <loic.rouchon@phpboost.com>
 * @desc This class allows you to manage easily html tables.
 * @package {@package}
 */
class HTMLTableModel
{
	const NO_PAGINATION = 0;
	const DEFAULT_PAGINATION = 25;

	/**
	 * @var HTMLTable
	 */
	public $html_table;

	private $id;
	private $caption = '';
	private $rows_per_page;
	private $nb_rows_options = array(10, 25, 100);
	private $default_sorting_rule;
	private $allowed_sort_parameters = array();
	private $filters = array();
	private $permanent_filters = array();
	private $display_footer = true;
	private $footer_css_class = '';

	/**
	 * @var HTMLTableColumn[]
	 */
	private $columns;

	public function __construct($id, array $columns, HTMLTableSortingRule $default_sorting_rule, $rows_per_page = self::DEFAULT_PAGINATION)
	{
		foreach ($columns as $column)
		{
			if ($column instanceof HTMLTableColumn)
				$this->add_column($column);
		}

		$default_sorting_rule->set_is_default_sorting(true);
		$this->default_sorting_rule = $default_sorting_rule;
		$this->rows_per_page = $rows_per_page;
		$this->set_nb_rows_options($this->nb_rows_options);
		$this->id = $id;
	}

	public function set_html_table(HTMLTable $html_table)
	{
		$this->html_table = $html_table;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_id()
	{
		return !empty($this->id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_caption()
	{
		return !empty($this->caption);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_caption()
	{
		return $this->caption;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_footer_displayed()
	{
		return $this->display_footer;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_pagination_activated()
	{
		return $this->rows_per_page > self::NO_PAGINATION;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_nb_rows_per_page()
	{
		return $this->rows_per_page;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_nb_rows_options()
	{
		return !empty($this->nb_rows_options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_nb_rows_options()
	{
		return $this->nb_rows_options;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_columns()
	{
		return $this->columns;
	}

	/**
	 * {@inheritdoc}
	 */
	public function default_sort_rule()
	{
		return $this->default_sorting_rule;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_sort_parameter_allowed($parameter)
	{
		return in_array($parameter, $this->allowed_sort_parameters);
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_filter_allowed($id, $value)
	{
		if (array_key_exists($id, $this->filters))
		{
			return $this->filters[$id]->is_value_allowed($value);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_filter($id)
	{
		return $this->filters[$id];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_filters()
	{
		return $this->filters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_permanent_filter($id)
	{
		return $this->permanent_filters[$id];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_permanent_filters()
	{
		return $this->permanent_filters;
	}

	public function set_id($id)
	{
		$this->id = $id;
	}

	public function set_caption($caption)
	{
		$this->caption = $caption;
	}

	public function hide_footer()
	{
		$this->display_footer = false;
	}
	
	public function has_footer_css_class()
	{
		return !empty($this->footer_css_class);
	}
	
	public function get_footer_css_class()
	{
		return $this->footer_css_class;
	}
	
	public function set_footer_css_class($class)
	{
		$this->footer_css_class = $class;
	}

	public function add_footer_css_class($class)
	{
		$this->footer_css_class .= ' ' . $class;
	}

	public function set_nb_rows_options(array $nb_rows_options)
	{
		if ($this->is_pagination_activated())
		{
			if (!empty($nb_rows_options))
			{
				$nb_rows_options[] = $this->rows_per_page;
				$nb_rows_options = array_unique($nb_rows_options);
				sort($nb_rows_options);
			}
			$this->nb_rows_options = $nb_rows_options;
		}
	}

	public function add_filter(HTMLTableFilter $filter)
	{
		$this->filters[$filter->get_id()] = $filter;
	}

	public function add_permanent_filter($filter)
	{
		$this->permanent_filters[] = $filter;
	}

	private function add_column(HTMLTableColumn $column)
	{
		$this->columns[] = $column;
		if ($column->is_sortable())
		{
			$this->allowed_sort_parameters[] = $column->get_sortable_parameter();
		}
	}

	public function delete_last_column()
	{
		array_pop($this->columns);
	}
}
?>