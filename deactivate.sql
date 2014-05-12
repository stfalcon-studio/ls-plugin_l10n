ALTER TABLE `prefix_user` DROP `user_lang`;
ALTER TABLE `prefix_user` DROP `user_role`;

DELETE FROM `prefix_topic` WHERE `topic_original_id` IS NOT NULL;
ALTER TABLE `prefix_topic` DROP `topic_lang`;
ALTER TABLE `prefix_topic` DROP INDEX `topic_original_id`;
ALTER TABLE `prefix_topic` DROP `topic_original_id`;

DROP TABLE IF EXISTS `prefix_blog_l10n`;