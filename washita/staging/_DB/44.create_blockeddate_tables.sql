

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




