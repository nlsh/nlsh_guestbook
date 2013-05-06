<?php

/**
 * Namespace
 */
namespace nlsh\guestbook;


/**
 * DCA- Klasse der Tabelle tl_module erweitern
 *
 * @package nlshGuestbook
 */


/**
 * Enthält Funktionen der Konfiguration einzelner Felder des DCA`s tl_module
 *
 * @copyright  Nils Heinold (c) 2013
 * @author     Nils Heinold
 * @package    nlshGuestbook
 * @link       http://github.com/nlsh/nlsh_guestbook
 * @license    LGPL
 */
class tl_moduleNlshGuestbook extends \Backend
{


    /**
     * Eintrag des 'Initial'- Templates, bei Neuanlage eines Gästebuchmodules.
     *
     * wird benötigt, da es die Werte in Form eines serialisierten Array zurück gibt!
     *
     * ein onload-callback des DCA's
     *
     * @param \DataContainer  DataContainer- Objekt
     */
    public function setInitialTemplate(\DataContainer $dc)
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
     * Gibt alle Gästebuch- Templates als Array zur Auswahl zurück.
     *
     * ein option-callback des Feldes com_nlsh_gb_template
     *
     * @param   \DataContainer  DataContainer- Objekt
     * @return  array           Array mit Templates, welche mit 'nlsh_guestbook_' beginnen
     */
    public function getCommentTemplates(\DataContainer $dc)
    {
        $intPid = $dc->activeRecord->pid;

        if ($this->Input->get('act') == 'overrideAll')
        {
            $intPid = $this->Input->get('id');
        }

        return $this->getTemplateGroup('nlsh_guestbook_', $intPid);
    }
}