CREATE TABLE IF NOT EXISTS `#__multipolls_polls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,    
  `published` tinyint(1) NOT NULL,    
  `publish_up` datetime NOT NULL,
  `publish_down` datetime NOT NULL,  
  `created` datetime NOT NULL,      
  PRIMARY KEY (`id`) 
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__multipolls_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_poll` int(10) unsigned NOT NULL, 
  `id_type` int(10) NOT NULL, 
  `published` tinyint(1) NOT NULL,
  `publish_up` datetime NOT NULL,
  `publish_down` datetime NOT NULL,
  `created` datetime NOT NULL,
  `ordering` mediumint(8) unsigned NOT NULL,
  `img_url` TEXT NOT NULL, 
  PRIMARY KEY (`id`),
  KEY `id_poll` (`id_poll`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL, 
  `published` tinyint(1) NOT NULL,
  `publish_up` datetime NOT NULL,
  `publish_down` datetime NOT NULL,
  `created` datetime NOT NULL,
  `ordering` mediumint(8) unsigned NOT NULL,   
  `img_url` TEXT NOT NULL,  
  PRIMARY KEY (`id`),
  KEY `id_poll` (`id_question`),  
  KEY `order` (`ordering`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__multipolls_langs`(
  `id_lang` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(32) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `published` int(11) NOT NULL,  
  PRIMARY KEY (`id_lang`)  
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_select_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `id_answer` int(10) unsigned NOT NULL,
  `value` int(10) DEFAULT NULL,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`),
  KEY `id_answer` (`id_answer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_text_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `id_answer` int(10) unsigned NOT NULL,
  `text` longtext,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`),
  KEY `id_answer` (`id_answer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_cb_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `answers` text DEFAULT NULL,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_radio_own_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `id_answer` varchar(50) DEFAULT NULL,
  `own_answer` text,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_radio_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) NOT NULL,
  `id_answer` int(10) unsigned NOT NULL,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_answer` (`id_answer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_type_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__multipolls_select_text_votes` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_question` int(10) unsigned NOT NULL,
  `id_answer` int(10) unsigned NOT NULL,
  `value` int(10) DEFAULT NULL,
  `text` longtext,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `date_voting` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  KEY `id_question` (`id_question`),
  KEY `id_answer` (`id_answer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

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

INSERT INTO `#__multipolls_type_questions` (`id`, `type`) VALUES
(1, 'Один вариант'),
(2, 'Несколько вариантов'),
(3, 'Цифра по шкале'),
(4, 'Ввод текста'),
(5, 'Цифра по шкале и ввод текста'),
(6, 'Один вариант либо свой'),
(7, 'Да или Нет'),
(8, 'Несколько вариантов и свой'),
(9, 'Выбор по приоритету');