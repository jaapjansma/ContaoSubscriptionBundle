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

namespace Edeveloper\ContaoSubscriptionBundle\Backend;

use Contao\Controller;
use Contao\DataContainer;
use Contao\System;
use Edeveloper\ContaoSubscriptionBundle\Model\SubscriptionModel;

class SubscriptionModule {

  /**
   * Create invoice for subscription.
   *
   * @param \Contao\DataContainer $dc
   * @return void
   */
  public function createInvoice(DataContainer $dc) {
    $subscription = SubscriptionModel::findByPk($dc->id);
    $invoice = $subscription->createNewInvoice();

    $url = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'invoices', 'act' => 'edit', 'id' => $invoice->id, 'rt' => REQUEST_TOKEN]);
    Controller::redirect($url);
  }

}