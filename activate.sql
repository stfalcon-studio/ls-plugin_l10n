ALTER TABLE `prefix_user` ADD `user_lang` VARCHAR(16) NOT NULL DEFAULT 'russian' AFTER `user_password`;

ALTER TABLE `prefix_topic` ADD `topic_lang` VARCHAR(16) NOT NULL DEFAULT 'russian' AFTER `topic_type`;
ALTER TABLE `prefix_topic` ADD `topic_original_id` INT(11) NULL AFTER `topic_lang`;

ALTER TABLE `prefix_topic` ADD INDEX (`topic_original_id`);

CREATE TABLE IF NOT EXISTS `prefix_blog_l10n` (
        `blog_id` INT(11) UNSIGNED NOT NULL,
        `blog_title_l10n` VARCHAR(200) NOT NULL,
        `blog_description_l10n` TEXT NOT NULL,
        `blog_url_l10n` VARCHAR(200) NOT NULL,
        `blog_lang` VARCHAR(16) NOT NULL,
        UNIQUE KEY `blog_id` (`blog_id`,`blog_lang`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_blog_l10n`
        ADD CONSTRAINT `prefix_blog_l10n_fk` FOREIGN KEY (`blog_id`) REFERENCES `prefix_blog` (`blog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO prefix_blog_l10n
        (SELECT
                blog_id,
                blog_title,
                blog_description,
                blog_url,
                'russian'
        FROM
                `prefix_blog`
        WHERE 1);