--USE washita_2016;

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
