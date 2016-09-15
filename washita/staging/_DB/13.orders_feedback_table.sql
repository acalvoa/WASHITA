
create table order_feedback(
   `ID` int primary key NOT NULL AUTO_INCREMENT,
   `RATING` tinyint COMMENT 'User rating',
   `TEXT` varchar(2000) COMMENT 'User feedback',
   `ORDER_NUMBER` nvarchar(32) NULL,
   `CREATE_DATE` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE `order_feedback` ADD `FEEDBACK_CODE` varchar(40) NULL;

ALTER TABLE `order_feedback` ADD INDEX `ORDER_FEEDBACK_ORDER_NUMBER_INDX` (`ORDER_NUMBER`);
