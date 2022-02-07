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

namespace Edeveloper\ContaoSubscriptionBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Edeveloper\ContaoSubscriptionBundle\Model\InvoiceModel;

/**
 * This class generates an invoice number for each new invoice.
 *
 */
class InvoiceNumberListener
{

  /**
   * @var InvoiceModel
   */
  protected $currentInvoice;

  /**
   * @Callback(table="tl_invoice", target="config.onload")
   */
  public function onLoadCallback(DataContainer $dc): void {
    if ($dc->id) {
      $this->currentInvoice = InvoiceModel::findByPk($dc->id);
    }
  }


  /**
   * @Callback(table="tl_invoice", target="config.onsubmit")
   */
  public function onSubmitCallback(DataContainer $dc): void
  {
    if (!$this->currentInvoice || !$this->currentInvoice->document_number) {
      $db = \Database::getInstance();

      try {
        // Lock tables so no other order can get the same ID
        $db->lockTables(array(InvoiceModel::getTable() => 'WRITE'));
        $dc->activeRecord->document_number = InvoiceModel::generateDocumentNumber();
        if ($dc->id) {
          $db->prepare('UPDATE `' . InvoiceModel::getTable() . '` SET document_number=? WHERE id=?')
            ->execute($dc->activeRecord->document_number, $dc->id);
        }
      } catch (\Exception $e) {
        // Make sure tables are always unlocked
        $db->unlockTables();
        throw $e;
      }
    }
  }
}