<?php
/**
 * Namespace
 */
namespace PhpUnitTest\guestbook;


 /** PHPUnit Test der Klasse ModuleNlshComments
 *
 * Dies ist eine Testklasse für die Funktionen
 * der Klasse ModuleNlshComments
 *
 * Beispielaufruf in der Konsole:
 *
 * phpunit ModuleNlshCommentsTest
 *
 * Manual : http://phpunit.de/manual/2.3/de/
 *          http://phpunit.de/manual/3.6/en/index.html
 */


/**
 * Das Contao- System für den PHPUnit- Test  auf TL_MODE FRONTEND definieren.
 */
define('TL_MODE', 'FE');

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/initialize.php');


/**
 * PHPUnit laden
 */
require_once 'PHPUnit/Autoload.php';


/**
 * Die zu testende Klasse laden
 *
 * Da die zu testende Klasse keinen Konstruktor besitzt, dieser aber für den
 * Test benötigt wird, lesen wir die Datei der Klasse ein, erweitern sie um
 * den Konstruktor, speichern sie im Temp- Systemordner und includen diese
 * danach.
 * Gelöscht werden kann diese dann dort per Systemwartung
 */
$tempClass = file_get_contents(
    TL_ROOT . '/system/modules/nlsh_guestbook/modules/ModuleNlshComments.php'
);

$tempClass = str_replace('// PlatzhalterKontruktor', 'public function __construct(){}', $tempClass);

$handle    = fopen(TL_ROOT . '/system/tmp/ModuleNlshComments.php', 'w');

fwrite($handle, $tempClass);
fclose($handle);

require_once TL_ROOT . '/system/tmp/ModuleNlshComments.php';


/**
 * Sprache laden, da Modul sprachabhängig
 * ( Sprache hier: Deutsch)
 */
require_once TL_ROOT . '/system/modules/nlsh_guestbook/languages/de/default.php';


/** PHPUnit- Testklasse der Klasse ModuleNlshComments
 *
 * PHPUnit version 3.7.8 oder höher
 *
 * PHP version 5.3.2 oder höher
 *
 * @copyright  Nils Heinold (c) 2013
 * @author     Nils Heinold
 * @package    nlshGuestbook
 * @link       http://github.com/nlsh/nlsh_guestbook
 * @license    LGPL
 */
class ModuleNlshCommentsTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Übernimmt das Objekt der zu testenden Klasse
     *
     * das zu testende Objekt
     *
     * @var object
     */
    protected $object;


    /**
     * Testobjekt erzeugen und
     * $this->object->bolPhpUnitTest = TRUE hinzufügen
     *
     * Für jeden Test wird ein neues Objekt aus der Klasse erzeugt.
     *
     * @return void
     */
    protected function setUp() {
        $this->object = new \nlsh\guestbook\ModuleNlshComments();

        $this->object->bolPhpUnitTest = TRUE;
    }


    /**
     * Testobjekt wieder löschen!
     *
     * Nach jedem Durchlauf einer Test- Methode
     * wird das zu testende Objekt wieder gelöscht.
     *
     * @return void
     */
    protected function tearDown() {
        $this->object = FALSE;
    }


    /**
     * Eigenschaften des initialisierten Objektes testen
     *
     * Die Eigenschaften des initialisierten Objektes müssen sein:
     *
     *  1. protected $strTemplate muss ein String sein!
     *  2. protected $inputNewEntrie muss FALSE sein!
     *  3. protected $arrSmilies muss ein Array sein!
     *
     * @return void
     */
    public function testInitializedObject() {
        $this->assertAttributeInternalType (
                                        'string',
                                        'strTemplate',
                                        $this->object,
                                        'protected $strTemplate must be a string!'
        );
        $this->assertAttributeEquals (
                                        FALSE,
                                        'inputNewEntrie',
                                        $this->object,
                                        'protected $inputNewEntrie must be FALSE!'
        );
        $this->assertAttributeInternalType(
                'array',
                'arrSmilies',
                $this->object,
                'protected $arrSmilies must be an array!'
        );
    }


}
