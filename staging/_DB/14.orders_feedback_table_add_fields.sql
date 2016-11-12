
ALTER TABLE `order_feedback` ADD `RATING_EASINESS` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_IRONING` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_WASHING` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_RECOMMEND` tinyint NULL;
ALTER TABLE `order_feedback` ADD `RATING_PICKUP` tinyint NULL;

ALTER TABLE `order_feedback` MODIFY `RATING` decimal;


