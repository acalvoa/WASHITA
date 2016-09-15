
create table city(
   ID int primary key NOT NULL AUTO_INCREMENT,
   NAME varchar(124) NOT NULL,   
   CREATE_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO `city` (`NAME`) VALUES ('Viña');
INSERT INTO `city` (`NAME`) VALUES ('Santiago');
