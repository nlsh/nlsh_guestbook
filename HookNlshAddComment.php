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
 * Contao- Hook 'AddComment'

 * zum nachträglichen bearbeiten des eingegebenen Kommentares
 *
 * Natürlich nur, wenn Eintrag vom eigenem Modul.
 *
 * Hier werden die Smilies durch das HTML- img- Tags ersetzt,
 * die Überschriften zu dem Kommentar eingefügt
 * und eine Email versandt, falls gewünscht
 *
 * @copyright  Nils Heinold 2013
 * @author     Nils Heinold
 * @package    nlshGuestbook
 * @link       http://github.com/nlsh/nlsh_guestbook
 * @license    LGPL
 */
class HookNlshAddComment extends \Backend
{
    /**
     * Definition der Smilies in einem Array
     *
     * Definition der Smilies in einem Array
     */
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
     * DB- Eintrag des Gästebuch- Modules
     *
     * den DB- Eintrag des Gästebuchmodules aufnehmen
     */
     public $tlModule = FALSE;


    /**
     * Neueintag bearbeiten
     *
     * Neueingetragenen Eintrag bearbeiten,
     * speichern und Benachrichtigungsmail senden
     *
     * @param int    $intId       ID des neu eingetragenen Gästebucheintrages
     * @param array  $arrComment  Array mit neuem Gästebucheintrag     *
     * @return void
     */

    public function nlshAddComment($intId, $arrComment) {
        $this->import('Input');

         /* Step by step
         $tl_article = $this->Database
                    ->prepare("SELECT   *
                               FROM     tl_article
                               WHERE    `pid` = ? "
                    )
                    ->execute($arrComment['parent']);

         $tl_content = $this->Database
                    ->prepare("SELECT   *
                               FROM     tl_content
                               WHERE    `pid` = ?
                               AND      `type` = 'module'"
                    )
                    ->execute($tl_article->id);

         $tlModule = $this->Database
                    ->prepare("SELECT   *
                               FROM     tl_module
                               WHERE    `id` = ?"
                    )
                    ->execute($tl_content->module);
         End Step by step */

        $this->tlModule = $this->Database
                ->prepare("SELECT   *
                           FROM     tl_module
                           WHERE    `id` = (
                                            SELECT      `module`
                                            FROM         tl_content
                                            WHERE       `pid` = (
                                                                SELECT      `id`
                                                                FROM         tl_article
                                                                WHERE        `pid` = ?
                                            )
                           AND      `type` = 'module'
                           )"
                )
                ->execute($arrComment['parent']);

         // nur wenn Eintrag vom Modul 'nlsh_guestbook'
        if ($this->tlModule->type == 'nlsh_guestbook') {
             // Smilies außerhalb der Extension hinzufügen
            $source      = 'system/modules/nlsh_guestbook/html/smilies/';
            $arrSmilies   = $this->arrSmilies;
            $arrSmilies[] = array (':-)', '', 'smile.gif');
            $arrSmilies[] = array (':-(', '', 'sad.gif');
            $arrSmilies[] = array (';-)', '', 'wink.gif');

             // Smilies ersetzen
            for ($b = 0, $count = count($arrSmilies); $b < $count; $b++) {
                $imageTag = sprintf(
                        '<img src="%s%s" title="%s" alt="Smile" />',
                        $source,
                        $arrSmilies[$b][2],
                        $arrSmilies[$b][0]
                );

                $arrComment['comment'] = str_replace(
                        $arrSmilies[$b][0],
                        $imageTag,
                        $arrComment['comment']);
            }

             // Überschrift zum Kommentar hinzufügen
            if ($this->Input->post('headline')) {
                $headline              = $this->checkString($this->Input->post('headline'));
                $arrComment['comment'] = '[h]' . $headline . '[/h]' .  $arrComment['comment'];
            };

             // Datensatz in Datenbank updaten
            $this->Database
                    ->prepare(
                                "UPDATE     `tl_comments`
                                SET         `comment` = ?
                                WHERE       `id` =?"
                    )
                    ->execute($arrComment['comment'], $intId);

             // Benachrichtigungs- Mail erstellen und senden, wenn gewünscht
            if ($this->tl_module->com_nlsh_gb_bolMail == TRUE) {
                $this->import('Email');

                $email          = new \email;
                $email->subject = $GLOBALS['TL_LANG']['nlsh_guestbook']['email_subject'];
                $email->html    = str_replace('[h]', '<h1>', $arrComment['comment']);
                $email->html    = str_replace('[/h]', '</h1>', $email->html);

                $email->sendTo($this->tl_module->com_nlsh_gb_email);
            }
        };
    }


    /**
     * Kontrolliert den String auf unerlaubte Eingaben.
     * kopiert aus dem Core von Contao!
     *
     * @param  string  $toCheck  zu kontrollierender String
     * @return string  bereinigter String
     */
    protected function checkString($toCheck) {
         // Prevent cross-site request forgeries
        $toCheck = preg_replace(
                '/(href|src|on[a-z]+)="[^"]*(contao\/main\.php|typolight\/main\.php|javascript|vbscri?pt|script|alert|document|cookie|window)[^"]*"+/i', '$1="#"',
                $toCheck
        );

        return $toCheck;
    }
}