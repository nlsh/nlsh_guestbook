<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Nils Heinold
 * @author     Nils Heinold
 * @package    nlsh_guestbook
 * @license    LGPL
 * @filesource
 */


/**
 * Class HookNlshAddComment
 *
 * @copyright  Nils Heinold
 * @author     Nils Heinold
 * @package    nlsh_guestbook
 */
class HookNlshAddComment extends Backend
{
    // Definition der Smilies in einem Array
    public $arrSmilies = array
    (
        array ('[smile]:D[/smile]',        '', 'big.gif'),
        array ('[smile]:)[/smile]',        '', 'smile.gif'),
        array ('[smile]:([/smile]',        '', 'sad.gif'),
        array ('[smile]:o[/smile]',        '', 'sur.gif'),
        array ('[smile]:shock[/smile]',    '', 'shock.gif'),
        array ('[smile]:?:[smile]',        '', 'question.gif'),
        array ('[smile]:?[/smile]',        '', 'confused.gif'),
        array ('[smile]8)[/smile]',        '', 'cool.gif'),
        array ('[smile]:lol:[/smile]',     '', 'lol.gif'),
        array ('[smile]:x[/smile]',        '', 'mad.gif'),
        array ('[smile]:raz:[/smile]',     '', 'raz.gif'),
        array ('[smile]:exc:[/smile]',     '', 'exc.gif'),
        array ('[smile]:oops:[/smile]',    '', 'red.gif'),
        array ('[smile]:cry:[/smile]',     '', 'cry.gif'),
        array ('[smile]:evil:[/smile]',    '', 'evil.gif'),
        array ('[smile]:twisted:[/smile]', '', 'twisted.gif'),
        array ('[smile]:roll:[/smile]',    '', 'roll.gif'),
        array ('[smile]:wink:[/smile]',    '', 'wink.gif'),
        array ('[smile]:idea:[/smile]',    '', 'idea.gif'),
        array ('[smile]:arrow:[/smile]',   '', 'arrow.gif'),
        array ('[smile]:respekt:[/smile]', '', 'respekt.gif'),
    );


    /**
     * Neueingetragenen Eintrag bearbeiten
     * @param int ID des neu eingetragenen Gästebucheintrages
     * @param array Array des Gästebucheintrages
     */

    public function nlshAddComment($intId, $arrComment)
    {
        $this->import('Input');

        /* Step by step
        $tl_article = $this->Database->prepare("SELECT * FROM tl_article WHERE `pid` = ? ")
                    ->execute($arrComment['parent']);

        $tl_content = $this->Database->prepare("SELECT * FROM tl_content WHERE `pid` = ? AND `type` = 'module'")
                    ->execute($tl_article->id);

        $tl_module = $this->Database->prepare("SELECT * FROM tl_module WHERE `id` = ?")
                    ->execute($tl_content->module);
        End Step by step */

        $typeModule = $this->Database->prepare("SELECT `type` FROM tl_module WHERE `id` = (SELECT `module` FROM tl_content WHERE `pid` = (SELECT `id` FROM tl_article WHERE `pid` = ? ) AND `type` = 'module')")
                    ->execute($arrComment['parent']);

        // nur wenn Eintrag vom Modul 'nlsh_guestbook'
        if ( $typeModule->type == 'nlsh_guestbook')
        {
            // Smilies außerhalb der Extension hinzufügen
            $arrSmilies = $this->arrSmilies;
            $arrSmilies[] = array (':-)', '', 'smile.gif');
            $arrSmilies[] = array (':-(', '', 'sad.gif');
            $arrSmilies[] = array (';-)', '', 'wink.gif');

            // Smilies ersetzen
            for ($b = 0; $b < count($arrSmilies); $b++)
            {
                $arrComment['comment'] = str_replace($arrSmilies[$b][0], '<img src="system/modules/nlsh_guestbook/html/smilies/' . $arrSmilies[$b][2] . '" title="' . $arrSmilies[$b][0] . '" alt="Smile" />', $arrComment['comment']);
            }

            // Überschrift zum Kommentar hinzufügen
            if ($this->Input->post('headline'))
            {
                $arrComment['comment'] = '[h]' . $this->_checkString($this->Input->post('headline')) . '[/h]' .  $arrComment['comment'];
            };

            // Datensatz in Datenbank updaten
            $this->Database->prepare("UPDATE `tl_comments` SET `comment` = ? WHERE `id` =?")
                        ->execute($arrComment['comment'], $intId);
        };

    }


    /**
     * Kontrolliert den String auf unerlaubte Eingaben
     * @param string zu kontrollierender String
     * @return string bereinigter String
     */
    protected function _checkString($strToCheck)
    {
        // Prevent cross-site request forgeries
        $strToCheck = preg_replace('/(href|src|on[a-z]+)="[^"]*(contao\/main\.php|typolight\/main\.php|javascript|vbscri?pt|script|alert|document|cookie|window)[^"]*"+/i', '$1="#"', $strToCheck);

        return $strToCheck;
    }
}