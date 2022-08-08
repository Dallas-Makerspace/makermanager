--tblclients
--tblcustomfieldsvalues
--tblhosting
--tblhostingaddons

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- TBLCLIENTS
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `tblclients` (
  `id` int(11) NOT NULL,
  `uuid` varchar(255) CHARACTER SET ucs2 NOT NULL,
  `firstname` text CHARACTER SET ucs2 NOT NULL,
  `lastname` text CHARACTER SET ucs2 NOT NULL,
  `companyname` text CHARACTER SET ucs2 NOT NULL,
  `email` text CHARACTER SET ucs2 NOT NULL,
  `address1` text CHARACTER SET ucs2 NOT NULL,
  `address2` text CHARACTER SET ucs2 NOT NULL,
  `city` text CHARACTER SET ucs2 NOT NULL,
  `state` text CHARACTER SET ucs2 NOT NULL,
  `postcode` text CHARACTER SET ucs2 NOT NULL,
  `country` text CHARACTER SET ucs2 NOT NULL,
  `phonenumber` text CHARACTER SET ucs2 NOT NULL,
  `tax_id` varchar(128) NOT NULL DEFAULT '',
  `password` text CHARACTER SET ucs2 NOT NULL,
  `authmodule` text CHARACTER SET ucs2 NOT NULL,
  `authdata` text CHARACTER SET ucs2 NOT NULL,
  `currency` int(11) NOT NULL,
  `defaultgateway` text CHARACTER SET ucs2 NOT NULL,
  `credit` decimal(16,2) NOT NULL,
  `taxexempt` tinyint(4) NOT NULL,
  `latefeeoveride` tinyint(4) NOT NULL,
  `overideduenotices` tinyint(4) NOT NULL,
  `separateinvoices` tinyint(4) NOT NULL,
  `disableautocc` tinyint(4) NOT NULL,
  `datecreated` date NOT NULL,
  `notes` text CHARACTER SET ucs2 NOT NULL,
  `billingcid` int(11) NOT NULL,
  `securityqid` int(11) NOT NULL,
  `securityqans` text CHARACTER SET ucs2 NOT NULL,
  `groupid` int(11) NOT NULL,
  `cardtype` varchar(255) CHARACTER SET ucs2 NOT NULL,
  `cardlastfour` text CHARACTER SET ucs2 NOT NULL,
  `cardnum` blob NOT NULL,
  `startdate` blob NOT NULL,
  `expdate` blob NOT NULL,
  `issuenumber` blob NOT NULL,
  `bankname` text CHARACTER SET ucs2 NOT NULL,
  `banktype` text CHARACTER SET ucs2 NOT NULL,
  `bankcode` blob NOT NULL,
  `bankacct` blob NOT NULL,
  `gatewayid` text CHARACTER SET ucs2 NOT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `ip` text CHARACTER SET ucs2 NOT NULL,
  `host` text CHARACTER SET ucs2 NOT NULL,
  `status` varchar(8) CHARACTER SET ucs2 NOT NULL,
  `language` text CHARACTER SET ucs2 NOT NULL,
  `pwresetkey` text CHARACTER SET ucs2 NOT NULL,
  `emailoptout` int(11) NOT NULL,
  `marketing_emails_opt_in` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `overrideautoclose` int(11) NOT NULL,
  `allow_sso` tinyint(4) NOT NULL,
  `email_verified` tinyint(4) NOT NULL,
  `email_preferences` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `pwresetexpiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `tblclients`
(`id`, `uuid`,                              `firstname`, `lastname`, `companyname`, `email`,            `address1`,    `address2`, `city`, `state`, `postcode`, `country`, `phonenumber`, `password`, `authmodule`, `authdata`, `currency`, `defaultgateway`, `credit`, `taxexempt`, `latefeeoveride`, `overideduenotices`, `separateinvoices`, `disableautocc`, `datecreated`, `notes`, `billingcid`, `securityqid`, `securityqans`, `groupid`, `cardtype`, `cardlastfour`, `cardnum`, `startdate`, `expdate`, `issuenumber`, `bankname`, `banktype`, `bankcode`, `bankacct`, `gatewayid`, `lastlogin`,            `ip`,           `host`,          `status`, `language`, `pwresetkey`, `emailoptout`, `marketing_emails_opt_in`, `overrideautoclose`, `allow_sso`, `email_verified`, `email_preferences`, `created_at`,          `updated_at`,          `pwresetexpiry`)
VALUES
(1, '3ab1e9c2-6283-4ab2-9f9d-f20c19e731f4', 'User',      '1',        '',            'user1@dms.local', '123 Main St', 'APT 1',     'Carrollton', 'TX', '75006', 'US',      '1234567890',  '',          '',          '',                 1,  '',               '0.00',             0,                0,                   0,                  0,              0,  '2012-09-25',  '',                0,              0, '',                     0, '',          '',                   0,           0,          0,            0,   '',         '',         '',         '',         '',          '2022-07-25 16:25:50', '47.184.40.185', '47.184.40.185', 'Active', '',         '',                     0,                          0,                  0,            1,                1,                NULL, '2022-08-02 01:00:00', '2022-08-25 16:25:50', '0000-00-00 00:00:00'),
(2, '3ab1e9c2-6283-4ab2-9f9d-f20c19e731f4', 'User',      '2',        '',            'user2@dms.local', '123 Main St', 'APT 2',     'Carrollton', 'TX', '75006', 'US',      '1234567890',  '',          '',          '',                 1,  '',               '0.00',             0,                0,                   0,                  0,              0,  '2012-09-25',  '',                0,              0, '',                     0, '',          '',                   0,           0,          0,            0,   '',         '',         '',         '',         '',          '2022-07-25 16:25:50', '47.184.40.185', '47.184.40.185', 'Active', '',         '',                     0,                          0,                  0,            1,                1,                NULL, '2022-08-02 01:00:00', '2022-08-25 16:25:50', '0000-00-00 00:00:00'),
(3, '3ab1e9c2-6283-4ab2-9f9d-f20c19e731f4', 'User',      '3',        '',            'user3@dms.local', '123 Main St', 'APT 3',     'Carrollton', 'TX', '75006', 'US',      '1234567890',  '',          '',          '',                 1,  '',               '0.00',             0,                0,                   0,                  0,              0,  '2012-09-25',  '',                0,              0, '',                     0, '',          '',                   0,           0,          0,            0,   '',         '',         '',         '',         '',          '2022-07-25 16:25:50', '47.184.40.185', '47.184.40.185', 'Active', '',         '',                     0,                          0,                  0,            1,                1,                NULL, '2022-08-02 01:00:00', '2022-08-25 16:25:50', '0000-00-00 00:00:00'),
(4, '3ab1e9c2-6283-4ab2-9f9d-f20c19e731f4', 'User',      '4',        '',            'user4@dms.local', '123 Main St', 'APT 4',     'Carrollton', 'TX', '75006', 'US',      '1234567890',  '',          '',          '',                 1,  '',               '0.00',             0,                0,                   0,                  0,              0,  '2012-09-25',  '',                0,              0, '',                     0, '',          '',                   0,           0,          0,            0,   '',         '',         '',         '',         '',          '2022-07-25 16:25:50', '47.184.40.185', '47.184.40.185', 'Active', '',         '',                     0,                          0,                  0,            1,                1,                NULL, '2022-08-02 01:00:00', '2022-08-25 16:25:50', '0000-00-00 00:00:00'),
(5, '3ab1e9c2-6283-4ab2-9f9d-f20c19e731f4', 'User',      '5',        '',            'user5@dms.local', '123 Main St', 'APT 5',     'Carrollton', 'TX', '75006', 'US',      '1234567890',  '',          '',          '',                 1,  '',               '0.00',             0,                0,                   0,                  0,              0,  '2012-09-25',  '',                0,              0, '',                     0, '',          '',                   0,           0,          0,            0,   '',         '',         '',         '',         '',          '2022-07-25 16:25:50', '47.184.40.185', '47.184.40.185', 'Active', '',         '',                     0,                          0,                  0,            1,                1,                NULL, '2022-08-02 01:00:00', '2022-08-25 16:25:50', '0000-00-00 00:00:00');

ALTER TABLE `tblclients`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tblclients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;



-- TBLCUSTOMFIELDSVALUES
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `tblcustomfieldsvalues` (
  `id` int(10) UNSIGNED NOT NULL,
  `fieldid` int(11) NOT NULL, -- 2 = username
  `relid` int(11) NOT NULL, -- tblclients.id
  `value` text CHARACTER SET ucs2 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `tblcustomfieldsvalues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IX_fieldId_relId` (`fieldid`,`relid`);

ALTER TABLE `tblcustomfieldsvalues`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `tblcustomfieldsvalues` (`fieldid`, `relid`, `value`, `created_at`, `updated_at`) VALUES
(2, 1, 'user1', '2022-08-02 14:05:22', '2022-08-02 14:05:22' ),
(2, 2, 'user2', '2022-08-02 14:05:22', '2022-08-02 14:05:22' ),
(2, 3, 'user3', '2022-08-02 14:05:22', '2022-08-02 14:05:22' ),
(2, 4, 'user4', '2022-08-02 14:05:22', '2022-08-02 14:05:22' ),
(2, 5, 'disableduser', '2022-08-02 14:05:22', '2022-08-02 14:05:22' );


COMMIT;



-- TBLHOSTING
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `tblhosting` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `packageid` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `regdate` date NOT NULL,
  `domain` text CHARACTER SET ucs2 NOT NULL,
  `paymentmethod` text CHARACTER SET ucs2 NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `firstpaymentamount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `billingcycle` text CHARACTER SET ucs2 NOT NULL,
  `nextduedate` date DEFAULT NULL,
  `nextinvoicedate` date NOT NULL,
  `termination_date` date NOT NULL,
  `completed_date` date NOT NULL DEFAULT '0000-00-00',
  `domainstatus` enum('Pending','Active','Suspended','Terminated','Cancelled','Fraud','Completed') NOT NULL DEFAULT 'Pending',
  `username` text CHARACTER SET ucs2 NOT NULL,
  `password` text CHARACTER SET ucs2 NOT NULL,
  `notes` text CHARACTER SET ucs2 NOT NULL,
  `subscriptionid` text CHARACTER SET ucs2 NOT NULL,
  `promoid` int(11) NOT NULL,
  `promocount` int(10) DEFAULT 0,
  `suspendreason` text CHARACTER SET ucs2 NOT NULL,
  `overideautosuspend` tinyint(4) NOT NULL,
  `overidesuspenduntil` date NOT NULL,
  `dedicatedip` text CHARACTER SET ucs2 NOT NULL,
  `assignedips` text CHARACTER SET ucs2 NOT NULL,
  `ns1` text CHARACTER SET ucs2 NOT NULL,
  `ns2` text CHARACTER SET ucs2 NOT NULL,
  `diskusage` int(11) NOT NULL,
  `disklimit` int(11) NOT NULL,
  `bwusage` int(11) NOT NULL,
  `bwlimit` int(11) NOT NULL,
  `lastupdate` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- userid = mm.whmcs_user_id == tblclients.id
INSERT INTO `tblhosting`
(`id`,`userid`, `orderid`, `packageid`, `server`, `regdate`, `domain`, `paymentmethod`, `qty`, `firstpaymentamount`, `amount`, `billingcycle`, `nextduedate`, `nextinvoicedate`, `termination_date`, `completed_date`, `domainstatus`, `username`, `password`, `notes`, `subscriptionid`, `promoid`, `promocount`, `suspendreason`, `overideautosuspend`, `overidesuspenduntil`, `dedicatedip`, `assignedips`, `ns1`, `ns2`, `diskusage`, `disklimit`, `bwusage`, `bwlimit`, `lastupdate`, `created_at`, `updated_at`) VALUES
(1, 1, 10001, 23, 0, '2022-06-18', '', 'authorizecim', 1, '60.00', '60.00', 'Monthly', '2022-07-18', '2022-08-18', '2022-07-23', '0000-00-00', 'Active', '', '', '', '', 0, 0, '', 0, '0000-00-00', '', '', '', '', 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(2, 2, 10002, 23, 0, '2022-06-18', '', 'authorizecim', 1, '60.00', '60.00', 'Monthly', '2022-08-18', '2022-08-18', '0000-00-00', '0000-00-00', 'Active', '', '', '', '', 0, 0, '', 0, '0000-00-00', '', '', '', '', 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(3, 3, 10003, 23, 0, '2022-06-18', '', 'authorizecim', 1, '60.00', '60.00', 'Monthly', '2022-07-18', '2022-08-18', '2022-07-23', '0000-00-00', 'Active', '', '', '', '', 0, 0, '', 0, '0000-00-00', '', '', '', '', 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(4, 4, 10004, 23, 0, '2022-06-18', '', 'authorizecim', 1, '60.00', '60.00', 'Monthly', '2022-08-18', '2022-08-18', '0000-00-00', '0000-00-00', 'Active', '', '', '', '', 0, 0, '', 0, '0000-00-00', '', '', '', '', 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(5, 5, 10005, 23, 0, '2022-06-19', '', 'authorizecim', 1, '40.00', '40.00', 'Monthly', '2022-07-19', '2022-08-19', '2022-07-19', '0000-00-00', 'Cancelled', '', '', '', '', 74, 3, '', 0, '0000-00-00', '', '', '', '', 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2022-07-19 03:01:53');

ALTER TABLE `tblhosting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`(8));

ALTER TABLE `tblhosting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


COMMIT;




-- TBLHOSTINGADDONS
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `tblhostingaddons` (
  `id` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `hostingid` int(11) NOT NULL,
  `addonid` int(11) NOT NULL,
  `userid` int(10) NOT NULL DEFAULT 0,
  `server` int(10) NOT NULL DEFAULT 0,
  `name` text CHARACTER SET ucs2 NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `firstpaymentamount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `setupfee` decimal(16,2) NOT NULL DEFAULT 0.00,
  `recurring` decimal(16,2) NOT NULL DEFAULT 0.00,
  `billingcycle` text CHARACTER SET ucs2 NOT NULL,
  `tax` text CHARACTER SET ucs2 NOT NULL,
  `status` enum('Pending','Active','Suspended','Terminated','Cancelled','Fraud','Completed') NOT NULL DEFAULT 'Pending',
  `regdate` date NOT NULL,
  `nextduedate` date DEFAULT NULL,
  `nextinvoicedate` date NOT NULL,
  `termination_date` date NOT NULL,
  `proratadate` date NOT NULL DEFAULT '0000-00-00',
  `paymentmethod` text CHARACTER SET ucs2 NOT NULL,
  `notes` text CHARACTER SET ucs2 NOT NULL,
  `subscriptionid` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `tblhostingaddons`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tblhostingaddons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `tblhostingaddons` (`orderid`, `hostingid`, `addonid`, `userid`, `server`, `name`, `qty`, `firstpaymentamount`, `setupfee`, `recurring`, `billingcycle`, `tax`, `status`, `regdate`, `nextduedate`, `nextinvoicedate`, `termination_date`, `proratadate`, `paymentmethod`, `notes`, `subscriptionid`, `created_at`, `updated_at`) VALUES
(10003, 3, 2, 3, 0, '', 1, '0.00', '0.00', '10.00', 'Monthly', '', 'Active', '2017-08-31', '2018-06-01', '2018-07-01', '2018-05-30', '0000-00-00', 'authorizecim', '', '', NULL, '2018-05-30 03:00:24'),
(10004, 4, 2, 4, 0, '', 1, '0.00', '0.00', '10.00', 'Monthly', '', 'Active', '2017-09-02', '2022-09-02', '2022-09-02', '0000-00-00', '0000-00-00', 'authorizecim', '', '', NULL, '2022-08-01 03:00:54'),
(10005, 5, 2, 5, 0, '', 1, '0.00', '0.00', '10.00', 'Monthly', '', 'Terminated', '2017-09-02', '2017-10-02', '2017-11-02', '0000-00-00', '0000-00-00', 'authorizecim', '', '', NULL, NULL);


COMMIT;
