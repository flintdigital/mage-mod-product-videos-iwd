<?php
$installer = $this;
$installer->startSetup();
$installer->run("
        DROP TABLE IF EXISTS {$this->getTable('iwd_video')};
        CREATE TABLE {$this->getTable('iwd_video')}(
          `video_id` INT(11) NOT NULL AUTO_INCREMENT,
          `title` VARCHAR(255) NOT NULL,
          `description` TEXT,
          `url` VARCHAR(500) NOT NULL,
          `video_type` VARCHAR(15) NOT NULL,
          `video_store_view` VARCHAR(500) NULL DEFAULT NULL,
          `video_status` BOOLEAN NOT NULL DEFAULT TRUE,
          `image` VARCHAR(500) NULL DEFAULT NULL,
          PRIMARY KEY (`video_id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

        DROP TABLE IF EXISTS {$this->getTable('iwd_product_video')};
        CREATE TABLE {$this->getTable('iwd_product_video')}(
          `entity_id` INT(11) NOT NULL AUTO_INCREMENT,
          `video_id` INT(11) NOT NULL,
          `product_id` INT(11) NOT NULL,
          `video_position` INT(5) NOT NULL DEFAULT '1',
          PRIMARY KEY (`entity_id`),
          CONSTRAINT `FK_iwd_pv_video` FOREIGN KEY (`video_id`) REFERENCES {$this->getTable('iwd_video')} (`video_id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_iwd_pv_product` FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");
$installer->endSetup();
