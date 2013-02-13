<?php

/**
* Contao Open Source CMS
*
* Copyright (C) 2005-2012 Leo Feyer
*
* @package nlshGuestbook
* @author Nils Heinold
* @link http://github.com/nlsh/nlsh_guestbook
* @license LGPL
* @copyright Nils Heinold 2012
*/


/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 *
 * Back end modules are stored in a global array called "BE_MOD". Each module
 * has certain properties like an icon, an optional callback function and one
 * or more tables. Each module belongs to a particular group.
 *
 *   $GLOBALS['BE_MOD'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'module_1' => array
 *           (
 *               'tables'       => array('table_1', 'table_2'),
 *               'key'          => array('Class', 'method'),
 *               'callback'     => 'ClassName',
 *               'icon'         => 'path/to/icon.gif',
 *               'stylesheet'   => 'path/to/stylesheet.css',
 *               'javascript'   => 'path/to/javascript.js'
 *           )
 *       )
 *   );
 *
 * Use function array_insert() to modify an existing modules array.
 */


/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 *
 * List all front end modules and their class names.
 *
 *   $GLOBALS['FE_MOD'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'module_1' => 'Contentlass',
 *           'module_2' => 'Contentlass'
 *       )
 *   );
 *
 * Use function array_insert() to modify an existing CTE array.
 */
array_insert ($GLOBALS['FE_MOD']['application'],3, array
(
    'nlsh_guestbook' => 'nlsh\guestbook\ModuleNlshComments',
));


/**
 * -------------------------------------------------------------------------
 * CONTENT ELEMENTS
 * -------------------------------------------------------------------------
 *
 * List all content elements and their class names.
 *
 *   $GLOBALS['TL_CTE'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'cte_1' => 'Contentlass',
 *           'cte_2' => 'Contentlass'
 *       )
 *   );
 *
 * Use function array_insert() to modify an existing CTE array.
 */


/**
 * -------------------------------------------------------------------------
 * BACK END FORM FIELDS
 * -------------------------------------------------------------------------
 *
 * List all back end form fields and their class names.
 *
 *   $GLOBALS['BE_FFL'] = array
 *   (
 *       'input'  => 'Class',
 *       'select' => 'Class'
 *   );
 *
 * Use function array_insert() to modify an existing FFL array.
 */


/**
 * -------------------------------------------------------------------------
 * FRONT END FORM FIELDS
 * -------------------------------------------------------------------------
 *
 * List all form fields and their class names.
 *
 *   $GLOBALS['TL_FFL'] = array
 *   (
 *       'input'  => Class,
 *       'select' => Class
 *   );
 *
 * Use function array_insert() to modify an existing FFL array.
 */


/**
 * -------------------------------------------------------------------------
 * CACHE TABLES
 * -------------------------------------------------------------------------
 *
 * These tables are used to cache data and can be truncated using back end
 * module "clear cache".
 *
 *   $GLOBALS['TL_CACHE'] = array
 *   (
 *       'table_1',
 *       'table_2'
 *   );
 *
 * Use function array_insert() to modify an existing cache array.
 */


/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 *
 * Hooking allows you to register one or more callback functions that are
 * called on a particular event in a specific order. Thus, third party
 * extensions can add functionality to the core system without having to
 * modify the source code.
 *
 *   $GLOBALS['TL_HOOKS'] = array
 *   (
 *       'hook_1' => array
 *       (
 *           array('Class', 'Method'),
 *           array('Class', 'Method')
 *       )
 *   );
 *
 * Use function array_insert() to modify an existing hooks array.
 */

$GLOBALS['TL_HOOKS']['addComment'][] = array('nlsh\guestbook\HookNlshAddComment', 'nlshAddComment');

/**
 * -------------------------------------------------------------------------
 * PAGE TYPES
 * -------------------------------------------------------------------------
 *
 * Page types and their corresponding front end controller class.
 *
 *   $GLOBALS['TL_PTY'] = array
 *   (
 *       'type_1' => 'PageType1',
 *       'type_2' => 'PageType2'
 *   );
 *
 * Use function array_insert() to modify an existing page types array.
 */
