<?php

/**
* Contao Open Source CMS
*
* Copyright (C) 2005-2012 Leo Feyer
*
* @package nlsh_guestbook
* @author Nils Heinold
* @link http://github.com/nlsh/nlsh_guestbook
* @license LGPL
* @copyright Nils Heinold 2012
*/


 /**
 * Table tl_module
 */


 /**
  * Add onload- callback to tl_module
   */

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('tl_NlshModule','setInitialTemplate');


 /**
 * Add palettes to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['nlsh_guestbook'] =     '{title_legend},
                                                                        name,headline,type;
                                                                    {comment_legend},
                                                                        com_order,perPage,com_moderate,com_bbcode,com_protected,com_requireLogin,com_disableCaptcha;
                                                                    {template_legend:hide},
                                                                        com_nlsh_gb_template;
                                                                    {protected_legend:hide},
                                                                        protected;
                                                                    {expert_legend:hide},
                                                                        guests,cssID,space';


 /**
 * Add Fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['com_nlsh_gb_template'] = array
(
                    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['com_nlsh_gb_template'],
                    'default'                 => 'com_default',
                    'exclude'                 => true,
                    'inputType'               => 'select',
                    'options_callback'        => array('tl_NlshModule', 'getCommentTemplates'),
                    'eval'                    => array('tl_class'=>'w50'),
                    'sql'                     => "varchar(32) NOT NULL default ''"
);


/**
* class tl_NlshModule
*
* Enthält Funktionen einzelner Felder der Konfiguration des tl_module DCA`s
* @package nlsh_guestbook
*/
class tl_NlshModule extends Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }


    /**
     * Belegt beim Anlegen eines Gästebuchmodules die Tabelle 'com_template' mit 'nlsh_guestbook_initial' vor
     *
     * onload-callback des DCA's
     */
    public function setInitialTemplate(Datacontainer $dc)
    {
        $objModule = $this->Database->prepare("SELECT * FROM `tl_module` WHERE `id` = ?")
                ->execute($dc->id);

        // Wenn Gästebuchmodul
        if($objModule->type == 'nlsh_guestbook')
        {
            // Template eintragen
            $this->Database->prepare("UPDATE `tl_module` SET `com_template` = 'nlsh_gb_initial' WHERE `tl_module`.`id` = ?")
                        ->execute($dc->id);
        }

    }


     /**
     * Gibt alle Gästebuchtemplates als Array zurück
     * option-callback des Feldes com_nlsh_gb_template
     * @param DataContainer
     * @return array
     */
    public function getCommentTemplates(DataContainer $dc)
    {
        $intPid = $dc->activeRecord->pid;

        if ($this->Input->get('act') == 'overrideAll')
        {
            $intPid = $this->Input->get('id');
        }

        return $this->getTemplateGroup('nlsh_guestbook_', $intPid);
    }
}