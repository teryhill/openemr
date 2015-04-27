CREATE TABLE IF NOT EXISTS `patient_tracker` (
  `id`                 bigint(20)   NOT NULL auto_increment,
  `date`               datetime     NOT NULL,
  `apptdate`           date         NOT NULL,
  `appttime`           time         NOT NULL,
  `eid`                bigint(20)   NOT NULL,
  `pid`                bigint(20)   NOT NULL,
  `user`               varchar(255) NOT NULL,
  `enc_id`             bigint(20)   NOT NULL,
  `endtime`            time         NOT NULL,
  `laststatus`         varchar(31)  NOT NULL,
  `lastroom`           varchar(20)  NOT NULL,
  `lastseq`            varchar(4)   NOT NULL,
   KEY  (`id`,`enc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `patient_tracker_element` (
  `pt_traker_id`       bigint(20)   NOT NULL,
  `start_datetime`     datetime     NOT NULL,
  `room`               varchar(20)  NOT NULL,
  `status`             varchar(31)  NOT NULL,
  `seq`                varchar(4)   NOT NULL,
  `user`               varchar(255) NOT NULL,
  KEY  (`pt_traker_id`)
) ENGINE=MyISAM;