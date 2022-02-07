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
 * Update the usage count
 *
 * @CronJob("minutely")
 */
class UpdateSubscriptionUsageCount {

  /**
   * @var \Doctrine\DBAL\Connection
   */
  protected $connection;

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function __invoke(): void {
    $sql = "
        UPDATE `" . SubscriptionModel::getTable() ."`
        SET `usage_count` = 0
        WHERE `status` = '0' OR `status` = '1' OR `status` = '2'";
    $this->connection->executeQuery($sql);

    $sql = "
        UPDATE `" . SubscriptionModel::getTable() ."`
        SET `usage_count` = (
          SELECT COUNT(*) 
          FROM `tl_member` 
          WHERE `disable` !='1' AND (`start` = '' OR `start` <= NOW()) AND (`stop` = '' OR `stop` > NOW())
          AND `subscription` = `" . SubscriptionModel::getTable() ."`.`id`
          GROUP BY `subscription`
        )
        WHERE `status` = '0' OR `status` = '1' OR `status` = '2'";
    $this->connection->executeQuery($sql);
  }

}