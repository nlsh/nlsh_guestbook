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
 * Kommentare vom Core- Modul Comments übernehmen
 *
 * @copyright  Nils Heinold 2013
 * @author     Nils Heinold
 * @package    nlshGuestbook
 * @link       http://github.com/nlsh/nlsh_guestbook
 * @license    LGPL
 */
class ModuleNlshComments extends \Module
{


    /**
    * Template
    * @var string
    */
    protected $strTemplate = 'nlsh_guestbook_default';


    /**
    * Neueintrag für Gästebuch.
    * Handelt es sich um einen Neueintrag ins Gästebuch?
    *
    * @var boolean
    */
    protected $inputNewEntrie = FALSE;


    /**
     * Definition des GET- Namens für einen Neueintrag
     */
    const GET_INPUT_GBENTRIE = 'nlsh_gb_input';


    /**
     * Definition der Smilies in einem Array
     *
     * @var array
     */
    protected $arrSmilies = array(
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
    * Im Backend eine wildcard anzeigen
    *
    * @return string HTML Ausgabe, getrennt für das Backend/ Frontend
    */
    public function generate() {
         // Wenn im Backend
        if (TL_MODE == 'BE') {
             // Neues Template
            $objTemplate = new \BackendTemplate('be_wildcard');

             // Befüllen
            $objTemplate->wildcard = '### nlsh_guestbook ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?' .
                                     'do=themes&amp;' .
                                     'table=tl_module&amp;' .
                                     'act=edit&amp;' .
                                     'id=' . $this->id;

             // Kurzanzeige im Backend
            return $objTemplate->parse();
        }

             // ansonsten Template wechseln
            $this->strTemplate = $this->com_nlsh_gb_template;
            $this->Template    = new \FrontendTemplate($this->strTemplate);

         // und Frontend Rendern (jetzt kommt die compile()- Methode
        return parent::generate();
    }


    /**
    * Generate the module
    *
    * Variablen für die Ausgabe des Templates bereitstellen
    *
    * @return void
    */
    protected function compile() {
        $objPage = $GLOBALS['objPage'];

        $this->import('Comments');
        $objConfig = new \stdClass();

        $objConfig->perPage        = $this->perPage;
        $objConfig->order          = $this->com_order;
        $objConfig->template       = $this->com_template;
        $objConfig->requireLogin   = $this->com_requireLogin;
        $objConfig->disableCaptcha = $this->com_disableCaptcha;
        $objConfig->bbcode         = $this->com_bbcode;
        $objConfig->moderate       = $this->com_moderate;

         // Kommentare vom Core anfordern
        $this->Comments->addCommentsToTemplate(
                $this->Template,
                $objConfig,
                'tl_page',
                $objPage->id,
                $GLOBALS['TL_ADMIN_EMAIL']
        );

         // Sprache nachladen
        $this->loadLanguageFile('tl_style');

         // BBCode definieren
        $this->arrBbcFormat = array(
            array ('[b]', '[/b]',
                   'text_bold.png',
                   $GLOBALS['TL_LANG']['tl_style']['bold']
            ),
            array ('[i]', '[/i]',
                   'text_italic.png',
                   $GLOBALS['TL_LANG']['tl_style']['italic']
            ),
            array ('[u]', '[/u]',
                   'text_underline.png',
                    $GLOBALS['TL_LANG']['tl_style']['underline']
            ),
            array ('[img]', '[/img]',
                   'picture_link.png',
                   $GLOBALS['TL_LANG']['nlsh_guestbook']['insertPicture']
            ),
            array ('[quote]', '[/quote]',
                   'comment_add.png',
                   $GLOBALS['TL_LANG']['nlsh_guestbook']['insertQoute']
            ),
        );

         // Einträge einlesen
        $arrGbEntries = $this->Database
                ->prepare(
                    "SELECT        `id`
                     FROM           tl_comments
                     WHERE          source=?
                     AND            parent=?" . (!BE_USER_LOGGED_IN ? ' AND published=1' : '') . '
                     ORDER BY       date ASC'
                )
                ->execute('tl_page', $objPage->id)
                ->fetchAllAssoc();

         // Anzahl der Einträge eintragen
        $this->Template->howManyEntriesText = sprintf(
                $GLOBALS['TL_LANG']['nlsh_guestbook']['howManyEntries'],
                count($arrGbEntries)
        );

         // die vom nlsh_gb_initial- Template übergebenen Comments- Daten
         // wieder in ein Array umwandeln und die Nummer hinzufügen
        foreach ($this->Template->comments as $comment) {
            $tempComment = unserialize(trim($comment));

             // Durchnummerieren
            for ($a = 0, $count = count($arrGbEntries); $a < $count; $a++) {
                if ($arrGbEntries[$a]['id'] == $tempComment['id']) {
                    $tempComment['nr'] = $a + 1;
                }
            }

             // Überschriften herausholen
            if (strpos($tempComment['comment'], '[/h]')) {
                $start = strpos($tempComment['comment'], '[h]');
                $end   = strpos($tempComment['comment'], '[/h]') + 4;

                $tempComment['headlineComment'] = substr(
                        $tempComment['comment'],
                        $start + 3,
                        $end - $start - 7
                );

                $tempComment['comment'] = substr(
                        $tempComment['comment'],
                        0,
                        $start
                ) .
                substr(
                        $tempComment['comment'],
                        $end
                );
            }

            $tempArrComments[] = $tempComment;
        }

         // und in das Template übernehmen
        $this->Template->comments = $tempArrComments;

         // Anzahl der übermittelten Kommentare ins Template
        $this->Template->countComments = count($this->Template->comments);

         // Wenn Link für ein neuen Gästebucheintrag benutzt wurde
         // Wird im Template abgefragt und benötigt!
        if ($this->Input->get(self::GET_INPUT_GBENTRIE) === 'TRUE') {
            $this->Template->inputNewEntrie = TRUE;
        }

         // Html- Link für neuen Eintrag erzeugen
        $this->Template->htmlLinkNewEntrie = $this->getHtmlLinkForNewNlshGbEntrie();

         // Formular erweitern mit Auswahl Smilies und BBCode und Überschriftenfeld
        if ($this->com_bbcode == TRUE) {
            $bbCode  = $this->getHtmlDivSelectBbcOrSmilies(
                    'bbcode',
                    'com_tl_page_' . $objPage->id,
                    $this->arrBbcFormat
            );

            $smilies = $this->getHtmlDivSelectBbcOrSmilies(
                    'smilies',
                    'com_tl_page_' . $objPage->id,
                    $this->arrSmilies
            );
        }

         // Formular für Dateneingabe vorbereiten
        $this->form                = new \FrontendTemplate('nlsh_mod_comment_form');
        $this->form->requireLogin  = $this->Template->requireLogin;
        $this->form->confirm       = $this->Template->confirm;
        $this->form->allowComments = $this->Template->allowComments;
        $this->form->formId        = $this->Template->formId;
        $this->form->bbCode        = $bbCode;
        $this->form->smilies       = $smilies;
        $this->form->submit        = $this->Template->submit;
        $this->form->fields        = $this->Template->fields;
        $this->form->cancelValue   = $GLOBALS['TL_LANG']['nlsh_guestbook']['cancelButton'];
        $this->form->action        = $this->delGetEntryFromRequest(
                $this->Template->action,
                self::GET_INPUT_GBENTRIE
        );
        $this->Template->form      = $this->form->parse();

    }


    /**
     * HTML- String für <div> zur Auswahl der BB- Codes und Smilies erzeugen
     *
     * @param  string  $cssClass     CSS- Klasse für Div- Container
     * @param  string  $cssIdForm    Name des CSS-ID`s des Formulares,
     *                               in dem sich die Textarea befindet
     * @param  array   $description  Smiliebeschreibungen/BBCodebeschreibung
     * @return string  Html für Auswahl- Container
     */
     public function getHtmlDivSelectBbcOrSmilies ($cssClass, $cssIdForm, $description) {
        $return = sprintf(
                  '<div class="%s">%s' . "\n</div>\n",
                  $cssClass,
                  $this->getHtmlLinkInsertWithJava($cssIdForm, $description)
        );
        return $return;
     }


    /**
     * Erstellt das HTML für einen Link für die Auswahl des BBCodes/ Smilies
     *
     * @param  string  $cssIdForm    Name des CSS- Id`s des Formulares,
     *                               in dem sich die Textarea befindet
     * @param  array   $description  Array mit den
     *                               Smiliebeschreibungen/BBCodebeschreibung
     * @return string  HTML für den Link
     */
    public function getHtmlLinkInsertWithJava($cssIdForm, $description) {
        $path = 'system/modules/nlsh_guestbook/html/smilies/';
        $link = '';

        for ($i = 0, $count = count($description); $i < $count; $i++) {
            $link .= sprintf(
                    "\n
                    <a href=\"javascript:insert('%s', '%s', '%s')\">
                    <img src=\"%s\" alt=\"%s\" title=\"%s\" /></a>",
                    $cssIdForm,
                    $description[$i][0],
                    $description[$i][1],
                    $path . $description[$i][2],
                    $description[$i][0],
                    $description[$i][3]
            );
        }

        return $link;
    }


    /**
     * HTML- String für Link zu einem neuen Gästebucheintrag erzeugen
     *
     * @return  string  HTML- String für Link zum neuen Eintrag
     */
    public function getHtmlLinkForNewNlshGbEntrie() {
        $objPage = $GLOBALS['objPage'];

        $link = $this->delGetEntryFromRequest(
                $this->Environment->request,
                self::GET_INPUT_GBENTRIE
        );

        if ($link == FALSE) {
            $link = $this->generateFrontendUrl(array(
                    'id'       => $objPage->id,
                    'language' => $objPage->language,
                    'alias'    => $objPage->alias )
            );
        }

         // wichtig !! Kontrolle auf zusätzlichen Request
        $connector = (strpos($link, '?') !== FALSE) ? '&' : '?';

        $return = sprintf(
                  '<a class="linknewentrie" title="%s" href="%s">%s</a>',
                  $GLOBALS['TL_LANG']['nlsh_guestbook']['inputNewEntries'],
                  $link . $connector . self::GET_INPUT_GBENTRIE . '=TRUE',
                  $GLOBALS['TL_LANG']['nlsh_guestbook']['inputNewEntries']
        );
        return $return;
    }


    /**
     * bestimmten Get- Eintrag aus Request- String löschen
     *
     * @param  string $request   Request- String
     * @param  string $getEntry  zu löschender GET- Eintrag
     * @return string bereinigter Request- String
     */
     public function delGetEntryFromRequest($request, $getEntry) {
        if (preg_match('/(&(amp;)?|\?)' .  $getEntry . '=[^&]+/', $request)) {
            return preg_replace('/(&(amp;)?|\?)' . $getEntry . '=[^&]+/', '', $request);
        }

        return $request;
     }
}
?>