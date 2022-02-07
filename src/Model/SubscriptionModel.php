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

class SubscriptionModel extends Model {

  protected static $strTable = 'tl_subscription';

  /**
   * Create new invoice.
   *
   * @return \Edeveloper\ContaoSubscriptionBundle\Model\InvoiceModel
   */
  public function createNewInvoice(): InvoiceModel {
    \Contao\System::loadLanguageFile(InvoiceModel::getTable());
    $invoice = new InvoiceModel();
    $invoice->date = time();
    $invoice->tstamp = time();
    $invoice->description = sprintf($GLOBALS['TL_LANG']['tl_invoice']['subscription_description'], $this->max_users);
    $invoice->price = $this->price;
    $invoice->subscription = $this->id;
    $invoice->paid = 0;
    $invoice->company_name = $this->company_name;
    $invoice->email = $this->email;
    $invoice->street = $this->street;
    $invoice->postal = $this->postal;
    $invoice->city = $this->city;
    $invoice->country = $this->country;
    $invoice->invoice_note = $this->invoice_note;
    $invoice->save();
    return $invoice;
  }

}