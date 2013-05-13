<?php

/**
 * Namespace
 */
namespace nlsh\guestbook;


/**
 * DCA- Klasse der Tabelle tl_comment erweitern
 *
 * @package nlshGuestbook
 */


/**
* Enthält Funktionen der Konfiguration einzelner Felder des DCA`s tl_comment
 *
 * @copyright Nils Heinold (c) 2013
 * @author    Nils Heinold
 * @package   nlshGuestbook
 * @link      https://github.com/nlsh/nlsh_guestbook
 * @license   LGPL
*/
class tl_commentNlshGuestbook extends \Backend
{


    /**
     * Den Backenduser importieren
     *
     * Contao- Core Funktion
     */
    public function __construct() {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }


    /**
     * Datum ins engl. Format konvertieren
     *
     * Das getDatePicker- Widget kann nichts mit dem dt.Datumsformat anfangen
     *
     * gibt zwar ein paar Tickets, aber sicher ist sicher
     *
     * load_callback des Feldes date
     *
     * @param   int             $field  gespeichertes Datum
     * @param   \DataContainer  $dc     DataContainer- Objekt von Contao
     * @return  string          Datum im engl. Format Y-m-d
     */
    public function dateEnglishFormat($field, \DataContainer $dc) {
         //       $GLOBALS['TL_CONFIG']['dateFormat'] = 'Y-m-d';

        return $field;
    }


    /**
     * Uhrzeit des Eintrages eintragen
     *
     * Durch das getDatePicker- Widget wird nur das Datum weitergegeben
     *
     * die Uhrzeit wird gelöscht, darum hier die Uhrzeit hinzufügen
     *
     * save_callback des Feldes date
     *
     * @param   int              $field  gewähltes Datum
     * @param   \DataContainer   $dc     DataContainer- Objekt von Contao
     * @return  string           gewähltes Datum mit Uhrzeit
     */
    public function saveTime($field, \DataContainer $dc) {
        $oldTstamp = $this->Database->prepare("SELECT `date` FROM `tl_comments` WHERE `id` =?")
                                ->execute($dc->id);

        $timeCompled = $field       + (date('G', $oldTstamp->date) * 60 * 60);
        $timeCompled = $timeCompled + (date('i', $oldTstamp->date) * 60);
        $timeCompled = $timeCompled + (date('s', $oldTstamp->date));

        return $timeCompled;
    }
}