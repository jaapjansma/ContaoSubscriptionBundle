<?php
/**
 * Copyright (C) 2022  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

$GLOBALS['TL_DCA']['tl_invoice'] = array
(
  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'sql' => array
    (
      'keys' => array
      (
        'id' => 'primary'
      )
    ),
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 1,
      'fields'                  => array('document_number', 'date'),
      'flag'                    => 0,
      'panelLayout'             => 'sort,filter,search,limit'
    ),
    'label' => array
    (
      'showColumns'             => true,
      'fields'                  => array('document_number', 'company_name', 'date', 'price', 'paid'),
    ),
    'global_operations' => array
    (
      'all' => array
      (
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
      )
    ),
    'operations' => array
    (
      'print_document' => array
      (
        'label'             => &$GLOBALS['TL_LANG']['tl_invoice']['print_document'],
        'href'              => 'key=print_document',
        'icon'              => 'bundles/contaosubscription/document-pdf-text.png',
      ),
      'edit' => array
      (
        'href'                => 'act=edit',
        'icon'                => 'edit.svg',
      ),
      'delete' => array
      (
        'href'                => 'act=delete',
        'icon'                => 'delete.svg',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_invoice']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
      ),
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                     => 'document_number,subscription,description,date,paid,,price;company_name,email,street,postal,city,country;invoice_note'
  ),

  // Subpalettes
  'subpalettes' => array
  (
  ),

  // Fields
  'fields' => array
  (
    'id' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL auto_increment"
    ),
    'tstamp' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL DEFAULT CURRENT_TIMESTAMP"
    ),
    'price' => array
    (
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class' => 'clr'),
      'sql'                     => "decimal(12,2) NOT NULL default '0.00'",
    ),
    'document_number' => array
    (
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('disabled'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'description' => array
    (
      'inputType'             => 'textarea',
      'eval'                  => array('tl_class' => 'clr'),
      'sql'                   => 'text NULL',
    ),
    'invoice_note' => array
    (
      'inputType'             => 'textarea',
      'eval'                  => array('tl_class' => 'clr'),
      'sql'                   => 'text NULL',
    ),
    'company_name' => array
    (
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'street' => array
    (
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'postal' => array
    (
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>32, 'tl_class'=>'w50'),
      'sql'                     => "varchar(32) NOT NULL default ''"
    ),
    'city' => array
    (
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'country' => array
    (
      'filter'                  => true,
      'inputType'               => 'select',
      'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
      'options_callback' => static function ()
      {
        return Contao\System::getCountries();
      },
      'sql'                     => "varchar(2) NOT NULL default ''"
    ),
    'email' => array
    (
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'rgxp'=>'email', 'decodeEntities'=>true, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'paid' => array
    (
      'filter'                  => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class' => 'clr'),
      'sql'                     => "char(1) NOT NULL DEFAULT '1'",
      'default'                 => '0',
    ),
    'date' => array
    (
      'inputType'               => 'text',
      'flag'                    => 5,
      'default'                 => time(),
      'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "int(10) NOT NULL DEFAULT CURRENT_TIMESTAMP"
    ),
    'subscription' => array(
      'filter'                  => true,
      'inputType'               => 'select',
      'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
      'foreignKey'              => \Edeveloper\ContaoSubscriptionBundle\Model\SubscriptionModel::getTable().'.company_name',
      'sql'                     => "int(10) unsigned NOT NULL default 0",
      'default'                 => '0',
    )
  )
);