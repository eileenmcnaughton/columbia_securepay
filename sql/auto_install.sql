-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the existing tables - this section generated from drop.tpl
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_securepay`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * civicrm_securepay
-- *
-- * FIXME
-- *
-- *******************************************************/
CREATE TABLE `civicrm_securepay` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Securepay ID',
  `order_id` varchar(64) NOT NULL COMMENT 'Secure Pay Order ID',
  `contribution_id` int unsigned COMMENT 'Contribution ID',
  `receive_date` datetime COMMENT 'Date contribution was received - not necessarily the creation date of the record',
  `amount` decimal(20,2) NOT NULL COMMENT 'Total amount of this contribution. Use market value for non-monetary gifts.',
  `is_test` tinyint NOT NULL DEFAULT 0,
  `processing_status_id` int unsigned DEFAULT 1,
  `order_status_id` int unsigned NOT NULL DEFAULT 1,
  `first_name` varchar(64) COMMENT 'First Name.',
  `last_name` varchar(64) COMMENT 'Last Name.',
  `email` varchar(254) COMMENT 'Email address',
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the secure pay record was created.',
  `modified_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'When the secure pay record was created or modified.',
  `street_address` varchar(96) COMMENT 'Concatenation of all routable street address components (prefix, street number, street name, suffix, unit\n      number OR P.O. Box). Apps should be able to determine physical location with this data (for mapping, mail\n      delivery, etc.).',
  `supplemental_address_1` varchar(96) COMMENT 'Supplemental Address Information, Line 1',
  `city` varchar(64) COMMENT 'City, Town or Village Name.',
  `state` varchar(64) COMMENT 'State.',
  `postal_code` varchar(64) COMMENT 'Store both US (zip5) AND international postal codes. App is responsible for country/region appropriate validation.',
  `data` text COMMENT 'Secure Pay data',
  PRIMARY KEY (`id`),
  INDEX `UI_order_id`(order_id),
  INDEX `UI_contribution_id`(contribution_id),
  INDEX `UI_receive_date`(receive_date),
  INDEX `UI_amount`(amount),
  INDEX `UI_first_name`(first_name),
  INDEX `UI_last_name`(last_name),
  INDEX `UI_email`(email),
  CONSTRAINT FK_civicrm_securepay_contribution_id FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution`(`id`) ON DELETE SET NULL
)
ENGINE=InnoDB;
