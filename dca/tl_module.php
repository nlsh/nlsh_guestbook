<?php
/**
 * Erweiterung des tl_Module DCA`s
 *
 * @copyright  Nils Heinold 2013
 * @author     Nils Heinold
 * @package    nlshGuestbook
 * @link       http://github.com/nlsh/nlsh_guestbook
 * @license    LGPL
 */


 /**
  * Add onload_callback to tl_module
   */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array(
        '\nlsh\guestbook\tl_moduleNlshGuestbook','setInitialTemplate'
);


 /**
 * Add __selector__ to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'com_nlsh_gb_bolMail';


 /**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['nlsh_guestbook'] =    '{title_legend},
                                                                        name,
                                                                        headline,
                                                                        type;
                                                                    {guestbook_legend},
                                                                        com_order,
                                                                        perPage,
                                                                        com_moderate,
                                                                        com_bbcode,
                                                                        com_protected,
                                                                        com_requireLogin,
                                                                        com_disableCaptcha,
                                                                        com_nlsh_gb_bolMail;
                                                                    {template_legend:hide},
                                                                        com_nlsh_gb_template;
                                                                    {protected_legend:hide},
                                                                        protected;
                                                                    {expert_legend:hide},
                                                                        guests,
                                                                        cssID,
                                                                        space';

 /**
 * Add subpalettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['com_nlsh_gb_bolMail'] = 'com_nlsh_gb_email';


 /**
 * Add Fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['com_nlsh_gb_bolMail'] = array(
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['com_nlsh_gb_bolMail'],
        'exclude'                 => true,
        'inputType'               => 'checkbox',
        'eval'                    => array('tl_class'=>'w50','submitOnChange'=>true),
        'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['com_nlsh_gb_email'] = array(
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['com_nlsh_gb_email'],
        'exclude'                 => true,
        'search'                  => true,
        'inputType'               => 'text',
        'eval'                    => array(
                'mandatory'=>true,
                'maxlength'=>255,
                'rgxp'=>'email',
                'decodeEntities'=>true,
                'tl_class'=>'w50'
        ),
        'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['com_nlsh_gb_template'] = array
(
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['com_nlsh_gb_template'],
        'default'                 => 'com_default',
        'exclude'                 => true,
        'inputType'               => 'select',
        'options_callback'        => array('\nlsh\guestbook\tl_moduleNlshGuestbook', 'getCommentTemplates'),
        'eval'                    => array('tl_class'=>'w50'),
        'sql'                     => "varchar(32) NOT NULL default ''"
);