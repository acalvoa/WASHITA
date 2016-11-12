


ALTER TABLE `users` ADD `TEMP_CODE_PASSWORD` VARCHAR(255) NULL COMMENT 'Temporary code to change the password';
ALTER TABLE `users` ADD `TEMP_CODE_PASSWORD_VALID_TILL` DATETIME NULL COMMENT 'Temporary code validity';
