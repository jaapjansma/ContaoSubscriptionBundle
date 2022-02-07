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

use Contao\Backend;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Edeveloper\ContaoSubscriptionBundle\Model\InvoiceModel;
use Edeveloper\ContaoSubscriptionBundle\Model\SubscriptionModel;

/**
 * This class renews an invoice as soon as it is marked as paid.
 *
 * There is one draw back you can mark an invoice as unpaid and mark it as paid
 * afterwards and the subscription would be renewed another time.
 */
class InvoicePaidListener
{

  /**
   * @var \Doctrine\DBAL\Connection
   */
  protected $connection;

  /**
   * @var InvoiceModel
   */
  protected $currentInvoice;

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

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
    if ($this->currentInvoice && !$this->currentInvoice->paid && $dc->activeRecord->paid && $dc->activeRecord->subscription) {
      $subsciption = SubscriptionModel::findByPk($dc->activeRecord->subscription);
      $subsciption->prolong();
    }
  }
}