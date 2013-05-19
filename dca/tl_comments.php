<?php
/**
 * Erweiterung des tl_comments DCA`s
 *
 * @copyright  Nils Heinold 2013
 * @author     Nils Heinold
 * @package    nlshGuestbook
 * @link       http://github.com/nlsh/nlsh_guestbook
 * @license    LGPL
 */


/**
* Table tl_comments
*/

foreach ($GLOBALS['TL_DCA']['tl_comments']['palettes'] as $k => $v) {
    if ($k != '__selector__') {
        if (strstr($v, '{comment_legend},comment;')) {
            $GLOBALS['TL_DCA']['tl_comments']['palettes'][$k] = str_replace(
                    '{comment_legend},comment;',
                    '{comment_legend},date;comment;',
                    $v
            );
        }
    }
}

$GLOBALS['TL_DCA']['tl_comments']['fields']['date']['inputType'] =  'text';

$GLOBALS['TL_DCA']['tl_comments']['fields']['date']['save_callback'] = array(
    array ('tlCommentNlshGuestbook', 'saveTime')
);

$GLOBALS['TL_DCA']['tl_comments']['fields']['date']['eval']  =  array(
    'rgxp'       => 'date',
    'datepicker' => $this->getDatePickerString(),
    'tl_class'   => 'w50 wizard'
);


/**
* class tl_comment
*
* @copyright Nils Heinold (c) 2013
* @author    Nils Heinold
* @package   nlshGuestbook
* @link      https://github.com/nlsh/nlsh_guestbook
* @license   LGPL
*/
class tlCommentNlshGuestbook extends \Backend
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
    * Uhrzeit des Eintrages eintragen
    *
    * Durch das getDatePicker- Widget wird nur das Datum weitergegeben
    *
    * die Uhrzeit wird gelöscht, darum hier die Uhrzeit hinzufügen
    *
    * save_callback des Feldes date
    *
    * @param   int             $field gewähltes Datum
    * @param   \DataContainer  $dc DataContainer- Objekt von Contao
    * @return  string          gewähltes Datum mit Uhrzeit
    */
    public function saveTime($field, DataContainer $dc) {
        $oldTstamp = $this->Database
            ->prepare("SELECT `date` FROM `tl_comments` WHERE `id` =?")
            ->execute($dc->id);

        $timeCompled = $field       + (date('G', $oldTstamp->date) * 60 * 60);
        $timeCompled = $timeCompled + (date('i', $oldTstamp->date) * 60);
        $timeCompled = $timeCompled + (date('s', $oldTstamp->date));

        return $timeCompled;
    }
}