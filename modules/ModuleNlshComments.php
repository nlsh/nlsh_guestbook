<?php
/**
* Namespace der Erweiterung
*/
namespace nlsh\guestbook;


/**
 * Übergibt die vom Core- Modul Comments über das 'Initial'- Template erzeugten Daten an das Template.
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
    protected $inputNewEntrie = false;


    /**
     * Definition des GET- Namens zur Abfrage des Gästebuch- Modules
     */
    const GET_INPUT_GBENTRIE = 'nlsh_gb_input';


    /**
     * Definition der Smilies in einem Array
     *
     * Definition der Smilies in einem Array
     */
    protected $arrSmilies = array
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
    * Display a wildcard in the back end
    * @return string
    */
    public function generate()
    {
    if (TL_MODE == 'BE')
    {
        $objTemplate = new \BackendTemplate('be_wildcard');

        $objTemplate->wildcard = '### nlsh_guestbook ###';
        $objTemplate->title = $this->headline;
        $objTemplate->id = $this->id;
        $objTemplate->link = $this->name;
        $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

        return $objTemplate->parse();
    }

        // Template wechseln
        $this->strTemplate = $this->com_nlsh_gb_template;
        $this->Template = new \FrontendTemplate($this->strTemplate);

    return parent::generate();
    }


    /**
    * Generate the module
    */
    protected function compile()
    {
    global $objPage;

        $this->import('Comments');
        $objConfig = new \stdClass();

        $objConfig->perPage        = $this->perPage;
        $objConfig->order          = $this->com_order;
        $objConfig->template       = $this->com_template;
        $objConfig->requireLogin   = $this->com_requireLogin;
        $objConfig->disableCaptcha = $this->com_disableCaptcha;
        $objConfig->bbcode         = $this->com_bbcode;
        $objConfig->moderate       = $this->com_moderate;

        $this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_page', $objPage->id, $GLOBALS['TL_ADMIN_EMAIL']);

        // Sprache nachladen
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

        // Anzahl aller Einträge ermitteln, um Nummer des Eintrages festlegen zu können
        $arrGbEntries = $this->Database->prepare("SELECT * FROM tl_comments WHERE source=? AND parent=?" . (!BE_USER_LOGGED_IN ? " AND published=1" : "") . " ORDER BY date ASC")
                    ->execute('tl_page', $objPage->id)
                    ->fetchAllAssoc();

        // Text für Anzahl der Einträge eintragen
        $this->Template->howManyEntriesText = sprintf($GLOBALS['TL_LANG']['nlsh_guestbook']['howManyEntries'], count($arrGbEntries));

        // die vom nlsh_gb_initial- Template übergebenen Comments- Daten wieder in ein Array umwandeln
        // und die Nummer hinzufügen
        foreach( $this->Template->comments as $comment)
        {
            $tempComment = unserialize(trim($comment));

            // Durchnummerieren
            for ($a = 0; $a < count($arrGbEntries); $a++)
            {
                if ($arrGbEntries[$a]['id'] == $tempComment['id'])
                {
                    $tempComment['nr'] = $a + 1;
                }
            }

            //Überschriften herausholen
            if (strpos($tempComment['comment'], '[/h]'))
            {
                $start = strpos($tempComment['comment'], '[h]');
                $end   = strpos($tempComment['comment'], '[/h]') + 4;

                $tempComment['headlineComment'] = substr($tempComment['comment'], $start + 3 , $end - $start - 7);
                $tempComment['comment'] = substr($tempComment['comment'], 0, $start) . substr($tempComment['comment'], $end);
            }

            $tempArrComments[] = $tempComment;
        }

        // und in das Template übernehmen
        $this->Template->comments = $tempArrComments;

        // Anzahl der übermittelten Kommentare ins Template
        $this->Template->countComments = count($this->Template->comments);

        // Wenn Link für ein neuen Gästebucheintrag benutzt wurde
        // Wird im Template abgefragt und benötigt!
        if ($this->Input->get(self::GET_INPUT_GBENTRIE) === 'true')
        {
            $this->Template->inputNewEntrie = true;
        }

        // Html- Link für neuen Eintrag erzeugen
        $this->Template->htmlLinkNewEntrie = $this->getHtmlLinkForNewNlshGbEntrie();

        // Formular erweitern mit Auswahl Smilies und BBCode und Überschriftenfeld
        if ( $this->com_bbcode == true)
        {
            $bbCode  = $this->getHtmlDivSelectBbcOrSmilies('bbcode', 'com_tl_page_' . $objPage->id, $this->arrBbcFormat);
            $smilies = $this->getHtmlDivSelectBbcOrSmilies('smilies', 'com_tl_page_' . $objPage->id, $this->arrSmilies);
        }

        // Formular für Dateneingabe vorbereiten
        $this->form                = new \FrontendTemplate('nlsh_mod_comment_form');
        $this->form->requireLogin  = $this->Template->requireLogin;
        $this->form->confirm       = $this->Template->confirm;
        $this->form->allowComments = $this->Template->allowComments;
        $this->form->action        = str_replace( '?' . self::GET_INPUT_GBENTRIE . '=true', '', $this->Template->action);
        $this->form->action        = str_replace( '&amp;' . self::GET_INPUT_GBENTRIE . '=true', '', $this->form->action);
        $this->form->formId        = $this->Template->formId;
        $this->form->bbCode        = $bbCode;
        $this->form->smilies       = $smilies;
        $this->form->submit        = $this->Template->submit;
        $this->form->fields        = $this->Template->fields;
        $this->form->cancelValue   = $GLOBALS['TL_LANG']['nlsh_guestbook']['cancelButton'];
        $this->Template->form      = $this->form->parse();

    }


    /**
     * HTML- String zur Auswahl der BB- Codes und Smilies erzeugen
     *
     * @param  string  CSS- Klasse für Div- Container
     * @param  string  Name des CSS-ID`s des Formulares, in dem sich die Textarea befindet
     * @param  array   Smiliebeschreibungen/BBCodebeschreibung
     * @return string  Html für Auswahl- Container
     */
     public function getHtmlDivSelectBbcOrSmilies ($strClass, $strCssIdForm, $arrArray)
     {
        return sprintf( '<div class="%s">%s' . "\n</div>\n",
                $strClass,
                $this->getHtmlLinkInsertWithJava($strCssIdForm, $arrArray)
        );
     }


    /**
     * Erstellt das HTML für einen Link für die Auswahl des BBCodes/ Smilies
     *
     * @param  string  Name des CSS- Id`s des Formulares, in dem sich die Textarea befindet
     * @param  array   Array mit den Smiliebeschreibungen/BBCodebeschreibung
     * @return string  HTML für den Link
     */
    public function getHtmlLinkInsertWithJava($strCssFormId, $arrArray)
    {
        $strUrl  = 'system/modules/nlsh_guestbook/html/smilies/';
        $strHtml = '';

        for ($i = 0; $i < count($arrArray); $i++)
        {
            $strHtml .= sprintf("\n <a href=\"javascript:insert('%s', '%s', '%s')\"><img src=\"%s\" alt=\"%s\" title=\"%s\" /></a>",
                         $strCssFormId,
                         $arrArray[$i][0],
                         $arrArray[$i][1],
                         $strUrl . $arrArray[$i][2],
                         $arrArray[$i][0],
                         $arrArray[$i][3]
            );
        }

        return $strHtml;
    }


    /**
     * HTML- String für Link zum neuen Gästebucheintrag erzeugen
     *
     * @return string  HTML- String für Link zum neuen Eintrag
     */
    public function getHtmlLinkForNewNlshGbEntrie()
    {
        $strOutput = $this->Environment->request;

        // wichtig !! Kontrolle auf zusätzlichen Request
        ($strOutput{0} === '?') ? $strOutput = $strOutput . '&amp;' : $strOutput = '?';

        return sprintf(
                '<a class="linknewentrie" title="%s" href="%s">%s</a>',
                $GLOBALS['TL_LANG']['nlsh_guestbook']['inputNewEntries'],
                $strOutput . self::GET_INPUT_GBENTRIE . '=true',
                $GLOBALS['TL_LANG']['nlsh_guestbook']['inputNewEntries']
        );
    }
}
