-- create table to store currency conversion data
CREATE TABLE IF NOT EXISTS `currencyrates` (

  `id` int(11) NOT NULL auto_increment,
  `currencyType` varchar(3) NOT NULL default '', -- assumed ISO 4217 for 3 letter currency codes, verify with 3rd party service
  `currencyRate`  DECIMAL(10,7) NOT NULL, -- verify max limit from 3rd party service, changed from @joshlawton's (7,4)
   PRIMARY KEY  (`id`)

);
