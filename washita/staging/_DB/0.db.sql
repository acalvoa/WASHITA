#USE washita_2016
CREATE USER 'washita_web'@'localhost'  # washit
  IDENTIFIED BY 'U4Dnpq9ZbGzJMVsMzo';#'washit.334411';


GRANT ALL PRIVILEGES ON *.* TO 'washita_web'@'localhost';


#USE washita_2016;

DROP TABLE IF EXISTS `orders`;

create table orders(
   ID int primary key NOT NULL AUTO_INCREMENT,
   ORDER_NUMBER nvarchar(32) NULL,
   NAME nvarchar(256) NOT NULL,
   ADDRESS nvarchar(1024) NOT NULL,
   EMAIL varchar(124) NULL,
   PHONE varchar(20) NULL,
   WEIGHT decimal NOT NULL,
   IS_IRONING BIT(1) NULL,
   IS_ECO BIT(1) NULL,
   IS_HYPOALLERGENIC BIT(1) NULL,
   IS_EXTRASTRONG BIT(1) NULL,
   DISCOUNT_COUPON nvarchar(10) NULL,
   PRICE_WITH_DISCOUNT decimal NOT NULL,
   PRICE_WITHOUT_DISCOUNT decimal NOT NULL,
    # 0 - no payment
    # 1 - payment attempt (failed or partial)
    # 2 - successfull payment (the sum is correct and transaction confirmed)
   PAYMENT_STATUS SMALLINT(3) NOT NULL DEFAULT 0,
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `orders` ADD INDEX `ORDER_NUMBER_INDX` (`ORDER_NUMBER`);


DROP TABLE IF EXISTS `discount`;

create table discount(
   ID int primary key NOT NULL AUTO_INCREMENT,
   COUPON nchar(6) NOT NULL,
   VALUE DECIMAL(5,2) NOT NULL, 
   VALID_TILL DATETIME NOT NULL
);

ALTER TABLE `discount` ADD INDEX `COUPON_INDX` (`COUPON`);


INSERT INTO `discount` (`ID`, `COUPON`, `VALUE`, `VALID_TILL`) VALUES (NULL, 'TEST20', '20', '2020-02-17 00:00:00');
 
 #USE washita_2016;

DROP TABLE IF EXISTS `flow_payment`;

create table flow_payment(
   ID int primary key NOT NULL AUTO_INCREMENT,
   ORDER_NUMBER nvarchar(32) NULL,
   FLOW_NUMBER nvarchar(50) NULL,   
   STATUS nvarchar(32) NULL,
   TRANSACTION_AMOUNT DECIMAL NULL,
   PAYER_EMAIL nvarchar(100) NULL,
   DESCRIPTION nvarchar(500) NULL,
   # 0 - request for transaction confirmation from flow service (confirma page)
   # 1 - failed end of payment (fracaso page)
   # 2 - successful end of payment (exito page)  
   RESPONSE_TYPE smallint(3) NULL,
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `flow_payment` ADD INDEX `FLOW_PAYMENT_ORDER_NUMBER_INDX` (`ORDER_NUMBER`);

ALTER TABLE `orders` ADD COLUMN `PICKUP_FROM` TIMESTAMP NOT NULL;
ALTER TABLE `orders` ADD COLUMN `PICKUP_TILL` TIMESTAMP NOT NULL;



ALTER TABLE `orders` ADD INDEX `ORDER_PICKUP_INDX` (`PICKUP_FROM`,`PICKUP_TILL`);


ALTER TABLE `orders` ADD COLUMN `IS_ONLY_IRONING` BIT(1) NULL;


ALTER TABLE `orders` ADD COLUMN `DROPOFF_FROM` TIMESTAMP NULL;
ALTER TABLE `orders` ADD COLUMN `DROPOFF_TILL` TIMESTAMP NULL;

#DROP TABLE IF EXISTS `users`;

create table users(
   ID int primary key NOT NULL AUTO_INCREMENT,
   EMAIL varchar(124) NOT NULL,
   NOTIFICATION_EMAIL varchar(124) NOT NULL,
   PASSWORD varchar(512) NOT NULL,
   NAME nvarchar(256) NOT NULL,
   LASTNAME nvarchar(256) NOT NULL,
   IS_COMPLETE BIT(1) NOT NULL DEFAULT 0,
   FIRST_FAILED_LOGIN_TIME INT UNSIGNED NOT NULL DEFAULT 0,
   FAILED_LOGIN_COUNT INT UNSIGNED NOT NULL DEFAULT 0,
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `users` ADD INDEX `USERS_EMAIL_INDX` (`EMAIL`);


# add actual weight
ALTER TABLE `orders` ADD COLUMN `ACTUAL_WEIGHT` decimal NULL;
ALTER TABLE `orders` ADD COLUMN `ADDITIONAL_PRICE_WITHOUT_DISCOUNT` decimal NULL;
ALTER TABLE `orders` ADD COLUMN `ADDITIONAL_PRICE_WITH_DISCOUNT` decimal NULL;




ALTER TABLE `users` ADD `AUTH_PROVIDER_NAME` VARCHAR(255) NULL COMMENT 'Provider name';
ALTER TABLE `users` ADD `AUTH_PROVIDER_UID` VARCHAR(255) NULL COMMENT 'Provider user ID';
 
CREATE UNIQUE INDEX USERS_AUTH_PROVIDER_UNIQUE_INDX ON users (AUTH_PROVIDER_NAME, AUTH_PROVIDER_UID);
 
ALTER TABLE `users` ADD INDEX `USERS_AUTH_PROVIDER_UID_INDX` (`AUTH_PROVIDER_UID`);

ALTER TABLE `users` ADD `TEMP_CODE_PASSWORD` VARCHAR(255) NULL COMMENT 'Temporary code to change the password';
ALTER TABLE `users` ADD `TEMP_CODE_PASSWORD_VALID_TILL` DATETIME NULL COMMENT 'Temporary code validity';




ALTER TABLE `orders` ADD `IS_FEEDBACK_REQUESTED` BOOL NOT NULL DEFAULT 0 COMMENT 'Is email sent';

ALTER TABLE `orders` ADD `FEEDBACK_CODE` varchar(40) NULL;
ALTER TABLE `orders` ADD INDEX `ORDERS_FEEDBACK_CODE_INDX` (`FEEDBACK_CODE`);



create table order_feedback(
   `ID` int primary key NOT NULL AUTO_INCREMENT,
   `RATING` tinyint COMMENT 'User rating',
   `TEXT` varchar(2000) COMMENT 'User feedback',
   `ORDER_NUMBER` nvarchar(32) NULL,
   `CREATE_DATE` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE `order_feedback` ADD `FEEDBACK_CODE` varchar(40) NULL;

ALTER TABLE `order_feedback` ADD INDEX `ORDER_FEEDBACK_ORDER_NUMBER_INDX` (`ORDER_NUMBER`);

ALTER TABLE `order_feedback` ADD `RATING_EASINESS` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_IRONING` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_WASHING` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_RECOMMEND` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_PICKUP` tinyint NULL;

ALTER TABLE `order_feedback` MODIFY `RATING` decimal;



create table wash_item(
   ID int primary key NOT NULL AUTO_INCREMENT,
   NAME nvarchar(256) NOT NULL,
   ITEM_WEIGHT decimal(3,2) NOT NULL COMMENT 'In kilos', 
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

create table order_wash_items(
   ORDER_NUMBER nvarchar(32) NULL,
   WASH_ITEM_ID INT NOT NULL,
   COUNT INT NOT NULL,
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE `order_wash_items` ADD INDEX `ORDER_WASH_ITEMS_ORDER_NUMBER_INDX` (`ORDER_NUMBER`);

ALTER TABLE `order_feedback` ADD `RATING_OVERALL` tinyint NULL;



ALTER TABLE `wash_item` ADD `IMAGE_FILE_NAME` nvarchar(1024) NULL;



create table city(
   ID int primary key NOT NULL AUTO_INCREMENT,
   NAME varchar(124) NOT NULL,   
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO `city` (`NAME`) VALUES ('Viña');
INSERT INTO `city` (`NAME`) VALUES ('Santiago');

create table city_area(
   ID int primary key NOT NULL AUTO_INCREMENT,
   CITY_ID INT NOT NULL,
   NAME varchar(124) NOT NULL,   
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);



INSERT INTO `city_area` (`CITY_ID`, `NAME`) VALUES (1, 'Reñaca');
INSERT INTO `city_area` (`CITY_ID`, `NAME`) VALUES (1, 'Plan Viña del Mar');
INSERT INTO `city_area` (`CITY_ID`, `NAME`) VALUES (1, 'Concón');

INSERT INTO `city_area` (`CITY_ID`, `NAME`) VALUES (2, 'Providencia');
INSERT INTO `city_area` (`CITY_ID`, `NAME`) VALUES (2, 'Las Condes');


ALTER TABLE `orders` 
    ADD `CITY_AREA_ID` INT NULL;

alter table wash_item add ITEM_DRY_CLEAN_PRICE decimal(10,2) NOT NULL default 0,
                      add ITEM_SPECIAL_CLEAN_PRICE decimal(10,2) NOT NULL default 0;

alter table `orders` drop column IS_ECO,
                    drop column      IS_HYPOALLERGENIC, 
                    drop column        IS_EXTRASTRONG;



alter table `orders` add WASH_TYPE SMALLINT(3);



UPDATE `orders` o1, `orders` o2 
SET o1.WASH_TYPE = (SELECT CASE WHEN o2.IS_ONLY_IRONING THEN 1 ELSE 0 END) 
WHERE o1.ID = o2.ID;

ALTER TABLE `discount` MODIFY VALUE decimal(10,2) NOT NULL,
                       MODIFY COUPON varchar(30) NOT NULL,
                       ADD IS_PERCENT BOOL NOT NULL DEFAULT 1,
                       ADD IS_ONE_TIME BOOL NOT NULL DEFAULT 0;



ALTER TABLE `discount` ADD IS_ONE_TIME_USED BOOL NOT NULL DEFAULT 0;


ALTER TABLE `orders` MODIFY DISCOUNT_COUPON varchar(30) NOT NULL;

ALTER TABLE `order_wash_items` ADD IS_ACTUAL BOOL NOT NULL DEFAULT 0;
ALTER TABLE `orders` ADD COMMENT varchar(3000)  NULL;
ALTER TABLE `orders` MODIFY WEIGHT decimal(6,2) NOT  NULL;
CREATE TABLE `registration_code`(
ID int primary key NOT NULL AUTO_INCREMENT,
   CODE nvarchar(30) NULL,
   # 0 - Usual
   # 1 - Influenter
   USER_TYPE SMALLINT(3) NOT NULL DEFAULT 1,
   IS_USED BOOL NOT NULL DEFAULT 0,
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `registration_code` ADD INDEX `REGISTRATION_CODE_INDX` (`CODE`);


# FOR A TEST // TEST DATA
# insert into registration_code(CODE, USER_TYPE) values("INF120ART",1);
# insert into registration_code(CODE, USER_TYPE) values("INF121ART",1);
# insert into registration_code(CODE, USER_TYPE) values("INF122ART",1);

   # 0 - Usual
   # 1 - Influenter
ALTER TABLE `users` ADD USER_TYPE SMALLINT(3) NOT NULL DEFAULT 0;


ALTER TABLE `users` ADD REGISTRATION_CODE nvarchar(30) NULL;



# Starter Type of discount
ALTER TABLE `discount` ADD INFLUENCER_USER_ID INT NULL;




ALTER TABLE `orders` ADD INDEX `ORDERS_DISCOUNT_COUPON_AND_EMAIL_INDX` (`DISCOUNT_COUPON`,`EMAIL`);

ALTER TABLE `users` ADD `PHONE` varchar(20) NULL,
                    ADD `ADDRESS` nvarchar(1024) NULL,
                    ADD `CITY_AREA_ID` INT NULL;

ALTER TABLE `users` ADD `PERSONAL_DISCOUNT_AMOUNT` decimal(10,0) NOT NULL DEFAULT 0;

ALTER TABLE `discount` ADD `MAX_USAGE` int NOT NULL DEFAULT 10000,
                       ADD `USED` int NOT NULL default 0;


update `discount` set MAX_USAGE = (case WHEN IS_ONE_TIME then 1 else 10000 end);

update `discount` set USED = (case WHEN IS_ONE_TIME_USED then 1 else 0 end); 
 
ALTER TABLE `registration_code` ADD `INITIAL_PERSONAL_DISCOUNT` decimal(10) NOT NULL DEFAULT 0;
 CREATE TABLE `order_custom_wash_items` (
  `ORDER_NUMBER` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `WASH_TYPE` tinyint(4) NOT NULL, 
  `NAME` varchar(100) NOT NULL,
  `COUNT` int(11) NOT NULL,
  `CREATE_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IS_ACTUAL` tinyint(1) NOT NULL DEFAULT '0'
); 

ALTER TABLE `orders` ADD ACTUAL_PRICE_WITH_DISCOUNT decimal(9,0) NULL;
ALTER TABLE `orders` MODIFY WEIGHT decimal(6,1) NULL;
ALTER TABLE `orders` MODIFY ACTUAL_WEIGHT decimal(6,1) NULL;

ALTER TABLE `wash_item` ADD WASH_TYPE smallint(3) NOT NULL default 0;
INSERT INTO `city_area`(`CITY_ID`, `NAME`) 
                VALUES (2,'Vitacura');

INSERT INTO `city_area`(`CITY_ID`, `NAME`) 
                VALUES (2,'Lo Barnechea');

INSERT INTO `city_area`(`CITY_ID`, `NAME`) 
                VALUES (2,'Santiago Centro');

ALTER TABLE `orders` ADD WASH_DETERGENT SMALLINT(3) DEFAULT '0';


CREATE TABLE `blockeddate`(
   ID int primary key NOT NULL AUTO_INCREMENT,
   `DATE` DATETIME NOT NULL,
   DESCRIPTION varchar(256) NOT NULL DEFAULT ""
);


CREATE TABLE `blockedday`(
   ID int primary key NOT NULL AUTO_INCREMENT,
   DAY int NOT NULL,
   MONTH int NOT NULL,
   DESCRIPTION varchar(256) NOT NULL DEFAULT ""
);


ALTER TABLE `discount` ADD IS_ONE_TIME_PER_EMAIL BOOL NOT NULL DEFAULT 0;


ALTER TABLE `discount`
  DROP `IS_ONE_TIME`,
  DROP `IS_ONE_TIME_USED`;


DELETE from `city_area` where `NAME` = 'Santiago Centro';