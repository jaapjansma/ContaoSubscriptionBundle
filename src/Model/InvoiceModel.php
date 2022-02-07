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

namespace Edeveloper\ContaoSubscriptionBundle\Model;

use Contao\Model;
use Contao\Model\Registry;
use Patchwork\Utf8;

class InvoiceModel extends Model {

  protected static $strTable = 'tl_invoice';

  /**
   * Modify the current row before it is stored in the database
   *
   * @param array $arrSet The data array
   *
   * @return array The modified data array
   */
  protected function preSave(array $arrSet)
  {
    if (!Registry::getInstance()->isRegistered($this)) {
      // This is a new record.
      $arrSet['document_number'] = static::generateDocumentNumber();
    }
    return $arrSet;
  }

  public static function generateDocumentNumber() {
    $db = \Database::getInstance();
    $strPrefix = date('Y').'-';
    $intDigits = 8;
    $strPrefix = \Controller::replaceInsertTags($strPrefix, false);
    $intPrefix = Utf8::strlen($strPrefix);
    $prefixCondition = ($strPrefix != '' ? " AND document_number LIKE '$strPrefix%'" : '');
    // Retrieve the highest available order ID
    $objMax = $db
      ->prepare("SELECT `document_number` FROM `".InvoiceModel::getTable()."` WHERE 1 $prefixCondition ORDER BY CAST(" . ($strPrefix != '' ? 'SUBSTRING(document_number, ' . ($intPrefix + 1) . ')' : 'document_number') . ' AS UNSIGNED) DESC')
      ->limit(1)
      ->execute();

    $intMax = (int) substr($objMax->document_number, $intPrefix);
    return $strPrefix . str_pad($intMax + 1, $intDigits, '0', STR_PAD_LEFT);
  }

}