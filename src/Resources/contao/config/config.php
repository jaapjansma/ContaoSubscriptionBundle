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

use \Edeveloper\ContaoSubscriptionBundle\Model\InvoiceModel;
use \Edeveloper\ContaoSubscriptionBundle\Model\SubscriptionModel;
use \Edeveloper\ContaoSubscriptionBundle\Backend\InvoiceModule;

$GLOBALS['BE_MOD']['accounts']['subscriptions'] = [
  'tables'            => [SubscriptionModel::getTable()]
];
$GLOBALS['BE_MOD']['accounts']['invoices'] = [
  'tables'            => [InvoiceModel::getTable()],
  'print_document'    => array(InvoiceModule::class, 'printDocument'),
];

$GLOBALS['TL_MODELS'][SubscriptionModel::getTable()] = SubscriptionModel::class;
$GLOBALS['TL_MODELS'][InvoiceModel::getTable()] = InvoiceModel::class;