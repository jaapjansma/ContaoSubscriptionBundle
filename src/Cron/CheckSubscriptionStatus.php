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

use Contao\CoreBundle\ServiceAnnotation\CronJob;
use Doctrine\DBAL\Connection;
use Edeveloper\ContaoSubscriptionBundle\Model\SubscriptionModel;

/**
 * Activate subscriptions and deactive expired subscriptions.
 *
 * @CronJob("minutely")
 */
class CheckSubscriptionStatus {

  /**
   * @var \Doctrine\DBAL\Connection
   */
  protected $connection;

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function __invoke(): void {
    $inactiveSql = "
      UPDATE `" . SubscriptionModel::getTable() . "` 
      SET `status` = 0 
      WHERE `status` != 0 AND (`start` IS NOT NULL AND FROM_UNIXTIME(`start`) > CURRENT_DATE())";
    $this->connection->executeQuery($inactiveSql);
    $activateSql = "
      UPDATE `" . SubscriptionModel::getTable() . "` 
      SET `status` = 1 
      WHERE `status` != 1 AND (`start` IS NULL OR FROM_UNIXTIME(`start`) < CURRENT_DATE()) AND (`expire` IS NULL OR FROM_UNIXTIME(`expire`) > CURRENT_DATE())";
    $this->connection->executeQuery($activateSql);
    $graceSql = "
      UPDATE `" . SubscriptionModel::getTable() . "` 
      SET `status` = 2 
      WHERE `status` IN (0, 1) AND (`expire` IS NOT NULL AND FROM_UNIXTIME(`expire`) < CURRENT_DATE() AND FROM_UNIXTIME(`expire`) < DATE_ADD(NOW(), INTERVAL 3 MONTH))";
    $this->connection->executeQuery($graceSql);
    $expireSql = "
      UPDATE `" . SubscriptionModel::getTable() . "` 
      SET `status` = 3 
      WHERE `status` IN (0, 1, 2) AND (`expire` IS NOT NULL AND FROM_UNIXTIME(`expire`) >= DATE_ADD(NOW(), INTERVAL 3 MONTH))";
    $this->connection->executeQuery($expireSql);
  }

}