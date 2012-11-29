<?php
/**
* Namespace der Erweiterung.
*/
namespace nlsh\guestbook;


/**
 * Contao- Hook 'AddComment' zum nachträglichen bearbeiten des eingegebenen Kommentares.
 * Natürlich nur, wenn Eintrag vom eigenem Modul.
 *
 * Hier werden die Smilies durch das HTML- img- Tags ersetzt
 *
 * und die Überschriften zu dem Kommentar eingefügt
 *
 * @copyright  Nils Heinold 2012
 * @author     Nils Heinold
 * @package    nlsh_guestbook
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
     * Neueingetragenen Eintrag bearbeiten und speichern
     *
     * @param int   ID des neu eingetragenen Gästebucheintrages
     * @param array Array mit neuem Gästebucheintrag
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
            $arrSmilies   = $this->arrSmilies;
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
     * Kontrolliert den String auf unerlaubte Eingaben.
     * !kopiert aus dem Core von Contao!
     *
     * @param  string  zu kontrollierender String
     * @return string  bereinigter String
     */
    protected function _checkString($strToCheck)
    {
        // Prevent cross-site request forgeries
        $strToCheck = preg_replace('/(href|src|on[a-z]+)="[^"]*(contao\/main\.php|typolight\/main\.php|javascript|vbscri?pt|script|alert|document|cookie|window)[^"]*"+/i', '$1="#"', $strToCheck);

        return $strToCheck;
    }
}