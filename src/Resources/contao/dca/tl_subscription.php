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

$GLOBALS['TL_DCA']['tl_subscription'] = array
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
    )
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 1,
      'fields'                  => array('start'),
      'flag'                    => 0,
      'panelLayout'             => 'sort,filter,search,limit'
    ),
    'label' => array
    (
      'showColumns'             => true,
      'fields'                  => array('company_name', 'active', 'max_users', 'usage_count', 'start', 'expire'),
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
      'edit' => array
      (
        'href'                => 'act=edit',
        'icon'                => 'edit.svg',
      ),
      'delete' => array
      (
        'href'                => 'act=delete',
        'icon'                => 'delete.svg',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_subscription']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
      ),
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                     => 'start,expire;active;max_users;price;{invoice_legend},company_name,email,street,postal,city,country;invoice_note'
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
      'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit'),
      'sql'                     => "decimal(12,2) NOT NULL default '0.00'",
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
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'rgxp'=>'email', 'unique'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'active' => array
    (
      'filter'                  => true,
      'inputType'               => 'checkbox',
      'eval'                    => array(),
      'sql'                     => "char(1) NOT NULL DEFAULT '1'",
      'default'                 => '1',
    ),
    'start' => array
    (
      'inputType'               => 'text',
      'flag'                    => 5,
      'default'                 => time(),
      'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "int(10) NOT NULL DEFAULT CURRENT_TIMESTAMP"
    ),
    'expire' => array
    (
      'inputType'               => 'text',
      'flag'                    => 5,
      'default'                 => strtotime('+1 year'),
      'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "int(10) NOT NULL DEFAULT CURRENT_TIMESTAMP"
    ),
    'max_users' => array
    (
      'inputType'               => 'text',
      'eval'                    => array('rgxp' => 'natural'),
      'sql'                     => "int(10) unsigned NOT NULL default 0",
      'flag'                    => 11,
      'default'                 => '0',
    ),
    'usage_count' => array
    (
      'inputType'               => 'text',
      'eval'                    => array('rgxp' => 'natural'),
      'sql'                     => "int(10) unsigned NOT NULL default 0",
      'flag'                    => 11,
      'default'                 => '0',
    ),
  )
);