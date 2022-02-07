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

namespace Edeveloper\ContaoSubscriptionBundle\Cron;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\CronJob;
use Doctrine\DBAL\Connection;
use Edeveloper\ContaoSubscriptionBundle\Model\InvoiceModel;
use Edeveloper\ContaoSubscriptionBundle\Model\SubscriptionModel;
use Psr\Log\LoggerInterface;

/**
 * Activate subscriptions and deactive expired subscriptions.
 *
 * @CronJob("minutely")
 */
class CreateRenewalSubscriptionInvoices {

  /**
   * @var \Doctrine\DBAL\Connection
   */
  protected $connection;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  public function __construct(Connection $connection, LoggerInterface $logger, ContaoFramework $framework) {
    $this->connection = $connection;
    $this->logger = $logger;
    $framework->initialize();
  }


  public function __invoke(): void {
    $subscriptionSql = "
        SELECT `id`   
        FROM `" . SubscriptionModel::getTable() . "` 
        WHERE 
          `status` = 2 
          AND `" . SubscriptionModel::getTable() . "`.`id` NOT IN (
            SELECT `subscription` 
            FROM `" . InvoiceModel::getTable() . "` 
            WHERE `paid` = 0
          )
        ORDER BY `" . SubscriptionModel::getTable() . "`.`id`   
        LIMIT 0, 100
    ";

    $objRows = $this->connection->prepare($subscriptionSql)->executeQuery();
    foreach($objRows as $row) {
      if (empty($row['id'])) {
        continue;
      }
      $subscription = SubscriptionModel::findByPk($row['id']);
      $invoice = $subscription->createNewInvoice();
      $loggerContext['invoice'] = $invoice->document_number;
      $loggerContext['subscription'] = $subscription->id;
      $this->logger->debug('Created invoice {invoice} for subscription {subscription}', $loggerContext);
    }
  }

}