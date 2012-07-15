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
 * Table tl_nlsh_guestbook 
 */
$GLOBALS['TL_DCA']['tl_nlsh_guestbook'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'closed'                      => true,
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('date'),
            'flag'                    => 6,
            'panelLayout'             => 'filter;search,limit',
        ),
        'label' => array
        (
            'fields'                  => array('author'),
            'format'                  => '%s',
            'label_callback'          => array('tl_nlsh_guestbook', 'addLabel')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('tl_nlsh_guestbook', 'toggleIcon')
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array(''),
        'default'                     => '{author_legend},
                                            author,
                                            email,
                                            webadress;
                                          {entry_legend},
                                            headline,
                                            entry;
                                          {published_legend},
                                            published;'
    ),

    // Subpalettes
    'subpalettes' => array
    (
        ''                            => ''
    ),

    // Fields
    'fields' => array
    (
        'author' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['author'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
        ),
        'email' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['email'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class' => 'w50')
        ),
        'webadress' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['webadress'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class' => 'w50')
        ),
        'headline' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['headline'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
        ),
        'entry' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['entry'],
            'exclude'                 => true,
            'inputType'               => 'textarea',
            'eval'                    => array('mandatory'=>true, 'rte'=>'tinyMCE', 'helpwizard'=>true)
        ),
        'published' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_nlsh_guestbook']['published'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => array()
        ),
    )
);


/**
* class tl_nlsh_guestbook
*
* Enthält Funktionen einzelner Felder der Konfiguration
* @package nlsh_guestbook
*/
class tl_nlsh_guestbook extends Backend
{


    /**
    * Import the back end user object
    */
    public function __construct()
    {
    parent::__construct();
    $this->import('BackendUser', 'User');
    }


	/**
* Add Label
* aus dem DCA des tl_content; list -> 'child_record_callback'   => array('tl_content', 'addCteType'
* @param array
* @return string
*/
public function addLabel($arrRow)
    {
        $key = $arrRow['published'] ? 'published' : 'published';
        $class = 'limit_height h20';

        return '
            <div class="cte_type ' . $key . '">
            <strong><a href="mailto:' . $arrRow['email'] .'" title="mailto:' . $arrRow['email'] . '">' . $arrRow['author'] . '</a></strong> '
            . '&nbsp;' . $GLOBALS['TL_LANG']['MSC']['nlsh_guestbook']['list_entries_date'] . '&nbsp;'
            . $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['date']) .
            '</div>
            <div><p><strong>' . $arrRow['headline'] . '</strong></p></div>
            <div class="' . trim($class) . '">' . $arrRow['entry'] . '</div>' . "\n";
    }


     /**
    * Return the "toggle visibility" button, aus tl_article kopiert
    * @param array
    * @param string
    * @param string
    * @param string
    * @param string
    * @param string
    * @return string
    */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen($this->Input->get('tid')))
        {
            $this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_nlsh_guestbook::published', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }

        $objPage = $this->Database->prepare("SELECT * FROM tl_nlsh_guestbook WHERE id=?")
                    ->limit(1)
                    ->execute($row['pid']);

        if (!$this->User->isAdmin && !$this->User->isAllowed(4, $objPage->row()))
        {
            return $this->generateImage($icon) . ' ';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
    }


    /**
    * Disable/enable a user group
    * @param integer
    * @param boolean
    */
    public function toggleVisibility($intId, $blnVisible)
    {
        // Check permissions to edit
        $this->Input->setGet('id', $intId);
        $this->Input->setGet('act', 'toggle');
        //$this->checkPermission();

        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_nlsh_guestbook::published', 'alexf'))
        {
            $this->log('Not enough permissions to publish/unpublish tl_nlsh_guestbook ID "'.$intId.'"', 'tl_nlsh_guestbook toggleVisibility', TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }

        $this->createInitialVersion('tl_nlsh_guestbook', $intId);

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_nlsh_guestbook']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_nlsh_guestbook']['fields']['published']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
            }
        }

        // Update the database
        $this->Database->prepare("UPDATE tl_nlsh_guestbook SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
                    ->execute($intId);

        $this->createNewVersion('tl_nlsh_guestbook', $intId);
    }
}
?>