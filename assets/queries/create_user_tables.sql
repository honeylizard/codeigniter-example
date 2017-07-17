--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user_auth` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `updated` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  `deleted_on` TIMESTAMP NULL ,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `user_meta`
--

CREATE TABLE IF NOT EXISTS `user_meta` (
  `user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `updated` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `first_name` VARCHAR(255) NULL ,
  `last_name` VARCHAR(255) NULL ,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `user_meta`
--
ALTER TABLE `user_meta`
  ADD CONSTRAINT `_user_meta_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_auth` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table structure for table `user_settings`
--

CREATE TABLE IF NOT EXISTS `user_settings` (
  `user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `updated` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `language` ENUM('en-US') NOT NULL DEFAULT 'en-US' ,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `user_meta`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `_user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_auth` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table structure for table `user_reset_tokens`
--

CREATE TABLE IF NOT EXISTS `user_reset_tokens` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(10) UNSIGNED NOT NULL ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `token` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `user_reset_tokens`
--
ALTER TABLE `user_reset_tokens`
  ADD CONSTRAINT `_user_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_auth` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
