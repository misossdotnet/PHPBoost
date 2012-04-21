<?php
/*##################################################
 *                           Plugin.class.php
 *                            -------------------
 *   begin                : February 21, 2012
 *   copyright            : (C) 2012 Kevin MASSY
 *   email                : soldier.weasel@gmail.com
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

abstract class Plugin
{
	private $id = 0;
	private $title = '';
	private $view;
	private $has_configuration = false;
	
	const READ_AUTHORIZATIONS = 1;
	
	public function __construct($title, View $view, $has_configuration = false)
	{
		$this->title = $title;
		$this->view = $view;
		$this->has_configuration = $has_configuration;
	}
	
	public function set_id($id)
	{
		$this->id = $id;
	}
	
	public function get_id()
	{
		return $this->id;
	}
	
	public function get_class()
	{
		return get_class($this);
	}
	
	public function get_title()
	{
		return $this->title;
	}
	
	public function get_view()
	{
		return $this->view;
	}
	
	public function get_preview()
	{
		return $this->view;
	}
	
	public function has_configuration()
	{
		return $this->has_configuration;
	}
	
	public function get_configuration()
	{
		return new PluginConfiguration($this->get_id());
	}
	
	public function get_fieldset_configuration(HTMLForm $form)
	{
		return new PluginFieldsetConfiguration($this->get_id(), $this->get_configuration(), $form);
	}
}
?>