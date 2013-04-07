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
* class tl_comment
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
    public function __construct()
    {
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
    * @param   int gespeichertes Datum
    * @param   \DataContainer DataContainer- Objekt von Contao
    * @return  string Datum im engl. Format Y-m-d
    */
    public function dateEnglishFormat($Field, \DataContainer $dc)
    {

 //       $GLOBALS['TL_CONFIG']['dateFormat'] = 'Y-m-d';

        return $Field;
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
    * @param   int gewähltes Datum
    * @param   \DataContainer DataContainer- Objekt von Contao
    * @return  string gewähltes Datum mit Uhrzeit
    */
    public function saveTime($Field, \DataContainer $dc)
    {
        $oldTstamp = $this->Database->prepare("SELECT `date` FROM `tl_comments` WHERE `id` =?")
                                ->execute($dc->id);

        $timeCompled = $Field + (date('G', $oldTstamp->date) * 60 * 60) + (date('i', $oldTstamp->date) * 60) + (date('s', $oldTstamp->date));

        return $timeCompled;
    }
}