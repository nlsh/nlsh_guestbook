<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package nlshGuestbook
 * @link    https://contao.org
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
	// Classes
	'nlsh\guestbook\tl_commentNlshGuestbook' => 'system/modules/nlsh_guestbook/classes/tl_commentNlshGuestbook.php',

	// Hooks
	'nlsh\guestbook\HookNlshAddComment'      => 'system/modules/nlsh_guestbook/hooks/HookNlshAddComment.php',

	// Modules
	'nlsh\guestbook\ModuleNlshComments'      => 'system/modules/nlsh_guestbook/modules/ModuleNlshComments.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'nlsh_cancelButton'      => 'system/modules/nlsh_guestbook/templates',
	'nlsh_gb_initial'        => 'system/modules/nlsh_guestbook/templates',
	'nlsh_guestbook_default' => 'system/modules/nlsh_guestbook/templates',
	'nlsh_mod_comment_form'  => 'system/modules/nlsh_guestbook/templates',
));
