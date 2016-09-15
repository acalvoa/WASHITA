
ALTER TABLE `discount` ADD `MAX_USAGE` int NOT NULL DEFAULT 10000,
                       ADD `USED` int NOT NULL default 0;


update `discount` set MAX_USAGE = (case WHEN IS_ONE_TIME then 1 else 10000 end);

update `discount` set USED = (case WHEN IS_ONE_TIME_USED then 1 else 0 end); 
 