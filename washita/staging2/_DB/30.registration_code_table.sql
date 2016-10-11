
CREATE TABLE `registration_code`(
ID int primary key NOT NULL AUTO_INCREMENT,
   CODE nvarchar(30) NULL,
   -- 0 - Usual
   -- 1 - Influenter
   USER_TYPE SMALLINT(3) NOT NULL DEFAULT 1,
   IS_USED BOOL NOT NULL DEFAULT 0,
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `registration_code` ADD INDEX `REGISTRATION_CODE_INDX` (`CODE`);


-- FOR A TEST // TEST DATA
-- insert into registration_code(CODE, USER_TYPE) values("INF120ART",1);
-- insert into registration_code(CODE, USER_TYPE) values("INF121ART",1);
-- insert into registration_code(CODE, USER_TYPE) values("INF122ART",1);
