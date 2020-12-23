TRUNCATE TABLE `#__multipolls_type_questions`;

INSERT INTO `#__multipolls_type_questions` (`id`, `type`) VALUES
(1, 'Один вариант'),
(2, 'Несколько вариантов'),
(3, 'Цифра по шкале'),
(4, 'Ввод текста'),
(5, 'Цифра по шкале и ввод текста'),
(6, 'Один вариант либо свой'),
(7, 'Да или Нет'),
(8, 'Несколько вариантов и свой');

CREATE TABLE IF NOT EXISTS `#__multipolls_select_range` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_question` int(10) NOT NULL,
  `max_range` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_cb_own_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `answers` text DEFAULT NULL,
  `own_answer` text,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_priority_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `id_answer` int(10) unsigned NOT NULL,
  `value` int(10) DEFAULT NULL,  
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;