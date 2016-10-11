
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

