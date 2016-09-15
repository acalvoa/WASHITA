
--USE washita_2016;

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
