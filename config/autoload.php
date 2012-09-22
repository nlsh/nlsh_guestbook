<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Nlsh_guestbook
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'nlsh',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Hooks
	'nlsh\nlsh_guestbook\HookNlshAddComment' => 'system/modules/nlsh_guestbook/hooks/HookNlshAddComment.php',

	// Modules
	'nlsh\nlsh_guestbook\ModuleNlshComments' => 'system/modules/nlsh_guestbook/modules/ModuleNlshComments.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'nlsh_gb_initial'        => 'system/modules/nlsh_guestbook/templates',
	'nlsh_guestbook_default' => 'system/modules/nlsh_guestbook/templates',
));
