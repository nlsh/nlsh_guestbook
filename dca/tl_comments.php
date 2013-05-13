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

$GLOBALS['TL_DCA']['tl_comments']['fields']['date']['load_callback'] = array(
        array ('\nlsh\guestbook\tl_commentNlshGuestbook', 'dateEnglishFormat')
);

$GLOBALS['TL_DCA']['tl_comments']['fields']['date']['save_callback'] = array(
        array ('\nlsh\guestbook\tl_commentNlshGuestbook', 'saveTime')
);

$GLOBALS['TL_DCA']['tl_comments']['fields']['date']['eval'] = array(
                    'rgxp' => 'date',
                    'datepicker' => $this->getDatePickerString(),
                    'tl_class' => 'w50 wizard'
);