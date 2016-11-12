
create table wash_item(
   ID int primary key NOT NULL AUTO_INCREMENT,
   NAME nvarchar(256) NOT NULL,
   ITEM_WEIGHT decimal(3,2) NOT NULL COMMENT 'In kilos', 
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
