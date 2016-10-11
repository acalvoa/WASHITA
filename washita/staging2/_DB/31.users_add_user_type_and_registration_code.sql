
   -- 0 - Usual
   -- 1 - Influenter
ALTER TABLE `users` ADD USER_TYPE SMALLINT(3) NOT NULL DEFAULT 0;


ALTER TABLE `users` ADD REGISTRATION_CODE nvarchar(30) NULL;


