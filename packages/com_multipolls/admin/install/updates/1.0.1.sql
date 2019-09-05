CREATE TABLE IF NOT EXISTS `#__multipolls_yn_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `id_answer` int(10) unsigned NOT NULL,
  `value` char(10) DEFAULT NULL,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`),
  KEY `id_answer` (`id_answer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT IGNORE INTO `#__multipolls_type_questions`(`id`, `type`) VALUES (7, 'yes/no')