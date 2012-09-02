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
 * Class ModuleNlshComments
 *
 * @copyright  Nils Heinold
 * @author     Nils Heinold
 * @package    nlsh_guestbook
 */
class ModuleNlshComments extends ModuleComments
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


    // Get- Parameter, um einen Eintrag zu erzeugen
    const GET_INPUT_GBENTRIE = 'nlsh_gb_input';


    public function generate()
    {
        // die gbentries.txt importieren
        //$this->importGbEntries('tl_files/');

        // Die Originalmethode ausführen
        $strOutput = parent::generate();

        $this->loadLanguageFile('tl_style');

        // BBCode definieren
        $this->arrBbcFormat = array
        (
            array ('[b]', '[/b]' , 'text_bold.png', $GLOBALS['TL_LANG']['tl_style']['bold']),
            array ('[i]','[/i]', 'text_italic.png', $GLOBALS['TL_LANG']['tl_style']['italic']),
            array ('[u]','[/u]', 'text_underline.png', $GLOBALS['TL_LANG']['tl_style']['underline']),
            array ('[img]','[/img]', 'picture_link.png', $GLOBALS['TL_LANG']['nlsh_guestbook']['insertPicture']),
            array ('[quote]','[/quote]', 'comment_add.png', $GLOBALS['TL_LANG']['nlsh_guestbook']['insertQoute']),
        );

        // Template wechseln
        $this->strTemplate = $this->com_nlsh_gb_template;
        $this->Template    = new FrontendTemplate($this->strTemplate);

        // String zerlegen und Übergabe in das Template
        $templateVars = $this->_getTemplateVars($strOutput);

        $this->Template->countCommentsText = $templateVars['countCommentsText'];
        $this->Template->form              = $templateVars['form'];
        $this->Template->pagination        = $templateVars['pagination'];
        $this->Template->countComments     = $templateVars['countComments'];
        $this->Template->gbEntries         = $templateVars['gbEntries'];

        // Wenn Link zum neuem Gästebucheintrag benutzt wurde
        if ($this->Input->get(self::GET_INPUT_GBENTRIE) === 'true')
        {
            $this->Template->inputNewEntrie = true;
        }

        // Html- Link für neuen Eintrag erzeugen
        $this->Template->htmlLinkNewEntrie = $this->getHtmlLinkForNewNlshGbEntrie();

        // $return vorbereiten
        $cssClass = ($this->cssID[1] == true) ? $this->cssID[1] . " " : '';
        $cssId    = ($this->cssID[0] == true) ? 'id="' . $this->cssID[0] . '" ' : '';

        $cssStyleMarginTop    = ($this->space[0] == true) ? 'margin-top:' . $this->space[0] . 'px;' : '';
        $cssStyleMarginBottom = ($this->space[1] == true) ? 'margin-bottom:' . $this->space[1] . 'px;' : '';
        $cssStyle             = ' style="' . $cssStyleMarginTop . $cssStyleMarginBottom . '"';

        //Templateausgabe
        return "\n<div " . $cssId . 'class="mod_nlsh_guestbook ' . $cssClass . 'block"' . $cssStyle . ">\n\n" . $this->Template->parse() . "</div>\n";
    }


    /**
     * Zerlegung des Initialstrings
     * @param string HTML- Ausgabestring des 'nlsh_gb_initial'- Templates
     * @return array Array mit den Daten
     */
    protected function _getTemplateVars($strFromInitialTemplate)
    {
        global $objPage;

        // Formulareingabe extrahieren
        $start = strpos($strFromInitialTemplate, '<form ');
        $end   = strpos($strFromInitialTemplate, '</form>') + 7;
        $form  = "\n<div class=\"form\">\n<!-- indexer::stop -->\n" . substr($strFromInitialTemplate, $start, $end - $start) . "\n</div>\n<!-- indexer::continue -->\n";

        // Formular erweitern mit Auswahl Smilies und BBCode und Überschriftenfeld
        if ( $this->com_bbcode == true)
        {
            $bbCode  = $this->_getHtmlDivSelectBbcOrSmilies('bbcode', 'com_tl_page_' . $objPage->id, $this->arrBbcFormat);
            $smilies = $this->_getHtmlDivSelectBbcOrSmilies('smilies', 'com_tl_page_' . $objPage->id, $this->arrSmilies);

            $form = str_replace('<div class="submit_container">', $bbCode . $smilies . "\n" . '<div class="submit_container">', $form);
        }

        $headline = "<div class =\"widget\">\n"
                    . "  <input id=\"ctrl_headline\" class=\"text\" type=\"text\" maxlength=\"255\" value=\"\" name=\"headline\" />"
                    . " <label for=\"ctrl_headline\"><span class=\"headline\">" . $GLOBALS['TL_LANG']['nlsh_guestbook']['headline'] . "</span></label>"
                    . "\n</div>\n";

        $startHeadline = strpos($form, '<textarea') - 23;

        $form = substr($form, 0, $startHeadline) . $headline  . substr($form, $startHeadline);

        $return['form'] = $form;

        // Array erzeugen
        $arrData = explode("^", $strFromInitialTemplate);
        $count   = count($arrData);

        // Wenn nur ein Feld, dann keine Daten, Mitteilung und zurück
        if($count == 1)
        {
            $return['countCommentsText'] = sprintf($GLOBALS['TL_LANG']['nlsh_guestbook']['howManyEntries'], 0);

            return $return;
        }

        ;
        // Pagination des Ausgabecontainers, falls vorhanden
        if (strpos($strFromInitialTemplate, '<div class="pagination') != false )
        {
            $start                = strpos($strFromInitialTemplate, '<div class="pagination');
            $end                  = strpos($strFromInitialTemplate, '</ul>') + 5;
            $return['pagination'] = "\n<!-- indexer::stop -->\n" . substr($strFromInitialTemplate, $start, $end - $start) . "\n\n </div>\n<!-- indexer::continue -->\n";
        }

        // Alle Gästebucheinträge holen zum Nummerieren der Einträge benötigt
        $arrGbEntries = $this->Database->prepare("SELECT * FROM tl_comments WHERE source=? AND parent=?" . (!BE_USER_LOGGED_IN ? " AND published=1" : "") . " ORDER BY date ASC")
                    ->execute('tl_page', $objPage->id)
                    ->fetchAllAssoc();

        $return['countCommentsText'] = sprintf($GLOBALS['TL_LANG']['nlsh_guestbook']['howManyEntries'], count($arrGbEntries));


        // Datensätze auslesen
        $newArr = array();

        for ($i = 0; $i < $count; $i++)
        {
            // Erster Eintrag und letzter Eintrag kann weg
            if ( ($i != 0) && ($i != $count))
            {
                $arrTemp = unserialize($arrData[$i]);

                // leere Einträge ignorieren
                if ($arrTemp['name'] == true)
                {
                    // Durchnummerieren
                    for ($a = 0; $a < count($arrGbEntries); $a++)
                    {
                        if ($arrGbEntries[$a]['id'] == $arrTemp['id'])
                        {
                            $arrTemp['nr'] = $a + 1;
                        }
                    }

                    //Überschriften herausholen
                    if (strpos($arrTemp['comment'], '[/h]'))
                    {
                        $start = strpos($arrTemp['comment'], '[h]');
                        $end   = strpos($arrTemp['comment'], '[/h]') + 4;

                        $arrTemp['headlineComment'] = substr($arrTemp['comment'], $start + 3 , $end - $start - 7);
                        $arrTemp['comment'] = substr($arrTemp['comment'], 0, $start) . substr($arrTemp['comment'], $end);
                    }

                    // und übergeben
                    $newArr[] = $arrTemp;
                };
            }
        }

        $return['countComments']  = count($newArr);
        $return['gbEntries']      = $newArr;

        return $return;
    }


    /**
     * HTML- String zur Auswahl der BB- Codes und Smilies
     * @param string CSS- Klasse für Div- Container
     * @param array Smiliebeschreibungen/BBCodebeschreibung
     * @return string Html für Auswahl- Container
     */
     protected function _getHtmlDivSelectBbcOrSmilies ($strClass, $strIdForm, $arrArray)
     {
        $return  = '<div class="' . $strClass . '">';
        $return .= $this->_getHtmlLinkInsertWithJava($strIdForm, $arrArray);
        $return .= "</div>\n";

        return $return;

     }


    /**
     * Erstellt das Html für einen Link für die Auswahl des BBCodes/ Smilies
     * @param string Name des Id`s des Formulares, in dem sich die Textarea befindet
     * @param array Array mit den Smiliebeschreibungen/BBCodebeschreibung
     * @return string HTML für den Link
     */
    protected function _getHtmlLinkInsertWithJava($strFormId, $arrArray)
    {
        $strUrl  = 'system/modules/nlsh_guestbook/html/smilies/';
        $strHtml = '';

        for ($i = 0; $i < count($arrArray); $i++)
        {
            $strHtml .= "\n<a href=\"javascript:insert('$strFormId', ' " . $arrArray[$i][0] . " ', ' " . $arrArray[$i][1] . " ')\"><img src=\"" . $strUrl . $arrArray[$i][2] . "\" alt=\"" . $arrArray[$i][0] . "\" title=\"" . $arrArray[$i][3] . "\" /></a>";
        }

        return $strHtml;
    }


    /**
     * HTML- String für Link zum neuen Eintrag erzeugen
     * @return string HTML- String für Link zum neuen Eintrag
     */
    public function getHtmlLinkForNewNlshGbEntrie()
    {

        $strOutput = "{{env::request}}";

        // wichtig !! Kontrolle auf zusätzliche $_GET
        if ( (strpos($this->Environment->request, "?") === false) || $_GET == false)
        {
            $strOutput .= '?';
        }
        else
        {
            $strOutput .= '&amp;';
        }
        $return = '<a class="linknewentrie" title="'. $GLOBALS['TL_LANG']['nlsh_guestbook']['inputNewEntries'] . '" href="' . $strOutput . self::GET_INPUT_GBENTRIE . '=true">' . $GLOBALS['TL_LANG']['nlsh_guestbook']['inputNewEntries'] . '</a>';

        return $return;
    }
}

?>