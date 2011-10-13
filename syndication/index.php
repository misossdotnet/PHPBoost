<?php
/*##################################################
 *                           index.php
 *                            -------------------
 *   begin                : July 16, 2011
 *   copyright            : (C) 2011 K�vin MASSY
 *   email                : soldier.weasel@gmail.com
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

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/begin.php';

$url_controller_mappers = array(
	new UrlControllerMapper('DisplayRssSyndicationController', '`^/rss(?:/([a-z0-9]+))?/?([0-9]+)?/?([a-z0-9]+)?/?$`', array('module_id', 'module_category_id', 'feed_name')),
	new UrlControllerMapper('DisplayAtomSyndicationController', '`^/atom(?:/([a-z0-9]+))?/?([0-9]+)?/?([a-z0-9]+)?/?$`', array('module_id', 'module_category_id', 'feed_name')),
);

header("Content-Type: application/xml; charset=iso-8859-1");
DispatchManager::dispatch($url_controller_mappers);
?>