--
--  Comment Meta Language Constructs:
--
--  #IfNotTable
--    argument: table_name
--    behavior: if the table_name does not exist,  the block will be executed

--  #IfTable
--    argument: table_name
--    behavior: if the table_name does exist, the block will be executed

--  #IfMissingColumn
--    arguments: table_name colname
--    behavior:  if the colname in the table_name table does not exist,  the block will be executed

--  #IfNotColumnType
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a column colname with a data type equal to value, then the block will be executed

--  #IfNotRow
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a row where colname = value, the block will be executed.

--  #IfNotRow2D
--    arguments: table_name colname value colname2 value2
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2, the block will be executed.

--  #IfNotRow3D
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3, the block will be executed.

--  #IfNotRow4D
--    arguments: table_name colname value colname2 value2 colname3 value3 colname4 value4
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3 AND colname4 = value4, the block will be executed.

--  #IfNotRow2Dx2
--    desc:      This is a very specialized function to allow adding items to the list_options table to avoid both redundant option_id and title in each element.
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  The block will be executed if both statements below are true:
--               1) The table table_name does not have a row where colname = value AND colname2 = value2.
--               2) The table table_name does not have a row where colname = value AND colname3 = value3.

--  #IfNotIndex
--    desc:      This function will allow adding of indexes/keys.
--    arguments: table_name colname
--    behavior:  If the index does not exist, it will be created

--  #EndIf
--    all blocks are terminated with and #EndIf statement.
#IfNotTable patient_tracker
CREATE TABLE `patient_tracker` (
  `id`                 bigint(20)   NOT NULL auto_increment,
  `date`               datetime     NOT NULL,
  `pid`                bigint(20)   NOT NULL,
  `user`               varchar(255) NOT NULL,
  `groupname`          varchar(255) NOT NULL,
  `authorized`         tinyint(4)   NOT NULL,
  `activity`           tinyint(4)   NOT NULL,
  `roomnumber`         varchar(15)  NOT NULL,
  `roomuser`           varchar(255) NOT NULL,
  `status`             varchar(2)   NOT NULL,
  `encnum`             bigint(20)   NOT NULL,
  `origappt`           time         NOT NULL,
  `provider`           varchar(30)  NOT NULL,
  `facility_id`        bigint(20)   NOT NULL,
  `arrivedatetime`     datetime     NOT NULL,
  `arrivedtestop`      datetime     NOT NULL,
  `arriveuser`         varchar(255) NOT NULL,
  `inroomdatetime`     datetime     NOT NULL,
  `inroomdtestop`      datetime     NOT NULL,
  `inroomuser`         varchar(255) NOT NULL,
  `nurseseendatetime`  datetime     NOT NULL,
  `nurseseendtestop`   datetime     NOT NULL,
  `nurseseenuser`      varchar(255) NOT NULL,
  `drseendatetime`     datetime     NOT NULL,
  `drseendtestop`      datetime     NOT NULL,
  `drseenuser`         varchar(255) NOT NULL,
  `techseendatetime`   datetime     NOT NULL,
  `techseendtestop`    datetime     NOT NULL,
  `techseenuser`       varchar(255) NOT NULL,
  `labseendatetime`    datetime     NOT NULL,
  `labseendtestop`     datetime     NOT NULL,
  `labseenuser`        varchar(255) NOT NULL,
  `proseendatetime`    datetime     NOT NULL,
  `proseendtestop`     datetime     NOT NULL,
  `proseenuser`        varchar(255) NOT NULL,
  `radseendatetime`    datetime     NOT NULL,
  `radseendtestop`     datetime     NOT NULL,
  `radseenuser`        varchar(255) NOT NULL,
  `ptseendatetime`     datetime     NOT NULL,
  `ptseendtestop`      datetime     NOT NULL,
  `ptseenuser`         varchar(255) NOT NULL,
  `checkoutdatetime`   datetime     NOT NULL,
  `checkoutdtestop`    datetime     NOT NULL,
  `checkoutuser`       varchar(255) NOT NULL,
  `def1datetime`       datetime     NOT NULL,
  `def1dtedtop`        datetime     NOT NULL,
  `def1user`           varchar(255) NOT NULL,
  `def2datetime`       datetime     NOT NULL,
  `def2dtestop`        datetime     NOT NULL,
  `def2user`           varchar(255) NOT NULL,
  `def3datetime`       datetime     NOT NULL,
  `def3dtestop`        datetime     NOT NULL,
  `def3user`           varchar(255) NOT NULL,
  `def4datetime`       datetime     NOT NULL,
  `def4dtestop`        datetime     NOT NULL,
  `def4user`           varchar(255) NOT NULL,
  `def5datetime`       datetime     NOT NULL,
  `def5dtestop`        datetime     NOT NULL,
  `def5user`           varchar(255) NOT NULL,
  `def6datetime`       datetime     NOT NULL,
  `def6dtestop`        datetime     NOT NULL,
  `def6user`           varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);
#EndIf