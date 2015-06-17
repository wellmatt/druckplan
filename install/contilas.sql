CREATE TABLE `abonnements` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`abouser`  int(11) NOT NULL ,
`module`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`objectid`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `add_table` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`application`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`code1`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`code2`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `address` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`active`  tinyint(4) NOT NULL ,
`businesscontact`  int(11) NOT NULL ,
`name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`name2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`city`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`country`  int(11) NOT NULL ,
`fax`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`phone`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`mobile`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`shoprel`  tinyint(2) NOT NULL ,
`is_default`  tinyint(2) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `article` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  int(11) NOT NULL DEFAULT 1 ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
`description`  text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
`number`  varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
`tradegroup`  int(11) NOT NULL ,
`shoprel`  int(11) NOT NULL DEFAULT 0 ,
`crtuser`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`uptuser`  int(11) NOT NULL DEFAULT 0 ,
`uptdate`  int(11) NOT NULL DEFAULT 0 ,
`picture`  varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
`tax`  float NOT NULL ,
`minorder`  int(11) NOT NULL ,
`maxorder`  int(11) NOT NULL ,
`orderunit`  int(11) NOT NULL ,
`orderunitweight`  float NOT NULL ,
`shop_customer_rel`  int(11) NOT NULL ,
`shop_customer_id`  int(11) NOT NULL ,
`isworkhourart`  tinyint(2) NOT NULL DEFAULT 0 ,
`show_shop_price`  tinyint(2) NOT NULL DEFAULT 1 ,
`shop_needs_upload`  tinyint(2) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `article_costs` (
`sep_articleid`  int(11) NOT NULL ,
`sep_min`  int(11) NOT NULL ,
`sep_max`  int(11) NOT NULL ,
`sep_price`  float NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `article_orderamounts` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`article_id`  int(11) NOT NULL ,
`amount`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `article_pictures` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`url`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`articleid`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `article_qualified_users` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`article`  int(11) NOT NULL ,
`user`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `article_seperation` (
`sep_articleid`  int(11) NOT NULL ,
`sep_min`  int(11) NOT NULL ,
`sep_max`  int(11) NOT NULL ,
`sep_price`  float NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `association` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`module1`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`objectid1`  int(11) NOT NULL ,
`module2`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`objectid2`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `attachments` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
`state`  tinyint(4) NOT NULL ,
`module`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`objectid`  int(11) NOT NULL ,
`filename`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`orig_filename`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `attributes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`state`  tinyint(2) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`module`  int(11) NOT NULL ,
`objectid`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`enable_customer`  tinyint(2) NOT NULL ,
`enable_contacts`  tinyint(2) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `attributes_items` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL ,
`attribute_id`  smallint(6) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`input`  tinyint(1) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `builder` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`description`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`html`  mediumblob NULL ,
`build`  mediumblob NULL ,
`options`  mediumblob NULL ,
`con`  mediumblob NULL ,
`recipients`  mediumblob NULL ,
`added`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`views`  int(11) NULL DEFAULT NULL ,
`submits`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `bulkletter` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`text`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`crt_user`  int(11) NOT NULL ,
`crt_date`  int(11) NOT NULL ,
`upd_user`  int(11) NOT NULL ,
`upd_date`  int(11) NOT NULL ,
`doc_print_created`  tinyint(2) NOT NULL ,
`doc_email_created`  tinyint(2) NOT NULL ,
`customer_filter`  tinyint(2) NOT NULL DEFAULT 4 ,
`customer_attrib`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `businesscontact` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`active`  tinyint(4) NOT NULL DEFAULT 1 ,
`commissionpartner`  tinyint(4) NOT NULL ,
`customer`  tinyint(4) NOT NULL DEFAULT 1 ,
`supplier`  tinyint(4) NOT NULL ,
`client`  int(11) NOT NULL DEFAULT 1 ,
`matchcode`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`name2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`city`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`country`  int(11) NOT NULL ,
`phone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`fax`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`email`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`web`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`language`  int(11) NOT NULL DEFAULT 22 ,
`payment_terms`  int(11) NOT NULL DEFAULT 0 ,
`discount`  float NOT NULL DEFAULT 0 ,
`lector_id`  int(11) NOT NULL ,
`shop_login`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`shop_pass`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`login_expire`  int(11) NOT NULL DEFAULT 0 ,
`ticket_enabled`  tinyint(2) NOT NULL DEFAULT 0 ,
`personalization_enabled`  tinyint(2) NOT NULL DEFAULT 0 ,
`branche`  tinyint(4) NOT NULL ,
`type`  tinyint(4) NOT NULL ,
`produkte`  tinyint(4) NOT NULL ,
`bedarf`  smallint(4) NOT NULL ,
`priv_name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_name2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_address1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_address2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_city`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_country`  int(11) NOT NULL ,
`priv_phone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_fax`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_email`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_name2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_address1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_address2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_city`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_country`  int(11) NOT NULL ,
`alt_phone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_fax`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_email`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`cust_number`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`number_at_customer`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`enabled_article`  tinyint(1) NOT NULL ,
`debitor_number`  int(11) NOT NULL ,
`kreditor_number`  int(11) NOT NULL ,
`iban`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`bic`  varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`position_titles`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`notifymailadr`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`supervisor`  int(11) NOT NULL ,
`tourmarker`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`notes`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`),
FULLTEXT INDEX `name1` (`name1`) ,
FULLTEXT INDEX `name2` (`name2`) ,
FULLTEXT INDEX `matchcode` (`matchcode`) 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `businesscontact_attributes` (
`businesscontact_id`  int(11) NOT NULL ,
`attribute_id`  int(11) NOT NULL ,
`item_id`  int(11) NOT NULL ,
`value`  tinyint(2) NOT NULL ,
`inputvalue`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `chat` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`state`  tinyint(2) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`from_id`  int(11) NOT NULL ,
`to_id`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `chromaticities` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`colors_front`  smallint(6) NOT NULL ,
`colors_back`  smallint(6) NOT NULL ,
`reverse_printing`  tinyint(1) NOT NULL ,
`markup`  float NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `clients` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`active`  tinyint(1) NOT NULL DEFAULT 1 ,
`client_status`  tinyint(4) NOT NULL DEFAULT 1 ,
`client_name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_street1`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_street2`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_street3`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_postcode`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_city`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_phone`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_fax`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_email`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_website`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bank_name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bank_blz`  varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bank_kto`  varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bank_iban`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bank_bic`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_gericht`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_steuernummer`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_ustid`  varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_country`  int(11) NOT NULL ,
`client_currency`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_decimal`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_thousand`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_taxes`  float NOT NULL DEFAULT 19 ,
`client_margin`  float NOT NULL DEFAULT 0 ,
`number_format_order`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'VO-YY-MM-XXXX' ,
`number_counter_order`  int(11) NOT NULL ,
`number_format_offer`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AN-YY-MM-XXXX' ,
`number_counter_offer`  int(11) NOT NULL ,
`number_format_offerconfirm`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AB-YY-MM-XXXX' ,
`number_counter_offerconfirm`  int(11) NOT NULL ,
`number_format_delivery`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'LS-YY-MM-XXXX' ,
`number_counter_delivery`  int(11) NOT NULL ,
`number_format_paper_order`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'PB-YY-MM-XXXX' ,
`number_counter_paper_order`  int(11) NOT NULL ,
`number_format_invoice`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'RE-YY-MM-XXXX' ,
`number_counter_invoice`  int(11) NOT NULL ,
`number_format_revert`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'GS-YY-MM-XXXX' ,
`number_counter_revert`  int(11) NOT NULL ,
`number_format_warning`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'MA-YY-MM-XXXX' ,
`number_counter_warning`  int(11) NOT NULL ,
`number_format_work`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'DR-YY-MM-XXXX' ,
`number_counter_work`  int(11) NOT NULL ,
`number_counter_ticket`  int(11) NOT NULL ,
`ticketnumberreset`  int(11) NOT NULL ,
`number_counter_debitor`  int(11) NOT NULL ,
`number_counter_creditor`  int(11) NOT NULL ,
`number_counter_customer`  int(11) NOT NULL ,
`client_bank2`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bic2`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_iban2`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bank3`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_bic3`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_iban3`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `collectiveinvoice` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(4) NOT NULL ,
`title`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`number`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`deliverycosts`  float NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'intern' ,
`client`  int(11) NOT NULL ,
`businesscontact`  int(11) NOT NULL ,
`deliveryterm`  int(11) NOT NULL ,
`paymentterm`  int(11) NOT NULL ,
`deliveryaddress`  int(11) NOT NULL ,
`invoiceaddress`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
`uptdate`  int(11) NOT NULL ,
`uptuser`  int(11) NOT NULL ,
`intent`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`intern_contactperson`  int(11) NOT NULL ,
`cust_message`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`cust_sign`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`custContactperson`  int(11) NOT NULL ,
`needs_planning`  tinyint(1) NOT NULL DEFAULT 0 ,
`deliverydate`  int(11) NOT NULL DEFAULT 0 ,
`ext_comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `collectiveinvoice_orderposition` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(4) NOT NULL ,
`quantity`  float(11,2) NOT NULL ,
`price`  float NOT NULL ,
`tax`  int(11) NOT NULL DEFAULT 19 ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`collectiveinvoice`  int(11) NOT NULL ,
`type`  int(11) NOT NULL ,
`inv_rel`  int(11) NOT NULL DEFAULT 1 ,
`object_id`  int(11) NOT NULL COMMENT 'Auftrag o. Artikel ID' ,
`rev_rel`  int(11) NOT NULL DEFAULT 0 ,
`file_attach`  int(11) NOT NULL DEFAULT 0 ,
`perso_order`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`from`  int(10) UNSIGNED NOT NULL ,
`to`  int(10) UNSIGNED NOT NULL ,
`message`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`sent`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`read`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`direction`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
INDEX `to` (`to`) USING BTREE ,
INDEX `from` (`from`) USING BTREE ,
INDEX `direction` (`direction`) USING BTREE ,
INDEX `read` (`read`) USING BTREE ,
INDEX `sent` (`sent`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_announcements` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`announcement`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`time`  int(10) UNSIGNED NOT NULL ,
`to`  int(10) NOT NULL ,
`recd`  int(1) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
INDEX `to` (`to`) USING BTREE ,
INDEX `time` (`time`) USING BTREE ,
INDEX `to_id` (`to`, `id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_block` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`fromid`  int(10) UNSIGNED NOT NULL ,
`toid`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
INDEX `fromid` (`fromid`) USING BTREE ,
INDEX `toid` (`toid`) USING BTREE ,
INDEX `fromid_toid` (`fromid`, `toid`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_chatroommessages` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`userid`  int(10) UNSIGNED NOT NULL ,
`chatroomid`  int(10) UNSIGNED NOT NULL ,
`message`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`sent`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
INDEX `userid` (`userid`) USING BTREE ,
INDEX `chatroomid` (`chatroomid`) USING BTREE ,
INDEX `sent` (`sent`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_chatrooms` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`lastactivity`  int(10) UNSIGNED NOT NULL ,
`createdby`  int(10) UNSIGNED NOT NULL ,
`password`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`type`  tinyint(1) UNSIGNED NOT NULL ,
`vidsession`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
INDEX `lastactivity` (`lastactivity`) USING BTREE ,
INDEX `createdby` (`createdby`) USING BTREE ,
INDEX `type` (`type`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_chatrooms_users` (
`userid`  int(10) UNSIGNED NOT NULL ,
`chatroomid`  int(10) UNSIGNED NOT NULL ,
`lastactivity`  int(10) UNSIGNED NOT NULL ,
`isbanned`  int(1) NULL DEFAULT 0 ,
PRIMARY KEY (`userid`, `chatroomid`),
INDEX `chatroomid` (`chatroomid`) USING BTREE ,
INDEX `lastactivity` (`lastactivity`) USING BTREE ,
INDEX `userid` (`userid`) USING BTREE ,
INDEX `userid_chatroomid` (`chatroomid`, `userid`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_comethistory` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`channel`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`message`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`sent`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
INDEX `channel` (`channel`) USING BTREE ,
INDEX `sent` (`sent`) USING BTREE ,
INDEX `channel_sent` (`channel`, `sent`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_guests` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`lastactivity`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
INDEX `lastactivity` (`lastactivity`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_messages_old` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`from`  int(10) UNSIGNED NOT NULL ,
`to`  int(10) UNSIGNED NOT NULL ,
`message`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`sent`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`read`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`direction`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
INDEX `to` (`to`) USING BTREE ,
INDEX `from` (`from`) USING BTREE ,
INDEX `direction` (`direction`) USING BTREE ,
INDEX `read` (`read`) USING BTREE ,
INDEX `sent` (`sent`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_status` (
`userid`  int(10) UNSIGNED NOT NULL ,
`message`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`status`  enum('available','away','busy','invisible','offline') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`typingto`  int(10) UNSIGNED NULL DEFAULT NULL ,
`typingtime`  int(10) UNSIGNED NULL DEFAULT NULL ,
`isdevice`  int(1) UNSIGNED NOT NULL DEFAULT 0 ,
`lastactivity`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`userid`),
INDEX `typingto` (`typingto`) USING BTREE ,
INDEX `typingtime` (`typingtime`) USING BTREE ,
INDEX `cometchat_status_lastactivity` (`lastactivity`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_status_old` (
`userid`  int(10) UNSIGNED NOT NULL ,
`message`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`status`  enum('available','away','busy','invisible','offline') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`typingto`  int(10) UNSIGNED NULL DEFAULT NULL ,
`typingtime`  int(10) UNSIGNED NULL DEFAULT NULL ,
`isdevice`  int(1) UNSIGNED NOT NULL DEFAULT 0 ,
`lastactivity`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`userid`),
INDEX `typingto` (`typingto`) USING BTREE ,
INDEX `typingtime` (`typingtime`) USING BTREE ,
INDEX `cometchat_status_lastactivity` (`lastactivity`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `cometchat_videochatsessions` (
`username`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`identity`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`timestamp`  int(10) UNSIGNED NULL DEFAULT 0 ,
PRIMARY KEY (`username`),
INDEX `username` (`username`) USING BTREE ,
INDEX `identity` (`identity`) USING BTREE ,
INDEX `timestamp` (`timestamp`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `comments` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL DEFAULT 0 ,
`crtcp`  int(11) NOT NULL ,
`state`  tinyint(4) NOT NULL ,
`module`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`objectid`  int(11) NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`visability`  tinyint(4) NOT NULL ,
`mailed`  tinyint(4) NOT NULL ,
PRIMARY KEY (`id`),
FULLTEXT INDEX `comment` (`comment`) 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `comments_article` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`comment_id`  int(11) NOT NULL ,
`state`  tinyint(4) NOT NULL ,
`articleid`  int(11) NOT NULL ,
`amount`  float NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Fixed
DELAY_KEY_WRITE=0
;

CREATE TABLE `commissioncontact` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`active`  tinyint(4) NOT NULL ,
`commissionpartner`  tinyint(4) NOT NULL ,
`customer`  tinyint(4) NOT NULL ,
`supplier`  tinyint(4) NOT NULL ,
`client`  int(11) NOT NULL ,
`name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`name2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`city`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`country`  int(11) NOT NULL ,
`phone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`fax`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`email`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`web`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`language`  int(11) NOT NULL ,
`payment_terms`  int(11) NOT NULL DEFAULT 0 ,
`discount`  float NOT NULL DEFAULT 0 ,
`lector_id`  int(11) NOT NULL ,
`shop_login`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`shop_pass`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`login_expire`  int(11) NOT NULL DEFAULT 0 ,
`ticket_enabled`  tinyint(2) NOT NULL DEFAULT 0 ,
`debitor_number`  int(11) NOT NULL ,
`kreditor_number`  int(11) NOT NULL ,
`iban`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`bic`  varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`ust`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`tax_number`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`cust_number`  int(11) NOT NULL ,
`provision`  int(3) NOT NULL DEFAULT 10 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `contactperson` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`active`  tinyint(4) NOT NULL ,
`businesscontact`  int(11) NOT NULL ,
`title`  varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`name2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`city`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`country`  int(11) NOT NULL ,
`phone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`mobil`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`fax`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`email`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`web`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`main_contact`  tinyint(2) NOT NULL ,
`active_adress`  tinyint(2) NOT NULL DEFAULT 1 ,
`alt_name1`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_name2`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_address1`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_address2`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_city`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_country`  int(11) NOT NULL ,
`alt_phone`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_fax`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_mobil`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`alt_email`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_name1`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_name2`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_address1`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_address2`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_zip`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_city`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_country`  int(11) NOT NULL ,
`priv_phone`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_fax`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_mobil`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`priv_email`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`shop_login`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`shop_pass`  varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`enabled_ticket`  tinyint(1) NOT NULL ,
`enabled_article`  tinyint(1) NOT NULL ,
`enabled_personalization`  tinyint(1) NOT NULL ,
`birthdate`  int(11) NOT NULL DEFAULT 0 ,
`notifymailadr`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `contactperson_attributes` (
`contactperson_id`  int(11) NOT NULL ,
`attribute_id`  int(11) NOT NULL ,
`item_id`  int(11) NOT NULL ,
`value`  tinyint(2) NOT NULL ,
`inputvalue`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `contactperson_categories_perm` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`categoryid`  int(11) NOT NULL ,
`cpid`  int(11) NOT NULL ,
`cansee`  tinyint(1) NOT NULL ,
`cancreate`  tinyint(1) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `countries` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`country_name`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`country_name_int`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`country_code`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`country_active`  tinyint(1) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;
INSERT INTO `countries` VALUES ('1', 'Andorra', '', 'AD', '0');
INSERT INTO `countries` VALUES ('2', 'Vereinigte Arabische Emirate', '', 'AE', '0');
INSERT INTO `countries` VALUES ('3', 'Afghanistan', '', 'AF', '0');
INSERT INTO `countries` VALUES ('4', 'Antigua und Barbuda', '', 'AG', '0');
INSERT INTO `countries` VALUES ('5', 'Anguilla', '', 'AI', '0');
INSERT INTO `countries` VALUES ('6', 'Albanien', '', 'AL', '0');
INSERT INTO `countries` VALUES ('7', 'Armenien', '', 'AM', '0');
INSERT INTO `countries` VALUES ('8', 'Niederländische Antillen', '', 'AN', '0');
INSERT INTO `countries` VALUES ('9', 'Angola', '', 'AO', '0');
INSERT INTO `countries` VALUES ('10', 'Antarktis', '', 'AQ', '0');
INSERT INTO `countries` VALUES ('11', 'Argentinien', '', 'AR', '0');
INSERT INTO `countries` VALUES ('12', 'Samoa', '', 'AS', '0');
INSERT INTO `countries` VALUES ('13', 'Österreich', '', 'AT', '1');
INSERT INTO `countries` VALUES ('14', 'Australien', '', 'AU', '0');
INSERT INTO `countries` VALUES ('15', 'Aruba', '', 'AW', '0');
INSERT INTO `countries` VALUES ('16', 'Aserbaidschan', '', 'AZ', '0');
INSERT INTO `countries` VALUES ('17', 'Bosnien-Herzegowina', '', 'BA', '0');
INSERT INTO `countries` VALUES ('18', 'Barbados', '', 'BB', '0');
INSERT INTO `countries` VALUES ('19', 'Bangladesh', '', 'BD', '0');
INSERT INTO `countries` VALUES ('20', 'Belgien', '', 'BE', '0');
INSERT INTO `countries` VALUES ('21', 'Burkina Faso', '', 'BF', '0');
INSERT INTO `countries` VALUES ('22', 'Bulgarien', '', 'BG', '0');
INSERT INTO `countries` VALUES ('23', 'Bahrain', '', 'BH', '0');
INSERT INTO `countries` VALUES ('24', 'Burundi', '', 'BI', '0');
INSERT INTO `countries` VALUES ('25', 'Benin', '', 'BJ', '0');
INSERT INTO `countries` VALUES ('26', 'Bermudas', '', 'BM', '0');
INSERT INTO `countries` VALUES ('27', 'Brunei', '', 'BN', '0');
INSERT INTO `countries` VALUES ('28', 'Bolivien', '', 'BO', '0');
INSERT INTO `countries` VALUES ('29', 'Brasilien', '', 'BR', '0');
INSERT INTO `countries` VALUES ('30', 'Bahamas', '', 'BS', '0');
INSERT INTO `countries` VALUES ('31', 'Bhutan', '', 'BT', '0');
INSERT INTO `countries` VALUES ('32', 'Bouvet-Inseln', '', 'BV', '0');
INSERT INTO `countries` VALUES ('33', 'Botswana', '', 'BW', '0');
INSERT INTO `countries` VALUES ('34', 'Weißrussland', '', 'BY', '0');
INSERT INTO `countries` VALUES ('35', 'Belize', '', 'BZ', '0');
INSERT INTO `countries` VALUES ('36', 'Kanada', '', 'CA', '0');
INSERT INTO `countries` VALUES ('37', 'Kokosinseln', '', 'CC', '0');
INSERT INTO `countries` VALUES ('38', 'Demokratische Republik Kongo (ehemals ZR: Zaire)', '', 'CD', '0');
INSERT INTO `countries` VALUES ('39', 'Zentralafrikanische Republik', '', 'CF', '0');
INSERT INTO `countries` VALUES ('40', 'Kongo', '', 'CG', '0');
INSERT INTO `countries` VALUES ('41', 'Schweiz', '', 'CH', '1');
INSERT INTO `countries` VALUES ('42', 'Elfenbeinküste', '', 'CI', '0');
INSERT INTO `countries` VALUES ('43', 'Cook-Inseln', '', 'CK', '0');
INSERT INTO `countries` VALUES ('44', 'Chile', '', 'CL', '0');
INSERT INTO `countries` VALUES ('45', 'Kamerun', '', 'CM', '0');
INSERT INTO `countries` VALUES ('46', 'China', '', 'CN', '0');
INSERT INTO `countries` VALUES ('47', 'Kolumbien', '', 'CO', '0');
INSERT INTO `countries` VALUES ('48', 'Costa Rica', '', 'CR', '0');
INSERT INTO `countries` VALUES ('49', 'Serbien und Montenegro (früher Tschechoslowakei)', '', 'CS', '0');
INSERT INTO `countries` VALUES ('50', 'Kuba', '', 'CU', '0');
INSERT INTO `countries` VALUES ('51', 'Kap Verde', '', 'CV', '0');
INSERT INTO `countries` VALUES ('52', 'Christmas Island', '', 'CX', '0');
INSERT INTO `countries` VALUES ('53', 'Zypern', '', 'CY', '0');
INSERT INTO `countries` VALUES ('54', 'Tschechische Republik', '', 'CZ', '0');
INSERT INTO `countries` VALUES ('55', 'Deutschland', 'Germany', 'DE', '1');
INSERT INTO `countries` VALUES ('56', 'Djibuti', '', 'DJ', '0');
INSERT INTO `countries` VALUES ('57', 'Dänemark', '', 'DK', '0');
INSERT INTO `countries` VALUES ('58', 'Dominika', '', 'DM', '0');
INSERT INTO `countries` VALUES ('59', 'Dominikanische Republik', '', 'DO', '0');
INSERT INTO `countries` VALUES ('60', 'Algerien', '', 'DZ', '0');
INSERT INTO `countries` VALUES ('61', 'Ecuador', '', 'EC', '0');
INSERT INTO `countries` VALUES ('62', 'Estland', '', 'EE', '0');
INSERT INTO `countries` VALUES ('63', 'Ägypten', '', 'EG', '0');
INSERT INTO `countries` VALUES ('64', 'Westsahara', '', 'EH', '0');
INSERT INTO `countries` VALUES ('65', 'Eritrea', '', 'ER', '0');
INSERT INTO `countries` VALUES ('66', 'Spanien', '', 'ES', '0');
INSERT INTO `countries` VALUES ('67', 'Äthiopien', '', 'ET', '0');
INSERT INTO `countries` VALUES ('68', 'Finnland', '', 'FI', '0');
INSERT INTO `countries` VALUES ('69', 'Fidschi-Inseln', '', 'FJ', '0');
INSERT INTO `countries` VALUES ('70', 'Falkland-Inseln', '', 'FK', '0');
INSERT INTO `countries` VALUES ('71', 'Mikronesien', '', 'FM', '0');
INSERT INTO `countries` VALUES ('72', 'Färöer Inseln', '', 'FO', '0');
INSERT INTO `countries` VALUES ('73', 'Frankreich', '', 'FR', '1');
INSERT INTO `countries` VALUES ('74', 'Frankreich (nur Europa)', '', 'FX', '0');
INSERT INTO `countries` VALUES ('75', 'Gabun', '', 'GA', '0');
INSERT INTO `countries` VALUES ('76', 'Großbritannien (UK)', 'Great Britain', 'GB', '1');
INSERT INTO `countries` VALUES ('77', 'Grenada', '', 'GD', '0');
INSERT INTO `countries` VALUES ('78', 'Georgien', '', 'GE', '0');
INSERT INTO `countries` VALUES ('79', 'französisch Guyana', '', 'GF', '0');
INSERT INTO `countries` VALUES ('80', 'Ghana', '', 'GH', '0');
INSERT INTO `countries` VALUES ('81', 'Gibraltar', '', 'GI', '0');
INSERT INTO `countries` VALUES ('82', 'Grönland', '', 'GL', '0');
INSERT INTO `countries` VALUES ('83', 'Gambia', '', 'GM', '0');
INSERT INTO `countries` VALUES ('84', 'Guinea', '', 'GN', '0');
INSERT INTO `countries` VALUES ('85', 'Guadeloupe', '', 'GP', '0');
INSERT INTO `countries` VALUES ('86', 'Äquatorial Guinea', '', 'GQ', '0');
INSERT INTO `countries` VALUES ('87', 'Griechenland', '', 'GR', '0');
INSERT INTO `countries` VALUES ('88', 'South Georgia und South Sandwich Islands', '', 'GS', '0');
INSERT INTO `countries` VALUES ('89', 'Guatemala', '', 'GT', '0');
INSERT INTO `countries` VALUES ('90', 'Guam', '', 'GU', '0');
INSERT INTO `countries` VALUES ('91', 'Guinea Bissau', '', 'GW', '0');
INSERT INTO `countries` VALUES ('92', 'Guyana', '', 'GY', '0');
INSERT INTO `countries` VALUES ('93', 'Hong Kong', '', 'HK', '0');
INSERT INTO `countries` VALUES ('94', 'Heard und McDonald Islands', '', 'HM', '0');
INSERT INTO `countries` VALUES ('95', 'Honduras', '', 'HN', '0');
INSERT INTO `countries` VALUES ('96', 'Kroatien', '', 'HR', '0');
INSERT INTO `countries` VALUES ('97', 'Haiti', '', 'HT', '0');
INSERT INTO `countries` VALUES ('98', 'Ungarn', '', 'HU', '0');
INSERT INTO `countries` VALUES ('99', 'Indonesien', '', 'ID', '0');
INSERT INTO `countries` VALUES ('100', 'Irland', '', 'IE', '0');
INSERT INTO `countries` VALUES ('101', 'Israel', '', 'IL', '0');
INSERT INTO `countries` VALUES ('102', 'Indien', '', 'IN', '0');
INSERT INTO `countries` VALUES ('103', 'Britisch-Indischer Ozean', '', 'IO', '0');
INSERT INTO `countries` VALUES ('104', 'Irak', '', 'IQ', '0');
INSERT INTO `countries` VALUES ('105', 'Iran', '', 'IR', '0');
INSERT INTO `countries` VALUES ('106', 'Island', '', 'IS', '0');
INSERT INTO `countries` VALUES ('107', 'Italien', '', 'IT', '1');
INSERT INTO `countries` VALUES ('108', 'Jamaika', '', 'JM', '0');
INSERT INTO `countries` VALUES ('109', 'Jordanien', '', 'JO', '0');
INSERT INTO `countries` VALUES ('110', 'Japan', '', 'JP', '0');
INSERT INTO `countries` VALUES ('111', 'Kenia', '', 'KE', '0');
INSERT INTO `countries` VALUES ('112', 'Kirgisistan', '', 'KG', '0');
INSERT INTO `countries` VALUES ('113', 'Kambodscha', '', 'KH', '0');
INSERT INTO `countries` VALUES ('114', 'Kiribati', '', 'KI', '0');
INSERT INTO `countries` VALUES ('115', 'Komoren', '', 'KM', '0');
INSERT INTO `countries` VALUES ('116', 'St. Kitts Nevis Anguilla', '', 'KN', '0');
INSERT INTO `countries` VALUES ('117', 'Nordkorea', '', 'KP', '0');
INSERT INTO `countries` VALUES ('118', 'Südkorea', '', 'KR', '0');
INSERT INTO `countries` VALUES ('119', 'Kuwait', '', 'KW', '0');
INSERT INTO `countries` VALUES ('120', 'Kaiman-Inseln', '', 'KY', '0');
INSERT INTO `countries` VALUES ('121', 'Kasachstan', '', 'KZ', '0');
INSERT INTO `countries` VALUES ('122', 'Laos', '', 'LA', '0');
INSERT INTO `countries` VALUES ('123', 'Libanon', '', 'LB', '0');
INSERT INTO `countries` VALUES ('124', 'Saint Lucia', '', 'LC', '0');
INSERT INTO `countries` VALUES ('125', 'Liechtenstein', '', 'LI', '0');
INSERT INTO `countries` VALUES ('126', 'Sri Lanka', '', 'LK', '0');
INSERT INTO `countries` VALUES ('127', 'Liberia', '', 'LR', '0');
INSERT INTO `countries` VALUES ('128', 'Lesotho', '', 'LS', '0');
INSERT INTO `countries` VALUES ('129', 'Litauen', '', 'LT', '0');
INSERT INTO `countries` VALUES ('130', 'Luxemburg', '', 'LU', '0');
INSERT INTO `countries` VALUES ('131', 'Lettland', '', 'LV', '0');
INSERT INTO `countries` VALUES ('132', 'Libyen', '', 'LY', '0');
INSERT INTO `countries` VALUES ('133', 'Marokko', '', 'MA', '0');
INSERT INTO `countries` VALUES ('134', 'Monaco', '', 'MC', '0');
INSERT INTO `countries` VALUES ('135', 'Moldavien', '', 'MD', '0');
INSERT INTO `countries` VALUES ('136', 'Madagaskar', '', 'MG', '0');
INSERT INTO `countries` VALUES ('137', 'Marshall-Inseln', '', 'MH', '0');
INSERT INTO `countries` VALUES ('138', 'Mazedonien', '', 'MK', '0');
INSERT INTO `countries` VALUES ('139', 'Mali', '', 'ML', '0');
INSERT INTO `countries` VALUES ('140', 'Myanmar', '', 'MM', '0');
INSERT INTO `countries` VALUES ('141', 'Mongolei', '', 'MN', '0');
INSERT INTO `countries` VALUES ('142', 'Macao', '', 'MO', '0');
INSERT INTO `countries` VALUES ('143', 'Marianen', '', 'MP', '0');
INSERT INTO `countries` VALUES ('144', 'Martinique', '', 'MQ', '0');
INSERT INTO `countries` VALUES ('145', 'Mauretanien', '', 'MR', '0');
INSERT INTO `countries` VALUES ('146', 'Montserrat', '', 'MS', '0');
INSERT INTO `countries` VALUES ('147', 'Malta', '', 'MT', '0');
INSERT INTO `countries` VALUES ('148', 'Mauritius', '', 'MU', '0');
INSERT INTO `countries` VALUES ('149', 'Malediven', '', 'MV', '0');
INSERT INTO `countries` VALUES ('150', 'Malawi', '', 'MW', '0');
INSERT INTO `countries` VALUES ('151', 'Mexiko', '', 'MX', '0');
INSERT INTO `countries` VALUES ('152', 'Malaysia', '', 'MY', '0');
INSERT INTO `countries` VALUES ('153', 'Mocambique', '', 'MZ', '0');
INSERT INTO `countries` VALUES ('154', 'Namibia', '', 'NA', '0');
INSERT INTO `countries` VALUES ('155', 'Neukaledonien', '', 'NC', '0');
INSERT INTO `countries` VALUES ('156', 'Niger', '', 'NE', '0');
INSERT INTO `countries` VALUES ('157', 'Norfolk-Inseln', '', 'NF', '0');
INSERT INTO `countries` VALUES ('158', 'Nigeria', '', 'NG', '0');
INSERT INTO `countries` VALUES ('159', 'Nicaragua', '', 'NI', '0');
INSERT INTO `countries` VALUES ('160', 'Niederlande', '', 'NL', '0');
INSERT INTO `countries` VALUES ('161', 'Norwegen', '', 'NO', '0');
INSERT INTO `countries` VALUES ('162', 'Nepal', '', 'NP', '0');
INSERT INTO `countries` VALUES ('163', 'Nauru', '', 'NR', '0');
INSERT INTO `countries` VALUES ('164', 'Niue', '', 'NU', '0');
INSERT INTO `countries` VALUES ('165', 'Neuseeland', '', 'NZ', '0');
INSERT INTO `countries` VALUES ('166', 'Oman', '', 'OM', '0');
INSERT INTO `countries` VALUES ('167', 'Panama', '', 'PA', '0');
INSERT INTO `countries` VALUES ('168', 'Peru', '', 'PE', '0');
INSERT INTO `countries` VALUES ('169', 'Französisch-Polynesien', '', 'PF', '0');
INSERT INTO `countries` VALUES ('170', 'Papua Neuguinea', '', 'PG', '0');
INSERT INTO `countries` VALUES ('171', 'Philippinen', '', 'PH', '0');
INSERT INTO `countries` VALUES ('172', 'Pakistan', '', 'PK', '0');
INSERT INTO `countries` VALUES ('173', 'Polen', '', 'PL', '1');
INSERT INTO `countries` VALUES ('174', 'St. Pierre und Miquelon', '', 'PM', '0');
INSERT INTO `countries` VALUES ('175', 'Pitcairn', '', 'PN', '0');
INSERT INTO `countries` VALUES ('176', 'Puerto Rico', '', 'PR', '0');
INSERT INTO `countries` VALUES ('177', 'Palästinensische Selbstverwaltungsgebiete', '', 'PS', '0');
INSERT INTO `countries` VALUES ('178', 'Portugal', '', 'PT', '0');
INSERT INTO `countries` VALUES ('179', 'Palau', '', 'PW', '0');
INSERT INTO `countries` VALUES ('180', 'Paraguay', '', 'PY', '0');
INSERT INTO `countries` VALUES ('181', 'Qatar', '', 'QA', '0');
INSERT INTO `countries` VALUES ('182', 'Reunion', '', 'RE', '0');
INSERT INTO `countries` VALUES ('183', 'Rumänien', '', 'RO', '0');
INSERT INTO `countries` VALUES ('184', 'Russland', '', 'RU', '0');
INSERT INTO `countries` VALUES ('185', 'Ruanda', '', 'RW', '0');
INSERT INTO `countries` VALUES ('186', 'Saudi-Arabien', '', 'SA', '0');
INSERT INTO `countries` VALUES ('187', 'Solomon-Inseln', '', 'SB', '0');
INSERT INTO `countries` VALUES ('188', 'Seychellen', '', 'SC', '0');
INSERT INTO `countries` VALUES ('189', 'Sudan', '', 'SD', '0');
INSERT INTO `countries` VALUES ('190', 'Schweden', '', 'SE', '0');
INSERT INTO `countries` VALUES ('191', 'Singapur', '', 'SG', '0');
INSERT INTO `countries` VALUES ('192', 'St. Helena', '', 'SH', '0');
INSERT INTO `countries` VALUES ('193', 'Slowenien', '', 'SI', '0');
INSERT INTO `countries` VALUES ('194', 'Svalbard und Jan Mayen Islands', '', 'SJ', '0');
INSERT INTO `countries` VALUES ('195', 'Slowakei (Slowakische Republik)', '', 'SK', '0');
INSERT INTO `countries` VALUES ('196', 'Sierra Leone', '', 'SL', '0');
INSERT INTO `countries` VALUES ('197', 'San Marino', '', 'SM', '0');
INSERT INTO `countries` VALUES ('198', 'Senegal', '', 'SN', '0');
INSERT INTO `countries` VALUES ('199', 'Somalia', '', 'SO', '0');
INSERT INTO `countries` VALUES ('200', 'Surinam', '', 'SR', '0');
INSERT INTO `countries` VALUES ('201', 'Sao Tome', '', 'ST', '0');
INSERT INTO `countries` VALUES ('202', 'Sowjetunion (obsolet)', '', 'SU', '0');
INSERT INTO `countries` VALUES ('203', 'El Salvador', '', 'SV', '0');
INSERT INTO `countries` VALUES ('204', 'Syrien', '', 'SY', '0');
INSERT INTO `countries` VALUES ('205', 'Swasiland', '', 'SZ', '0');
INSERT INTO `countries` VALUES ('206', 'Turks- und Kaikos-Inseln', '', 'TC', '0');
INSERT INTO `countries` VALUES ('207', 'Tschad', '', 'TD', '0');
INSERT INTO `countries` VALUES ('208', 'Französisches Süd-Territorium', '', 'TF', '0');
INSERT INTO `countries` VALUES ('209', 'Togo', '', 'TG', '0');
INSERT INTO `countries` VALUES ('210', 'Thailand', '', 'TH', '0');
INSERT INTO `countries` VALUES ('211', 'Tadschikistan', '', 'TJ', '0');
INSERT INTO `countries` VALUES ('212', 'Tokelau', '', 'TK', '0');
INSERT INTO `countries` VALUES ('213', 'Ost-Timor', '', 'TL', '0');
INSERT INTO `countries` VALUES ('214', 'Turkmenistan', '', 'TM', '0');
INSERT INTO `countries` VALUES ('215', 'Tunesien', '', 'TN', '0');
INSERT INTO `countries` VALUES ('216', 'Tonga', '', 'TO', '0');
INSERT INTO `countries` VALUES ('217', 'Türkei', '', 'TR', '0');
INSERT INTO `countries` VALUES ('218', 'Trinidad Tobago', '', 'TT', '0');
INSERT INTO `countries` VALUES ('219', 'Tuvalu', '', 'TV', '0');
INSERT INTO `countries` VALUES ('220', 'Taiwan', '', 'TW', '0');
INSERT INTO `countries` VALUES ('221', 'Tansania', '', 'TZ', '0');
INSERT INTO `countries` VALUES ('222', 'Ukraine', '', 'UA', '0');
INSERT INTO `countries` VALUES ('223', 'Uganda', '', 'UG', '0');
INSERT INTO `countries` VALUES ('224', 'Großbritannien', '', 'UK', '0');
INSERT INTO `countries` VALUES ('225', 'US- kleinere Inseln außerhalb', '', 'UM', '0');
INSERT INTO `countries` VALUES ('226', 'Vereinigte Staaten von Amerika', '', 'US', '0');
INSERT INTO `countries` VALUES ('227', 'Uruguay', '', 'UY', '0');
INSERT INTO `countries` VALUES ('228', 'Usbekistan', '', 'UZ', '0');
INSERT INTO `countries` VALUES ('229', 'Vatikan', '', 'VA', '0');
INSERT INTO `countries` VALUES ('230', 'St. Vincent', '', 'VC', '0');
INSERT INTO `countries` VALUES ('231', 'Venezuela', '', 'VE', '0');
INSERT INTO `countries` VALUES ('232', 'Virgin Island (Brit.)', '', 'VG', '0');
INSERT INTO `countries` VALUES ('233', 'Virgin Island (USA)', '', 'VI', '0');
INSERT INTO `countries` VALUES ('234', 'Vietnam', '', 'VN', '0');
INSERT INTO `countries` VALUES ('235', 'Vanuatu', '', 'VU', '0');
INSERT INTO `countries` VALUES ('236', 'Wallis et Futuna', '', 'WF', '0');
INSERT INTO `countries` VALUES ('237', 'Samoa', '', 'WS', '0');
INSERT INTO `countries` VALUES ('238', 'Jemen', '', 'YE', '0');
INSERT INTO `countries` VALUES ('239', 'Mayotte', '', 'YT', '0');
INSERT INTO `countries` VALUES ('240', 'Jugoslawien (obsolet)', '', 'YU', '0');
INSERT INTO `countries` VALUES ('241', 'Südafrika', '', 'ZA', '0');
INSERT INTO `countries` VALUES ('242', 'Sambia', '', 'ZM', '0');
INSERT INTO `countries` VALUES ('243', 'Zimbabwe', '', 'ZW', '0');

CREATE TABLE `deliveryterms` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`active`  tinyint(4) NOT NULL ,
`client`  int(11) NOT NULL ,
`name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`charges`  float NOT NULL ,
`shoprel`  int(11) NOT NULL ,
`tax`  float NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `documents` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`doc_name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`doc_req_id`  int(11) NOT NULL ,
`doc_req_module`  smallint(6) NOT NULL DEFAULT 0 ,
`doc_type`  int(11) NOT NULL DEFAULT 0 ,
`doc_hash`  varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`doc_sent`  tinyint(1) NOT NULL DEFAULT 0 ,
`doc_crtdat`  int(11) NOT NULL DEFAULT 0 ,
`doc_crtusr`  int(11) NOT NULL DEFAULT 0 ,
`doc_price_netto`  float NOT NULL DEFAULT 0 ,
`doc_price_brutto`  float NOT NULL DEFAULT 0 ,
`doc_payable`  int(11) NOT NULL DEFAULT 0 ,
`doc_payed`  int(11) NOT NULL DEFAULT 0 ,
`doc_warning_id`  int(11) NULL DEFAULT NULL ,
`doc_reverse`  tinyint(2) NOT NULL ,
`doc_storno_date`  int(11) NOT NULL ,
`paper_order_pid`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_unicode_ci
ROW_FORMAT=Compact
;

CREATE TABLE `documents_freednumbers` (
`type`  int(11) NOT NULL ,
`number`  varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`client_id`  int(11) NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `events` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) NOT NULL ,
`public`  tinyint(1) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`description`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`begin`  int(11) NOT NULL ,
`end`  int(11) NOT NULL ,
`participants_ext`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`participants_int`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`adress`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `finishing` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`lector_id`  int(11) NOT NULL DEFAULT 0 ,
`status`  tinyint(2) NOT NULL ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`beschreibung`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`kosten`  float NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `foldtypes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(4) NOT NULL DEFAULT 1 ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`beschreibung`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`vertical`  smallint(6) NOT NULL DEFAULT 0 ,
`horizontal`  smallint(6) NOT NULL DEFAULT 0 ,
`picture`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`breaks`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `formats` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`width`  int(11) NOT NULL ,
`height`  int(11) NOT NULL ,
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;
INSERT INTO `formats` VALUES ('1', 'DIN A0', '841', '1189');
INSERT INTO `formats` VALUES ('2', 'DIN A1', '594', '841');
INSERT INTO `formats` VALUES ('3', 'DIN A2', '420', '594');
INSERT INTO `formats` VALUES ('4', 'DIN A3', '297', '420');
INSERT INTO `formats` VALUES ('5', 'DIN A4', '210', '297');
INSERT INTO `formats` VALUES ('6', 'DIN A5', '148', '210');
INSERT INTO `formats` VALUES ('7', 'DIN A6', '105', '148');
INSERT INTO `formats` VALUES ('8', 'DIN A7', '74', '105');
INSERT INTO `formats` VALUES ('9', 'DIN A8', '52', '74');
INSERT INTO `formats` VALUES ('10', 'DIN A9', '37', '52');
INSERT INTO `formats` VALUES ('11', 'DIN A10', '26', '37');
INSERT INTO `formats` VALUES ('12', 'DIN B0', '1000', '1414');
INSERT INTO `formats` VALUES ('13', 'DIN B1', '707', '1000');
INSERT INTO `formats` VALUES ('14', 'DIN B2', '500', '707');
INSERT INTO `formats` VALUES ('15', 'DIN B3', '353', '500');
INSERT INTO `formats` VALUES ('16', 'DIN B4', '250', '353');
INSERT INTO `formats` VALUES ('17', 'DIN B5', '176', '250');
INSERT INTO `formats` VALUES ('18', 'DIN B6', '125', '176');
INSERT INTO `formats` VALUES ('19', 'DIN B7', '88', '125');
INSERT INTO `formats` VALUES ('20', 'DIN B8', '62', '88');
INSERT INTO `formats` VALUES ('21', 'DIN B9', '44', '62');
INSERT INTO `formats` VALUES ('23', 'DIN C0', '917', '1297');
INSERT INTO `formats` VALUES ('24', 'DIN C1', '648', '917');
INSERT INTO `formats` VALUES ('25', 'DIN C2', '458', '648');
INSERT INTO `formats` VALUES ('26', 'DIN C3', '324', '458');
INSERT INTO `formats` VALUES ('27', 'DIN C4', '229', '324');
INSERT INTO `formats` VALUES ('28', 'DIN C5', '162', '229');
INSERT INTO `formats` VALUES ('29', 'DIN C6', '114', '162');
INSERT INTO `formats` VALUES ('30', 'DIN C7', '81', '114');
INSERT INTO `formats` VALUES ('31', 'DIN C8', '57', '81');
INSERT INTO `formats` VALUES ('32', 'DIN C9', '40', '57');
INSERT INTO `formats` VALUES ('33', 'DIN C10', '28', '40');
INSERT INTO `formats` VALUES ('34', 'DIN D0', '771', '1090');
INSERT INTO `formats` VALUES ('35', 'DIN D1', '545', '771');
INSERT INTO `formats` VALUES ('36', 'DIN D2', '385', '545');
INSERT INTO `formats` VALUES ('37', 'DIN D3', '272', '385');
INSERT INTO `formats` VALUES ('38', 'DIN D4', '192', '272');
INSERT INTO `formats` VALUES ('39', 'DIN D5', '136', '192');
INSERT INTO `formats` VALUES ('40', 'DIN D6', '96', '136');
INSERT INTO `formats` VALUES ('41', 'DIN D7', '68', '96');
INSERT INTO `formats` VALUES ('42', 'DIN D8', '48', '68');
INSERT INTO `formats` VALUES ('43', 'DIN D9', '34', '48');
INSERT INTO `formats` VALUES ('44', 'DIN D10', '24', '34');
INSERT INTO `formats` VALUES ('45', 'DIN Lang', '105', '210');
INSERT INTO `formats` VALUES ('46', 'DIN Lang klassisch', '99', '210');
INSERT INTO `formats` VALUES ('47', 'freies Format', '0', '0');
INSERT INTO `formats` VALUES ('48', 'Visitenkarte QF', '85', '55');
INSERT INTO `formats` VALUES ('49', 'Etikette 80x120 mm', '120', '80');
INSERT INTO `formats` VALUES ('50', 'Scheckkarte', '85', '55');
INSERT INTO `formats` VALUES ('51', 'Scheckkarte (mit Foto)', '100', '65');
INSERT INTO `formats` VALUES ('52', 'Visitenkarte HF', '55', '85');
INSERT INTO `formats` VALUES ('53', 'Visitenkarte', '85', '55');
INSERT INTO `formats` VALUES ('54', 'DIN Lang masch Kuv', '229', '114');
INSERT INTO `formats` VALUES ('55', 'Visitenkarte', '90', '50');
INSERT INTO `formats` VALUES ('56', 'Website', '100', '100');
INSERT INTO `formats` VALUES ('57', 'Produkt Frei', '100', '100');
INSERT INTO `formats` VALUES ('58', 'CS30', '141', '240');

CREATE TABLE `ftpcustuploads` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`ftp_cust_id`  int(11) NOT NULL ,
`ftp_orgname`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`ftp_hash`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`ftp_status`  int(11) NOT NULL ,
`ftp_conf_step`  tinyint(4) NOT NULL DEFAULT 4 ,
`ftp_filesize`  int(11) NOT NULL DEFAULT 0 ,
`ftp_crtdat`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `ftpdownloads` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`ftp_cust_id`  int(11) NOT NULL ,
`ftp_orgname`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`ftp_hash`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`ftp_status`  int(11) NOT NULL ,
`ftp_conf_step`  tinyint(4) NOT NULL DEFAULT 4 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `groups` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`group_name`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`group_description`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`group_status`  tinyint(4) NULL DEFAULT NULL ,
`group_rights`  int(64) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id_UNIQUE` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `info_table` (
`uniq`  int(11) NOT NULL AUTO_INCREMENT ,
`form`  int(11) NULL DEFAULT NULL ,
`id`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`time`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`views`  int(11) NULL DEFAULT NULL ,
`submissions`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`uniq`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `invoices_emissions` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`invc_title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`invc_number`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`invc_price_netto`  decimal(11,2) NOT NULL ,
`invc_taxes_active`  smallint(6) NOT NULL DEFAULT 1 ,
`invc_payed`  smallint(6) NOT NULL DEFAULT 0 ,
`invc_payed_dat`  int(11) NOT NULL DEFAULT 0 ,
`invc_payable_dat`  int(11) NOT NULL DEFAULT 0 ,
`invc_crtusr`  int(11) NOT NULL DEFAULT 0 ,
`invc_crtdat`  int(11) NOT NULL DEFAULT 0 ,
`invc_companyid`  int(11) NOT NULL DEFAULT 0 ,
`invc_supplierid`  int(11) NOT NULL DEFAULT 0 ,
`invc_orders`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`),
INDEX `invc_payed` (`invc_payed`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `invoices_reverts` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`rev_title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`rev_number`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`rev_price_netto`  decimal(11,2) NOT NULL ,
`rev_taxes_active`  smallint(6) NOT NULL DEFAULT 1 ,
`rev_payed`  smallint(6) NOT NULL DEFAULT 0 ,
`rev_payed_dat`  int(11) NOT NULL DEFAULT 0 ,
`rev_payable_dat`  int(11) NOT NULL DEFAULT 0 ,
`rev_crtusr`  int(11) NOT NULL DEFAULT 0 ,
`rev_crtdat`  int(11) NOT NULL DEFAULT 0 ,
`rev_companyid`  int(11) NOT NULL DEFAULT 0 ,
`rev_supplierid`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
INDEX `rev_payed` (`rev_payed`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `invoices_templates` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`invc_title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`invc_price_netto`  decimal(11,2) NOT NULL ,
`invc_taxes_active`  smallint(6) NOT NULL DEFAULT 1 ,
`invc_crtusr`  int(11) NOT NULL DEFAULT 0 ,
`invc_crtdat`  int(11) NOT NULL DEFAULT 0 ,
`invc_companyid`  int(11) NOT NULL DEFAULT 0 ,
`invc_supplierid`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
INDEX `invc_crtdat` (`invc_crtdat`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `language` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`language`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`language_int`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`language_code`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'de' ,
`language_active`  tinyint(1) NOT NULL ,
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;
INSERT INTO `language` VALUES ('1', 'Afar', '', 'aa', '0');
INSERT INTO `language` VALUES ('2', 'Abchasisch', '', 'ab', '0');
INSERT INTO `language` VALUES ('3', 'Afrikaans', '', 'af', '0');
INSERT INTO `language` VALUES ('4', 'Amharisch', '', 'am', '0');
INSERT INTO `language` VALUES ('5', 'Arabisch', '', 'ar', '0');
INSERT INTO `language` VALUES ('6', 'Assamesisch', '', 'as', '0');
INSERT INTO `language` VALUES ('7', 'Aymara', '', 'ay', '0');
INSERT INTO `language` VALUES ('8', 'Aserbaidschanisch', '', 'az', '0');
INSERT INTO `language` VALUES ('9', 'Baschkirisch', '', 'ba', '0');
INSERT INTO `language` VALUES ('10', 'Belorussisch', '', 'be', '0');
INSERT INTO `language` VALUES ('11', 'Bulgarisch', '', 'bg', '0');
INSERT INTO `language` VALUES ('12', 'Biharisch', '', 'bh', '0');
INSERT INTO `language` VALUES ('13', 'Bislamisch', '', 'bi', '0');
INSERT INTO `language` VALUES ('14', 'Bengalisch', '', 'bn', '0');
INSERT INTO `language` VALUES ('15', 'Tibetanisch', '', 'bo', '0');
INSERT INTO `language` VALUES ('16', 'Bretonisch', '', 'br', '0');
INSERT INTO `language` VALUES ('17', 'Katalanisch', '', 'ca', '0');
INSERT INTO `language` VALUES ('18', 'Korsisch', '', 'co', '0');
INSERT INTO `language` VALUES ('19', 'Tschechisch', '', 'cs', '0');
INSERT INTO `language` VALUES ('20', 'Walisisch', '', 'cy', '0');
INSERT INTO `language` VALUES ('21', 'Dänisch', '', 'da', '0');
INSERT INTO `language` VALUES ('22', 'Deutsch', 'German', 'de', '1');
INSERT INTO `language` VALUES ('23', 'Dzongkha, Bhutani', '', 'dz', '0');
INSERT INTO `language` VALUES ('24', 'Griechisch', '', 'el', '0');
INSERT INTO `language` VALUES ('25', 'Englisch', 'English', 'en', '1');
INSERT INTO `language` VALUES ('26', 'Esperanto', '', 'eo', '0');
INSERT INTO `language` VALUES ('27', 'Spanisch', '', 'es', '0');
INSERT INTO `language` VALUES ('28', 'Estnisch', '', 'et', '0');
INSERT INTO `language` VALUES ('29', 'Baskisch', '', 'eu', '0');
INSERT INTO `language` VALUES ('30', 'Persisch', '', 'fa', '0');
INSERT INTO `language` VALUES ('31', 'Finnisch', '', 'fi', '0');
INSERT INTO `language` VALUES ('32', 'Fiji', '', 'fj', '0');
INSERT INTO `language` VALUES ('33', 'Färöisch', '', 'fo', '0');
INSERT INTO `language` VALUES ('34', 'Französisch', '', 'fr', '0');
INSERT INTO `language` VALUES ('35', 'Friesisch', '', 'fy', '0');
INSERT INTO `language` VALUES ('36', 'Irisch', '', 'ga', '0');
INSERT INTO `language` VALUES ('37', 'Schottisches Gälisch', '', 'gd', '0');
INSERT INTO `language` VALUES ('38', 'Galizisch', '', 'gl', '0');
INSERT INTO `language` VALUES ('39', 'Guarani', '', 'gn', '0');
INSERT INTO `language` VALUES ('40', 'Gujaratisch', '', 'gu', '0');
INSERT INTO `language` VALUES ('41', 'Haussa', '', 'ha', '0');
INSERT INTO `language` VALUES ('42', 'Hebräisch', '', 'he', '0');
INSERT INTO `language` VALUES ('43', 'Hindi', '', 'hi', '0');
INSERT INTO `language` VALUES ('44', 'Kroatisch', '', 'hr', '0');
INSERT INTO `language` VALUES ('45', 'Ungarisch', '', 'hu', '0');
INSERT INTO `language` VALUES ('46', 'Armenisch', '', 'hy', '0');
INSERT INTO `language` VALUES ('47', 'Interlingua', '', 'ia', '0');
INSERT INTO `language` VALUES ('48', 'Indonesisch', '', 'id', '0');
INSERT INTO `language` VALUES ('49', 'Interlingue', '', 'ie', '0');
INSERT INTO `language` VALUES ('50', 'Inupiak', '', 'ik', '0');
INSERT INTO `language` VALUES ('51', 'Isländisch', '', 'is', '0');
INSERT INTO `language` VALUES ('52', 'Italienisch', '', 'it', '0');
INSERT INTO `language` VALUES ('53', 'Inuktitut (Eskimo)', '', 'iu', '0');
INSERT INTO `language` VALUES ('54', 'Hebräisch (veraltet, nun: he)', '', 'iw', '0');
INSERT INTO `language` VALUES ('55', 'Japanisch', '', 'ja', '0');
INSERT INTO `language` VALUES ('56', 'Jiddish (veraltet, nun: yi)', '', 'ji', '0');
INSERT INTO `language` VALUES ('57', 'Javanisch', '', 'jv', '0');
INSERT INTO `language` VALUES ('58', 'Georgisch', '', 'ka', '0');
INSERT INTO `language` VALUES ('59', 'Kasachisch', '', 'kk', '0');
INSERT INTO `language` VALUES ('60', 'Kalaallisut (Grönländisch)', '', 'kl', '0');
INSERT INTO `language` VALUES ('61', 'Kambodschanisch', '', 'km', '0');
INSERT INTO `language` VALUES ('62', 'Kannada', '', 'kn', '0');
INSERT INTO `language` VALUES ('63', 'Koreanisch', '', 'ko', '0');
INSERT INTO `language` VALUES ('64', 'Kaschmirisch', '', 'ks', '0');
INSERT INTO `language` VALUES ('65', 'Kurdisch', '', 'ku', '0');
INSERT INTO `language` VALUES ('66', 'Kirgisisch', '', 'ky', '0');
INSERT INTO `language` VALUES ('67', 'Lateinisch', '', 'la', '0');
INSERT INTO `language` VALUES ('68', 'Lingala', '', 'ln', '0');
INSERT INTO `language` VALUES ('69', 'Laotisch', '', 'lo', '0');
INSERT INTO `language` VALUES ('70', 'Litauisch', '', 'lt', '0');
INSERT INTO `language` VALUES ('71', 'Lettisch', '', 'lv', '0');
INSERT INTO `language` VALUES ('72', 'Malagasisch', '', 'mg', '0');
INSERT INTO `language` VALUES ('73', 'Maorisch', '', 'mi', '0');
INSERT INTO `language` VALUES ('74', 'Mazedonisch', '', 'mk', '0');
INSERT INTO `language` VALUES ('75', 'Malajalam', '', 'ml', '0');
INSERT INTO `language` VALUES ('76', 'Mongolisch', '', 'mn', '0');
INSERT INTO `language` VALUES ('77', 'Moldavisch', '', 'mo', '0');
INSERT INTO `language` VALUES ('78', 'Marathi', '', 'mr', '0');
INSERT INTO `language` VALUES ('79', 'Malaysisch', '', 'ms', '0');
INSERT INTO `language` VALUES ('80', 'Maltesisch', '', 'mt', '0');
INSERT INTO `language` VALUES ('81', 'Burmesisch', '', 'my', '0');
INSERT INTO `language` VALUES ('82', 'Nauruisch', '', 'na', '0');
INSERT INTO `language` VALUES ('83', 'Nepalesisch', '', 'ne', '0');
INSERT INTO `language` VALUES ('84', 'Holländisch', '', 'nl', '0');
INSERT INTO `language` VALUES ('85', 'Norwegisch', '', 'no', '0');
INSERT INTO `language` VALUES ('86', 'Okzitanisch', '', 'oc', '0');
INSERT INTO `language` VALUES ('87', 'Oromo', '', 'om', '0');
INSERT INTO `language` VALUES ('88', 'Oriya', '', 'or', '0');
INSERT INTO `language` VALUES ('89', 'Pundjabisch', '', 'pa', '0');
INSERT INTO `language` VALUES ('90', 'Polnisch', '', 'pl', '0');
INSERT INTO `language` VALUES ('91', 'Paschtu', '', 'ps', '0');
INSERT INTO `language` VALUES ('92', 'Portugiesisch', '', 'pt', '0');
INSERT INTO `language` VALUES ('93', 'Quechua', '', 'qu', '0');
INSERT INTO `language` VALUES ('94', 'Rätoromanisch', '', 'rm', '0');
INSERT INTO `language` VALUES ('95', 'Kirundisch', '', 'rn', '0');
INSERT INTO `language` VALUES ('96', 'Rumänisch', '', 'ro', '0');
INSERT INTO `language` VALUES ('97', 'Russisch', '', 'ru', '0');
INSERT INTO `language` VALUES ('98', 'Kijarwanda', '', 'rw', '0');
INSERT INTO `language` VALUES ('99', 'Sanskrit', '', 'sa', '0');
INSERT INTO `language` VALUES ('100', 'Zinti', '', 'sd', '0');
INSERT INTO `language` VALUES ('101', 'Sango', '', 'sg', '0');
INSERT INTO `language` VALUES ('102', 'Serbokroatisch (veraltet)', '', 'sh', '0');
INSERT INTO `language` VALUES ('103', 'Singhalesisch', '', 'si', '0');
INSERT INTO `language` VALUES ('104', 'Slowakisch', '', 'sk', '0');
INSERT INTO `language` VALUES ('105', 'Slowenisch', '', 'sl', '0');
INSERT INTO `language` VALUES ('106', 'Samoanisch', '', 'sm', '0');
INSERT INTO `language` VALUES ('107', 'Schonisch', '', 'sn', '0');
INSERT INTO `language` VALUES ('108', 'Somalisch', '', 'so', '0');
INSERT INTO `language` VALUES ('109', 'Albanisch', '', 'sq', '0');
INSERT INTO `language` VALUES ('110', 'Serbisch', '', 'sr', '0');
INSERT INTO `language` VALUES ('111', 'Swasiländisch', '', 'ss', '0');
INSERT INTO `language` VALUES ('112', 'Sesothisch', '', 'st', '0');
INSERT INTO `language` VALUES ('113', 'Sudanesisch', '', 'su', '0');
INSERT INTO `language` VALUES ('114', 'Schwedisch', '', 'sv', '0');
INSERT INTO `language` VALUES ('115', 'Suaheli', '', 'sw', '0');
INSERT INTO `language` VALUES ('116', 'Tamilisch', '', 'ta', '0');
INSERT INTO `language` VALUES ('117', 'Tegulu', '', 'te', '0');
INSERT INTO `language` VALUES ('118', 'Tadschikisch', '', 'tg', '0');
INSERT INTO `language` VALUES ('119', 'Thai', '', 'th', '0');
INSERT INTO `language` VALUES ('120', 'Tigrinja', '', 'ti', '0');
INSERT INTO `language` VALUES ('121', 'Turkmenisch', '', 'tk', '0');
INSERT INTO `language` VALUES ('122', 'Tagalog', '', 'tl', '0');
INSERT INTO `language` VALUES ('123', 'Sezuan', '', 'tn', '0');
INSERT INTO `language` VALUES ('124', 'Tongaisch', '', 'to', '0');
INSERT INTO `language` VALUES ('125', 'Türkisch', '', 'tr', '0');
INSERT INTO `language` VALUES ('126', 'Tsongaisch', '', 'ts', '0');
INSERT INTO `language` VALUES ('127', 'Tatarisch', '', 'tt', '0');
INSERT INTO `language` VALUES ('128', 'Twi', '', 'tw', '0');
INSERT INTO `language` VALUES ('129', 'Uigur', '', 'ug', '0');
INSERT INTO `language` VALUES ('130', 'Ukrainisch', '', 'uk', '0');
INSERT INTO `language` VALUES ('131', 'Urdu', '', 'ur', '0');
INSERT INTO `language` VALUES ('132', 'Usbekisch', '', 'uz', '0');
INSERT INTO `language` VALUES ('133', 'Vietnamesisch', '', 'vi', '0');
INSERT INTO `language` VALUES ('134', 'Volapük', '', 'vo', '0');
INSERT INTO `language` VALUES ('135', 'Wolof', '', 'wo', '0');
INSERT INTO `language` VALUES ('136', 'Xhosa', '', 'xh', '0');
INSERT INTO `language` VALUES ('137', 'Jiddish', '', 'yi', '0');
INSERT INTO `language` VALUES ('138', 'Joruba', '', 'yo', '0');
INSERT INTO `language` VALUES ('139', 'Zhuang', '', 'za', '0');
INSERT INTO `language` VALUES ('140', 'Chinesisch', '', 'zh', '0');
INSERT INTO `language` VALUES ('141', 'Zulu', '', 'zu', '0');

CREATE TABLE `machine_groups` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`position`  int(11) NOT NULL DEFAULT 0 ,
`type`  smallint(1) NOT NULL DEFAULT 0 COMMENT '0 = inhouse, 1 = Fremdleistung' ,
`lector_id`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
UNIQUE INDEX `position` (`position`) USING BTREE ,
UNIQUE INDEX `name` (`name`) USING BTREE ,
INDEX `lector_id` (`lector_id`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `machines` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`lector_id`  int(11) NOT NULL DEFAULT 0 ,
`state`  tinyint(2) NOT NULL DEFAULT 1 ,
`type`  tinyint(4) NOT NULL ,
`group`  tinyint(4) NOT NULL ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`document_text`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`pricebase`  tinyint(4) NOT NULL DEFAULT 5 ,
`price`  float NOT NULL DEFAULT 0 ,
`border_left`  int(11) NOT NULL DEFAULT 0 ,
`border_right`  int(11) NOT NULL DEFAULT 0 ,
`border_top`  int(11) NOT NULL DEFAULT 0 ,
`border_bottom`  int(11) NOT NULL DEFAULT 0 ,
`colors_front`  smallint(6) NOT NULL DEFAULT 0 ,
`colors_back`  smallint(6) NOT NULL DEFAULT 0 ,
`time_platechange`  smallint(6) NOT NULL DEFAULT 0 ,
`time_colorchange`  smallint(6) NOT NULL DEFAULT 0 ,
`time_base`  smallint(6) NOT NULL DEFAULT 10 ,
`units_per_hour`  int(11) NOT NULL DEFAULT 0 ,
`unit`  tinyint(4) NOT NULL DEFAULT 1 ,
`finish`  tinyint(1) NOT NULL DEFAULT 0 ,
`finish_plate_cost`  float NOT NULL DEFAULT 0 ,
`finish_paper_cost`  float NOT NULL DEFAULT 0 ,
`maxhours`  smallint(6) NOT NULL DEFAULT 0 ,
`paper_size_height`  smallint(6) NOT NULL DEFAULT 0 ,
`paper_size_width`  smallint(6) NOT NULL DEFAULT 0 ,
`paper_size_min_height`  smallint(6) NOT NULL DEFAULT 0 ,
`paper_size_min_width`  smallint(6) NOT NULL DEFAULT 0 ,
`difficulty`  smallint(6) NOT NULL DEFAULT 0 ,
`time_setup_stations`  float NOT NULL DEFAULT 0 ,
`anz_stations`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`pages_per_station`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`anz_signatures`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`time_signatures`  int(11) NOT NULL DEFAULT 0 ,
`time_envelope`  int(11) NOT NULL DEFAULT 0 ,
`time_trimmer`  int(11) NOT NULL DEFAULT 0 ,
`time_stacker`  int(11) NOT NULL DEFAULT 0 ,
`crtdat`  int(11) NOT NULL DEFAULT 0 ,
`crtusr`  int(11) NOT NULL DEFAULT 0 ,
`upddat`  int(11) NULL DEFAULT NULL ,
`updusr`  int(11) NULL DEFAULT NULL ,
`cutprice`  float NOT NULL ,
`umschl_umst`  tinyint(4) NOT NULL DEFAULT 0 ,
`internaltext`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`hersteller`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '' ,
`baujahr`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '' ,
`DPHeight`  float(11,0) NOT NULL ,
`DPWidth`  float(11,0) NOT NULL ,
`breaks`  int(11) NOT NULL ,
`breaks_time`  int(11) NOT NULL ,
`color`  varchar(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `machines_chromaticities` (
`machine_id`  int(11) NOT NULL ,
`chroma_id`  int(11) NOT NULL ,
`clickprice`  float NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `machines_difficulties` (
`machine_id`  int(11) NOT NULL ,
`diff_id`  int(11) NOT NULL DEFAULT 0 ,
`diff_unit`  int(11) NOT NULL ,
`value`  float NOT NULL DEFAULT 0 ,
`percent`  float NOT NULL DEFAULT 0 ,
INDEX `machine_id` (`machine_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `machines_locks` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`machineid`  int(11) NOT NULL ,
`start`  int(11) NOT NULL ,
`stop`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `machines_qualified_users` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`machine`  int(11) NOT NULL ,
`user`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `machines_unitsperhour` (
`machine_id`  int(11) NOT NULL DEFAULT 0 ,
`units_from`  int(11) NOT NULL DEFAULT 0 ,
`units_amount`  int(11) NOT NULL DEFAULT 0 ,
INDEX `machine_id` (`machine_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `menu_elements` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`path`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`icon`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`public`  tinyint(1) NOT NULL ,
`parent`  int(11) NOT NULL ,
`type`  tinyint(4) NOT NULL ,
`order`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;
INSERT INTO `menu_elements` VALUES ('1', 'Administration', '', 'images/icons/cross-small.png', '0', '0', '0', '106');
INSERT INTO `menu_elements` VALUES ('2', 'Benutzer', 'libs/basic/user/user.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '0');
INSERT INTO `menu_elements` VALUES ('4', 'Mandanten', 'libs/basic/clients/client.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '4');
INSERT INTO `menu_elements` VALUES ('5', 'Menüstruktur', 'libs/basic/menu/menuconfig.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '5');
INSERT INTO `menu_elements` VALUES ('47', 'Länderverwaltung', 'libs/basic/countries/country.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '6');
INSERT INTO `menu_elements` VALUES ('63', 'Gruppen', 'libs/basic/groups/groups.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '1');
INSERT INTO `menu_elements` VALUES ('67', 'Merkmale', 'libs/modules/businesscontact/attributes.overview.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '3');
INSERT INTO `menu_elements` VALUES ('72', 'Geschäftskontakte', 'libs/modules/businesscontact/businesscontact.php', 'images/icons/user-business.png', '0', '71', '1', '2');
INSERT INTO `menu_elements` VALUES ('116', 'Schriftarten', 'libs/modules/personalization/persofont.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '12');
INSERT INTO `menu_elements` VALUES ('124', 'Einstellungen', 'libs/modules/perferences/perferences.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '0');
INSERT INTO `menu_elements` VALUES ('129', 'Organizer', '', '', '1', '0', '0', '100');
INSERT INTO `menu_elements` VALUES ('130', 'Geschäftskontakte', 'libs/modules/businesscontact/businesscontact.php', 'images/icons/aaa_contilas_icon.png', '0', '129', '1', '3');
INSERT INTO `menu_elements` VALUES ('134', 'Kalender', 'libs/modules/organizer/calendar.php', 'images/icons/aaa_contilas_icon.png', '1', '129', '1', '2');
INSERT INTO `menu_elements` VALUES ('135', 'Kontakte Privat', 'libs/modules/organizer/kontakte.php', 'images/icons/ui-radio-button-uncheck.png', '0', '129', '1', '4');
INSERT INTO `menu_elements` VALUES ('136', 'Nachrichten', 'libs/modules/organizer/nachrichten.php', 'images/icons/aaa_contilas_icon.png', '0', '129', '1', '1');
INSERT INTO `menu_elements` VALUES ('137', 'Provisionspartner', 'libs/modules/commissioncontact/commissioncontact.php', 'images/icons/ui-radio-button-uncheck.png', '0', '129', '1', '5');
INSERT INTO `menu_elements` VALUES ('139', 'Serienbriefe', 'libs/modules/bulkLetter/bulkLetter.overview.php', 'images/icons/ui-radio-button-uncheck.png', '0', '129', '1', '6');
INSERT INTO `menu_elements` VALUES ('140', 'Urlaubsplanung', 'libs/modules/organizer/urlaub.php', 'images/icons/ui-radio-button-uncheck.png', '0', '129', '1', '7');
INSERT INTO `menu_elements` VALUES ('141', 'Vorgänge', '', '', '0', '0', '0', '101');
INSERT INTO `menu_elements` VALUES ('143', 'Bestellungen(Perso)', 'libs/modules/personalization/personalization.order.overview.php', 'images/icons/ui-radio-button-uncheck.png', '0', '141', '1', '3');
INSERT INTO `menu_elements` VALUES ('144', 'Statistik', 'libs/modules/statistics/overview.php', 'images/icons/ui-radio-button-uncheck.png', '0', '141', '1', '4');
INSERT INTO `menu_elements` VALUES ('145', 'Job-Tickets', 'libs/modules/tickets/ticket.php', 'images/icons/ui-radio-button-uncheck.png', '1', '141', '1', '2');
INSERT INTO `menu_elements` VALUES ('146', 'Vorgänge / Kalkulationen', 'libs/modules/calculation/order.php', 'images/icons/ui-radio-button-uncheck.png', '0', '141', '1', '1');
INSERT INTO `menu_elements` VALUES ('147', 'Planung', '', '', '0', '0', '0', '102');
INSERT INTO `menu_elements` VALUES ('148', 'Auftragsimport', 'libs/modules/sched_own/orderimport.php', 'images/icons/ui-radio-button-uncheck.png', '0', '147', '1', '3');
INSERT INTO `menu_elements` VALUES ('149', 'Auslieferungsplan', 'libs/modules/schedule/deliveryplan.php', 'images/icons/ui-radio-button-uncheck.png', '0', '147', '1', '3');
INSERT INTO `menu_elements` VALUES ('150', 'Lager', 'libs/modules/warehouse/warehouse.php', 'images/icons/ui-radio-button-uncheck.png', '0', '147', '1', '4');
INSERT INTO `menu_elements` VALUES ('151', 'Planungsübersicht', 'libs/modules/schedule/schedule.php', 'images/icons/ui-radio-button-uncheck.png', '0', '147', '1', '1');
INSERT INTO `menu_elements` VALUES ('153', 'Kundenportal', '', '', '0', '0', '0', '103');
INSERT INTO `menu_elements` VALUES ('155', 'Kundenuploads', 'libs/modules/custupload/overview.php', 'images/icons/ui-radio-button-uncheck.png', '0', '153', '1', '1');
INSERT INTO `menu_elements` VALUES ('156', 'Web to Print', 'libs/modules/personalization/personalization.php', 'images/icons/ui-radio-button-uncheck.png', '0', '153', '1', '2');
INSERT INTO `menu_elements` VALUES ('157', 'Fragenbögen', '', '', '0', '153', '0', '3');
INSERT INTO `menu_elements` VALUES ('160', 'Ergebnisse', 'libs/modules/surverys/surverys.submissions.php', 'images/icons/ui-radio-button-uncheck.png', '0', '157', '1', '2');
INSERT INTO `menu_elements` VALUES ('161', 'Übersicht', 'libs/modules/surverys/surverys.overview.php', 'images/icons/ui-radio-button-uncheck.png', '0', '157', '1', '1');
INSERT INTO `menu_elements` VALUES ('162', 'Buchhaltung', '', '', '0', '0', '0', '104');
INSERT INTO `menu_elements` VALUES ('163', 'Rechnungseingang', 'libs/modules/accounting/incominginvoice.php', 'images/icons/ui-radio-button-uncheck.png', '0', '162', '1', '1');
INSERT INTO `menu_elements` VALUES ('164', 'Rechnungseingang Vorlagen', 'libs/modules/accounting/incominginvoicetemplate.php', 'images/icons/ui-radio-button-uncheck.png', '0', '162', '1', '2');
INSERT INTO `menu_elements` VALUES ('165', 'Rechnungsausgang', 'libs/modules/accounting/outgoinginvoice.php', 'images/icons/ui-radio-button-uncheck.png', '0', '162', '1', '3');
INSERT INTO `menu_elements` VALUES ('166', 'Mahnungen', 'libs/modules/accounting/invoicewarning.php', 'images/icons/ui-radio-button-uncheck.png', '0', '162', '1', '4');
INSERT INTO `menu_elements` VALUES ('167', 'Mahnstufen', 'libs/modules/accounting/warnlevel.overview.php', 'images/icons/ui-radio-button-uncheck.png', '0', '162', '1', '5');
INSERT INTO `menu_elements` VALUES ('168', 'Eingangsgutschriften', 'libs/modules/accounting/incomingrevert.php', 'images/icons/ui-radio-button-uncheck.png', '0', '162', '1', '6');
INSERT INTO `menu_elements` VALUES ('169', 'Stammdaten', '', '', '0', '0', '0', '105');
INSERT INTO `menu_elements` VALUES ('170', 'Artikel', 'libs/modules/article/article.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '1');
INSERT INTO `menu_elements` VALUES ('171', 'Produkte', 'libs/modules/products/products.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '2');
INSERT INTO `menu_elements` VALUES ('172', 'Produktformate', 'libs/modules/paperformats/paperformats.php', 'images/icons/ui-radio-button-uncheck.png', '1', '169', '1', '3');
INSERT INTO `menu_elements` VALUES ('173', 'Warengruppe', 'libs/modules/tradegroup/tradegroup.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '4');
INSERT INTO `menu_elements` VALUES ('176', 'Maschinen', 'libs/modules/machines/machines.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '5');
INSERT INTO `menu_elements` VALUES ('178', 'Maschinengruppen', 'libs/modules/machines/machinegroup.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '6');
INSERT INTO `menu_elements` VALUES ('179', 'Falzarten', 'libs/modules/foldtypes/foldtypes.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '7');
INSERT INTO `menu_elements` VALUES ('180', 'Farbigkeit', 'libs/modules/chromaticity/chromaticity.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '8');
INSERT INTO `menu_elements` VALUES ('181', 'Lacke', 'libs/modules/finishings/finishings.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '9');
INSERT INTO `menu_elements` VALUES ('182', 'Papiere', 'libs/modules/paper/paper.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '10');
INSERT INTO `menu_elements` VALUES ('183', 'Lieferarten', 'libs/modules/deliveryterms/deliveryterms.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '11');
INSERT INTO `menu_elements` VALUES ('184', 'Personalisierung', 'libs/modules/personalization/personalization.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '12');
INSERT INTO `menu_elements` VALUES ('185', 'Zahlungsarten', 'libs/modules/paymentterms/paymentterms.php', 'images/icons/ui-radio-button-uncheck.png', '0', '169', '1', '13');
INSERT INTO `menu_elements` VALUES ('186', 'Aufgaben', 'libs/modules/tasks/task.overview.php', 'images/icons/ui-radio-button-uncheck.png', '1', '129', '1', '20');
INSERT INTO `menu_elements` VALUES ('187', 'Dokumente', 'libs/modules/autodoc/autotemplate.php', 'images/icons/ui-radio-button-uncheck.png', '0', '1', '1', '8');
INSERT INTO `menu_elements` VALUES ('188', 'Tickets', 'libs/modules/tickets/ticket.admin.php', 'images/icons/status-offline.png', '0', '1', '1', '10');
INSERT INTO `menu_elements` VALUES ('189', 'Planungstafel', 'libs/modules/schedule/schedule.timeline.php', 'images/icons/ui-radio-button-uncheck.png', '1', '147', '1', '2');
INSERT INTO `menu_elements` VALUES ('190', 'Sperrzeiten', 'libs/modules/machines/machine.locks.php', 'images/icons/ui-radio-button-uncheck.png', '1', '147', '1', '10');
INSERT INTO `menu_elements` VALUES ('191', 'Ansprechpartner', 'libs/modules/businesscontact/contactperson.overview.php', 'images/icons/aaa_contilas_icon.png', '0', '129', '1', '3');
INSERT INTO `menu_elements` VALUES ('193', 'Mail v2', 'libs/modules/mail/mail.overview.php', 'images/icons/mail.png', '1', '129', '1', '0');
INSERT INTO `menu_elements` VALUES ('194', 'Planungs V2', 'libs/modules/planning/planning.overview.php', 'images/icons/ui-radio-button-uncheck.png', '1', '147', '1', '0');

CREATE TABLE `menu_groups` (
`menu_id`  int(11) NOT NULL ,
`group_id`  int(11) NOT NULL ,
INDEX `menu_id` (`menu_id`, `group_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `menu_status` (
`user_id`  int(11) NOT NULL ,
`menu_elements_id`  int(11) NOT NULL ,
`display`  tinyint(4) NOT NULL DEFAULT 1 ,
INDEX `user_id` (`user_id`) USING BTREE ,
INDEX `menu_elements_id` (`menu_elements_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `menu_user` (
`menu_id`  int(11) NOT NULL ,
`user_id`  int(11) NOT NULL ,
INDEX `menu_id` (`menu_id`, `user_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `msg` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`from_id`  int(11) NOT NULL ,
`subject`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`text`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`created`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `msg_docs` (
`msg_id`  int(11) NOT NULL ,
`document_id`  int(11) NOT NULL ,
INDEX `msg_id` (`msg_id`, `document_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `msg_folder` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`parent`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `msg_in_folder` (
`msg_id`  int(11) NOT NULL ,
`user_id`  int(11) NOT NULL ,
`folder_id`  int(11) NOT NULL ,
`gelesen`  int(11) NOT NULL ,
`geantwortet`  int(11) NOT NULL ,
INDEX `msg_id` (`msg_id`, `user_id`, `folder_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `notes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`state`  tinyint(2) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`module`  int(11) NOT NULL ,
`objectid`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`file_name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `notifications` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user`  int(11) NOT NULL ,
`title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`path`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`state`  int(11) NOT NULL ,
`crtmodule`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `orders` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`number`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`status`  tinyint(2) NOT NULL DEFAULT 1 ,
`businesscontact_id`  int(11) NOT NULL ,
`product_id`  int(11) NOT NULL DEFAULT 0 ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`notes`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`delivery_address_id`  int(11) NOT NULL DEFAULT 0 ,
`invoice_address_id`  int(11) NOT NULL DEFAULT 0 ,
`delivery_terms_id`  int(11) NOT NULL DEFAULT 0 ,
`payment_terms_id`  int(11) NOT NULL DEFAULT 0 ,
`delivery_date`  int(11) NOT NULL DEFAULT 0 ,
`delivery_cost`  float NOT NULL DEFAULT 0 ,
`text_offer`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`text_offerconfirm`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`text_invoice`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`cust_contactperson`  int(11) NOT NULL DEFAULT 0 ,
`crtdat`  int(11) NOT NULL ,
`crtusr`  int(11) NOT NULL ,
`upddat`  int(11) NULL DEFAULT NULL ,
`updusr`  int(11) NULL DEFAULT NULL ,
`collectiveinvoice_id`  int(11) NOT NULL ,
`intern_contactperson`  int(11) NOT NULL ,
`cust_message`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`cust_sign`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`inv_amount`  int(11) NOT NULL ,
`inv_price_update`  tinyint(2) NOT NULL ,
`deliv_amount`  int(11) NOT NULL ,
`label_logo_active`  tinyint(2) NOT NULL ,
`label_box_amount`  int(11) NOT NULL ,
`label_title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`show_product`  tinyint(1) NOT NULL COMMENT 'Produktdetails auf Dokumenten zeigen' ,
`productname`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`show_price_per_thousand`  tinyint(1) NOT NULL ,
`paper_order_boegen`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`paper_order_price`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`paper_order_supplier`  int(11) NOT NULL ,
`paper_order_calc`  int(11) NOT NULL ,
`beilagen`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `orders_articles` (
`calc_id`  int(11) NOT NULL ,
`article_id`  int(11) NOT NULL ,
`amount`  float NOT NULL ,
`scale`  int(11) NOT NULL COMMENT '0=proKalk, 1=proStk' 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `orders_calculationpositions` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(4) NOT NULL ,
`quantity`  int(11) NOT NULL ,
`price`  float NOT NULL ,
`tax`  int(11) NOT NULL DEFAULT 19 ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`calculation_id`  int(11) NOT NULL ,
`type`  int(11) NOT NULL ,
`inv_rel`  int(11) NOT NULL DEFAULT 1 ,
`object_id`  int(11) NOT NULL COMMENT 'Manuell o. Artikel ID' ,
`scale`  tinyint(1) NOT NULL COMMENT 'pro Stk o. Kalkulation' ,
`show_price`  tinyint(1) NOT NULL ,
`show_quantity`  tinyint(1) NOT NULL ,
`cost`  float NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `orders_calculations` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`order_id`  int(11) NOT NULL ,
`state`  tinyint(1) NOT NULL DEFAULT 1 ,
`product_format`  int(11) NOT NULL ,
`product_format_width`  int(11) NOT NULL ,
`product_format_height`  int(11) NOT NULL ,
`product_format_width_open`  int(11) NOT NULL ,
`product_format_height_open`  int(11) NOT NULL ,
`product_pages_content`  int(11) NOT NULL ,
`product_pages_addcontent`  int(11) NOT NULL ,
`product_pages_envelope`  int(11) NOT NULL ,
`product_amount`  int(11) NOT NULL ,
`product_sorts`  int(11) NOT NULL ,
`paper_content`  int(11) NOT NULL ,
`paper_content_width`  int(11) NOT NULL ,
`paper_content_height`  int(11) NOT NULL ,
`paper_content_weight`  int(11) NOT NULL ,
`paper_addcontent`  int(11) NOT NULL ,
`paper_addcontent_width`  int(11) NOT NULL ,
`paper_addcontent_height`  int(11) NOT NULL ,
`paper_addcontent_weight`  int(11) NOT NULL ,
`paper_envelope`  int(11) NOT NULL ,
`paper_envelope_width`  int(11) NOT NULL ,
`paper_envelope_height`  int(11) NOT NULL ,
`paper_envelope_weight`  int(11) NOT NULL ,
`product_folding`  int(11) NOT NULL ,
`add_charge`  float NOT NULL DEFAULT 0 ,
`margin`  float NOT NULL DEFAULT 0 ,
`discount`  float NOT NULL DEFAULT 0 ,
`chromaticities_content`  int(11) NOT NULL DEFAULT 0 ,
`chromaticities_addcontent`  int(11) NOT NULL DEFAULT 0 ,
`chromaticities_envelope`  int(11) NOT NULL DEFAULT 0 ,
`envelope_height_open`  int(11) NOT NULL DEFAULT 0 ,
`envelope_width_open`  int(11) NOT NULL DEFAULT 0 ,
`calc_auto_values`  tinyint(1) NOT NULL DEFAULT 1 ,
`paper_content_grant`  int(11) NOT NULL DEFAULT 0 ,
`paper_addcontent_grant`  int(11) NOT NULL DEFAULT 0 ,
`paper_envelope_grant`  int(11) NOT NULL DEFAULT 0 ,
`text_processing`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`foldscheme_content`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`foldscheme_addcontent`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`foldscheme_envelope`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`crtdat`  int(11) NOT NULL ,
`crtusr`  int(11) NOT NULL ,
`upddat`  int(11) NULL DEFAULT NULL ,
`updusr`  int(11) NULL DEFAULT NULL ,
`product_pages_addcontent2`  int(11) NOT NULL ,
`paper_addcontent2`  int(11) NOT NULL ,
`paper_addcontent2_width`  int(11) NOT NULL ,
`paper_addcontent2_height`  int(11) NOT NULL ,
`paper_addcontent2_weight`  int(11) NOT NULL ,
`chromaticities_addcontent2`  int(11) NOT NULL DEFAULT 0 ,
`paper_addcontent2_grant`  int(11) NOT NULL ,
`foldscheme_addcontent2`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`product_pages_addcontent3`  int(11) NOT NULL ,
`paper_addcontent3`  int(11) NOT NULL ,
`paper_addcontent3_width`  int(11) NOT NULL ,
`paper_addcontent3_height`  int(11) NOT NULL ,
`paper_addcontent3_weight`  int(11) NOT NULL ,
`chromaticities_addcontent3`  int(11) NOT NULL DEFAULT 0 ,
`paper_addcontent3_grant`  int(11) NOT NULL ,
`foldscheme_addcontent3`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cut_content`  float NOT NULL DEFAULT 3 ,
`cut_addcontent`  float NOT NULL DEFAULT 3 ,
`cut_addcontent2`  float NOT NULL DEFAULT 3 ,
`cut_addcontent3`  float NOT NULL DEFAULT 3 ,
`cut_envelope`  float NOT NULL DEFAULT 3 ,
`color_control`  tinyint(4) NOT NULL DEFAULT 1 ,
`cutter_weight`  int(11) NOT NULL DEFAULT 0 ,
`cutter_height`  int(11) NOT NULL DEFAULT 0 ,
`roll_dir`  int(4) NOT NULL DEFAULT 0 ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '' ,
PRIMARY KEY (`id`),
INDEX `order_id` (`order_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `orders_machines` (
`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
`info`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`calc_id`  int(11) NOT NULL ,
`machine_id`  int(11) NOT NULL ,
`machine_group`  smallint(6) NOT NULL ,
`chromaticity_id`  int(11) NOT NULL ,
`time`  smallint(6) NOT NULL ,
`price`  float NOT NULL ,
`part`  smallint(6) NOT NULL ,
`finishing`  tinyint(1) NOT NULL DEFAULT 0 ,
`supplier_send_date`  int(11) NOT NULL ,
`supplier_receive_date`  int(11) NOT NULL ,
`supplier_id`  int(11) NOT NULL ,
`supplier_info`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`supplier_price`  float NOT NULL COMMENT 'EK vom Lieferanten' ,
`supplier_status`  tinyint(2) NOT NULL ,
`umschl_umst`  tinyint(4) NOT NULL DEFAULT 0 ,
`cutter_cuts`  int(11) NOT NULL DEFAULT 0 ,
`roll_dir`  int(11) NOT NULL ,
`format_in_width`  int(11) NOT NULL ,
`format_in_height`  int(11) NOT NULL ,
`format_out_width`  int(11) NOT NULL ,
`format_out_height`  int(11) NOT NULL ,
`color_detail`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`special_margin`  float(11,2) NOT NULL ,
`special_margin_text`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`foldtype`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `papers` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL DEFAULT 1 ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`type`  smallint(6) NOT NULL COMMENT 'Bögen oder Rolle' ,
`pricebase`  tinyint(4) NOT NULL DEFAULT 1 ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `papers_prices` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`paper_id`  int(11) NOT NULL ,
`weight_from`  int(11) NOT NULL ,
`weight_to`  int(11) NOT NULL ,
`size_width`  int(11) NOT NULL ,
`size_height`  int(11) NOT NULL ,
`quantity_from`  int(11) NOT NULL ,
`price`  float NOT NULL ,
`weight`  float NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `papers_sizes` (
`paper_id`  int(11) NOT NULL ,
`width`  int(11) NOT NULL ,
`height`  int(11) NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `papers_supplier` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`paper_id`  int(11) NOT NULL ,
`supplier_id`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Fixed
DELAY_KEY_WRITE=0
;

CREATE TABLE `papers_weights` (
`paper_id`  int(11) NOT NULL ,
`weight`  smallint(6) NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `paymentterms` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`active`  tinyint(4) NOT NULL ,
`client`  int(11) NOT NULL ,
`name1`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`skonto_days1`  tinyint(4) NOT NULL ,
`skonto1`  tinyint(4) NOT NULL ,
`skonto_days2`  tinyint(4) NOT NULL ,
`skonto2`  tinyint(4) NOT NULL ,
`netto_days`  tinyint(4) NOT NULL ,
`shop_rel`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `perferences` (
`zuschussprodp`  float(12,2) NOT NULL DEFAULT 0.00 ,
`calc_detailed_printpreview`  tinyint(1) NOT NULL DEFAULT 1 ,
`pdf_margin_top`  float(12,2) NOT NULL ,
`pdf_margin_left`  float(12,2) NOT NULL ,
`pdf_margin_right`  float(12,2) NOT NULL ,
`pdf_margin_bottom`  float(12,2) NOT NULL ,
`default_ticket_id`  int(11) NOT NULL ,
`dt_show_default`  int(11) NOT NULL DEFAULT 10 ,
`dt_state_save`  tinyint(1) NOT NULL ,
`mail_domain`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;
INSERT INTO `perferences` VALUES ('10.00', '1', '50.00', '30.00', '15.00', '30.00', '49', '10', '0', '');

CREATE TABLE `perferences_formats_raw` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`width`  float(11,2) NOT NULL ,
`height`  float(11,2) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `persofont` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`filename`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`filename2`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `personalization` (
`id`  int(32) NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`status`  tinyint(2) NOT NULL ,
`picture`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`article`  int(11) NOT NULL ,
`customer`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
`uptdate`  int(11) NOT NULL ,
`uptuser`  int(11) NOT NULL ,
`direction`  int(11) NOT NULL ,
`format`  varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`format_width`  float NOT NULL ,
`format_height`  float NOT NULL ,
`type`  tinyint(2) NOT NULL ,
`picture2`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`linebyline`  tinyint(1) NOT NULL DEFAULT 0 ,
`hidden`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `personalization_items` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`xpos`  float NOT NULL ,
`ypos`  float NOT NULL ,
`height`  float NOT NULL ,
`width`  float NOT NULL ,
`boxtype`  int(11) NOT NULL ,
`personalization_id`  int(11) NOT NULL ,
`text_size`  float NOT NULL ,
`justification`  tinyint(2) NOT NULL ,
`font`  smallint(6) NOT NULL ,
`color_c`  smallint(4) NOT NULL ,
`color_m`  smallint(4) NOT NULL ,
`color_y`  smallint(4) NOT NULL ,
`color_k`  smallint(4) NOT NULL ,
`spacing`  float NOT NULL ,
`dependency_id`  int(11) NOT NULL ,
`reverse`  tinyint(2) NOT NULL ,
`predefined`  tinyint(2) NOT NULL DEFAULT 0 ,
`position`  tinyint(2) NOT NULL DEFAULT 0 ,
`readonly`  tinyint(2) NOT NULL DEFAULT 0 ,
`tab`  float(11,2) NOT NULL ,
`zzgroup`  tinyint(1) NOT NULL ,
`sort`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `personalization_orderitems` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`persoid`  int(11) NOT NULL ,
`persoorderid`  int(11) NOT NULL ,
`persoitemid`  int(11) NOT NULL ,
`value`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `personalization_orders` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL DEFAULT 1 ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`persoid`  int(11) NOT NULL ,
`documentid`  int(11) NOT NULL ,
`customerid`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`orderdate`  int(11) NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`amount`  int(11) NOT NULL ,
`contact_person_id`  int(11) NOT NULL ,
`deliveryaddress_id`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `personalization_seperation` (
`sep_personalizationid`  int(11) NOT NULL ,
`sep_min`  int(11) NOT NULL ,
`sep_max`  int(11) NOT NULL ,
`sep_price`  float NOT NULL ,
`sep_show`  tinyint(2) NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `planning_jobs` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`object`  int(11) NOT NULL ,
`type`  tinyint(1) NOT NULL ,
`subobject`  int(11) NOT NULL ,
`assigned_user`  int(11) NOT NULL ,
`ticket`  int(11) NOT NULL ,
`start`  int(11) NOT NULL ,
`stop`  int(11) NOT NULL ,
`state`  tinyint(1) NOT NULL DEFAULT 1 ,
`artmach`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `products` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`state`  tinyint(2) NOT NULL DEFAULT 1 ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`description`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`picture`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`pages_from`  smallint(6) NOT NULL ,
`pages_to`  smallint(6) NOT NULL ,
`pages_step`  smallint(6) NOT NULL ,
`has_content`  tinyint(1) NOT NULL DEFAULT 1 ,
`has_addcontent`  tinyint(1) NOT NULL DEFAULT 0 ,
`has_envelope`  tinyint(1) NOT NULL DEFAULT 0 ,
`factor_width`  float NOT NULL DEFAULT 1 ,
`factor_height`  float NOT NULL DEFAULT 1 ,
`taxes`  float NOT NULL DEFAULT 19 ,
`grant_paper`  int(11) NOT NULL DEFAULT 0 ,
`type`  smallint(6) NOT NULL DEFAULT 0 ,
`text_offer`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`text_offerconfirm`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`text_invoice`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`text_processing`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`shop_rel`  int(11) NOT NULL ,
`tradegroup`  int(11) NOT NULL ,
`is_individual`  tinyint(4) NOT NULL DEFAULT 1 ,
`has_addcontent2`  tinyint(1) NOT NULL ,
`has_addcontent3`  tinyint(1) NOT NULL ,
`load_dummydata`  tinyint(2) NOT NULL ,
`singleplateset`  tinyint(1) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `products_formats` (
`product_id`  int(11) NOT NULL ,
`format_id`  int(11) NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `products_machines` (
`product_id`  int(11) NOT NULL ,
`machine_id`  int(11) NOT NULL ,
`default`  tinyint(2) NOT NULL DEFAULT 0 ,
`minimum`  int(11) NOT NULL DEFAULT 0 ,
`maximum`  int(11) NOT NULL DEFAULT 0 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `products_papers` (
`product_id`  int(11) NOT NULL ,
`paper_id`  int(11) NOT NULL ,
`weight`  smallint(6) NOT NULL ,
`part`  tinyint(4) NOT NULL DEFAULT 1 ,
INDEX `product_id` (`product_id`, `paper_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `schedules` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(1) NOT NULL DEFAULT 1 ,
`finished`  tinyint(1) NOT NULL DEFAULT 0 ,
`number`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`customer_id`  int(11) NOT NULL DEFAULT 0 ,
`customer_cp_id`  int(11) NOT NULL ,
`object`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`amount`  int(11) NOT NULL DEFAULT 0 ,
`colors`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`finishing_id`  int(11) NOT NULL DEFAULT 0 ,
`delivery_date`  int(11) NOT NULL DEFAULT 0 ,
`delivery_location`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`deliveryterms_id`  int(11) NOT NULL DEFAULT 0 ,
`notes`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`createuser`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`status_dtp`  tinyint(1) NOT NULL DEFAULT 0 ,
`status_paper`  tinyint(1) NOT NULL DEFAULT 0 ,
`lector_id`  int(11) NOT NULL DEFAULT 0 ,
`druckplan_id`  int(11) NOT NULL DEFAULT 0 ,
`crtusr`  int(11) NOT NULL DEFAULT 0 ,
`crtdat`  int(11) NOT NULL DEFAULT 0 ,
`updusr`  int(11) NOT NULL ,
`upddat`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `schedules_downtimes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(1) NOT NULL DEFAULT 1 ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `name` (`name`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `schedules_machines` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`schedules_part_id`  int(11) NOT NULL DEFAULT 0 COMMENT 'jobs.id' ,
`machine_group`  int(11) NOT NULL DEFAULT 0 COMMENT 'machine_categories.id' ,
`machine_id`  int(11) NOT NULL DEFAULT 0 COMMENT 'machines.id' ,
`target_time`  float(9,2) NULL DEFAULT 0.00 COMMENT 'Sollzeit. Kann NULL sein, weil bei Fremdleistungen nicht erfassbar.' ,
`actual_time`  float(9,2) NULL DEFAULT NULL COMMENT 'Istzeit' ,
`down_time`  float(9,2) NULL DEFAULT NULL COMMENT 'Ausfallzeit' ,
`down_time_type`  int(11) NULL DEFAULT NULL COMMENT 'Art der Ausfallzeit - downtimes.id' ,
`deadline`  int(11) NOT NULL DEFAULT 0 COMMENT 'Termin' ,
`notes`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`crtusr`  int(11) NOT NULL DEFAULT 0 ,
`crtdat`  int(11) NOT NULL DEFAULT 0 ,
`updusr`  int(11) NULL DEFAULT NULL ,
`upddat`  int(11) NULL DEFAULT NULL ,
`priority`  int(11) NOT NULL DEFAULT 0 ,
`lector_id`  int(11) NOT NULL DEFAULT 0 ,
`finished`  tinyint(1) NOT NULL ,
`amount`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`colors`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`finishing`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
INDEX `schedules_part_id` (`schedules_part_id`) USING BTREE ,
INDEX `category_id` (`machine_group`) USING BTREE ,
INDEX `machine_id` (`machine_id`) USING BTREE ,
INDEX `down_time_type` (`down_time_type`) USING BTREE ,
INDEX `created_by` (`crtusr`) USING BTREE ,
INDEX `updated_by` (`updusr`) USING BTREE ,
INDEX `lector_id` (`lector_id`) USING BTREE ,
INDEX `deadline` (`deadline`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `schedules_machines_usertime` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`sched_machine`  int(11) NOT NULL ,
`user`  int(11) NOT NULL ,
`ticket`  int(11) NOT NULL ,
`ticket_time`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `schedules_parts` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`finished`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = nicht erledigt, 1 = erledigt' ,
`schedule_id`  int(11) NOT NULL DEFAULT 0 ,
`crtusr`  int(11) NOT NULL DEFAULT 0 ,
`crtdat`  int(11) NOT NULL DEFAULT 0 ,
`updusr`  int(11) NULL DEFAULT NULL ,
`upddat`  int(11) NULL DEFAULT NULL ,
`lector_id`  int(11) NOT NULL DEFAULT 0 ,
`druckplan_id`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
INDEX `k_job_id` (`schedule_id`) USING BTREE ,
INDEX `created_by` (`crtusr`) USING BTREE ,
INDEX `updated_by` (`updusr`) USING BTREE ,
INDEX `k_lector_id` (`lector_id`) USING BTREE 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Fixed
DELAY_KEY_WRITE=0
;

CREATE TABLE `stats` (
`id`  int(11) NULL DEFAULT NULL ,
`time`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Fixed
DELAY_KEY_WRITE=0
;

CREATE TABLE `submissions` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`content`  mediumblob NULL ,
`seen`  int(11) NULL DEFAULT NULL ,
`form_id`  int(11) NULL DEFAULT NULL ,
`added`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `tasks` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`crt_date`  int(11) NOT NULL ,
`due_date`  int(11) NOT NULL ,
`prio`  tinyint(2) NOT NULL ,
`crt_usr`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `tickets` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`crtdate`  int(11) NOT NULL ,
`crtuser`  int(11) NOT NULL ,
`duedate`  int(11) NOT NULL ,
`closedate`  int(11) NULL DEFAULT NULL ,
`closeuser`  int(11) NULL DEFAULT NULL ,
`editdate`  int(11) NULL DEFAULT NULL ,
`number`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`customer`  int(11) NOT NULL ,
`customer_cp`  int(11) NOT NULL ,
`assigned_user`  int(11) NULL DEFAULT NULL ,
`assigned_group`  int(11) NULL DEFAULT NULL ,
`state`  tinyint(4) NOT NULL ,
`category`  int(11) NOT NULL ,
`priority`  int(11) NOT NULL ,
`source`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`tourmarker`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`planned_time`  float(11,2) NOT NULL DEFAULT 0.00 ,
PRIMARY KEY (`id`),
FULLTEXT INDEX `title` (`title`) ,
FULLTEXT INDEX `number` (`number`) ,
FULLTEXT INDEX `number_2` (`number`, `title`) 
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `tickets_categories` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`protected`  tinyint(4) NOT NULL DEFAULT 0 ,
`sort`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM AUTO_INCREMENT=4 
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;
INSERT INTO `tickets_categories` VALUES ('2', 'Job', '1', '5');
INSERT INTO `tickets_categories` VALUES ('3', 'Geschlossen', '1', '6');

CREATE TABLE `tickets_categories_groupperm` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`categoryid`  int(11) NOT NULL ,
`groupid`  int(11) NOT NULL ,
`cansee`  tinyint(1) NOT NULL ,
`cancreate`  tinyint(1) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `tickets_logs` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`ticket`  int(11) NOT NULL ,
`crtusr`  int(11) NOT NULL ,
`date`  int(11) NOT NULL ,
`entry`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `tickets_priorities` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`value`  int(11) NOT NULL ,
`protected`  tinyint(4) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM AUTO_INCREMENT=10 
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;
INSERT INTO `tickets_priorities` VALUES ('2', '5-7-T', '3', '1');
INSERT INTO `tickets_priorities` VALUES ('3', '3-4-T', '4', '1');
INSERT INTO `tickets_priorities` VALUES ('4', '8-14-T', '2', '1');
INSERT INTO `tickets_priorities` VALUES ('9', 'Alarm', '1', '1');

CREATE TABLE `tickets_states` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`protected`  tinyint(4) NOT NULL DEFAULT 0 ,
`colorcode`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM AUTO_INCREMENT=4 
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;
INSERT INTO `tickets_states` VALUES ('1', 'gelöscht', '1', '#04d562');
INSERT INTO `tickets_states` VALUES ('2', 'offen', '1', '#1b8ccd');
INSERT INTO `tickets_states` VALUES ('3', 'geschlossen', '1', '#ca3806');

CREATE TABLE `timekeeper` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL ,
`user_id`  int(11) NOT NULL ,
`module`  int(11) NOT NULL ,
`object_id`  int(11) NOT NULL ,
`startdate`  int(11) NOT NULL ,
`enddate`  int(11) NOT NULL ,
`comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`sub_object_id`  int(11) NOT NULL ,
`article_id`  int(11) NOT NULL ,
`article_amount`  float NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
CHECKSUM=0
ROW_FORMAT=Dynamic
DELAY_KEY_WRITE=0
;

CREATE TABLE `timers` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`crtuser`  int(11) NOT NULL ,
`module`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`objectid`  int(11) NOT NULL ,
`state`  tinyint(1) NOT NULL ,
`starttime`  int(11) NOT NULL ,
`stoptime`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `tradegroup` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`tradegroup_state`  int(11) NOT NULL DEFAULT 1 ,
`tradegroup_title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
`tradegroup_desc`  text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
`tradegroup_shoprel`  int(11) NOT NULL DEFAULT 0 ,
`tradegroup_parentid`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
ROW_FORMAT=Compact
;

CREATE TABLE `user` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`client`  int(11) NOT NULL ,
`login`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`password`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`user_firstname`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`user_lastname`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`user_level`  int(11) NOT NULL ,
`user_email`  varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`user_phone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`user_signature`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`user_lang`  int(11) NOT NULL DEFAULT 1 ,
`user_active`  tinyint(4) NOT NULL DEFAULT 1 ,
`user_forwardmail`  tinyint(4) NOT NULL DEFAULT 1 ,
`telefon_ip`  varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`cal_birthdays`  tinyint(4) NOT NULL DEFAULT 0 ,
`cal_tickets`  tinyint(4) NOT NULL DEFAULT 0 ,
`cal_orders`  tinyint(4) NOT NULL DEFAULT 0 ,
`w_mo`  float(4,2) NOT NULL ,
`w_tu`  float(4,2) NOT NULL ,
`w_we`  float(4,2) NOT NULL ,
`w_th`  float(4,2) NOT NULL ,
`w_fr`  float(4,2) NOT NULL ,
`w_sa`  float(4,2) NOT NULL ,
`w_su`  float(4,2) NOT NULL ,
`w_month`  float(11,2) NOT NULL ,
PRIMARY KEY (`id`),
INDEX `login` (`login`) USING BTREE ,
INDEX `fk_user_clients` (`client`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `user_contacts` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) NOT NULL ,
`status`  tinyint(4) NOT NULL ,
`name1`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`name2`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address1`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`address2`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`postcode`  varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`city`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`phone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`fax`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`email`  varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`cellphone`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`website`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`notes`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`public`  tinyint(4) NOT NULL ,
`country`  int(11) NOT NULL ,
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `user_emailaddress` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  tinyint(2) NOT NULL ,
`user_id`  smallint(6) NOT NULL ,
`address`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`password`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`type`  tinyint(2) NOT NULL ,
`host`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`port`  smallint(4) NOT NULL ,
`signature`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`use_imap`  tinyint(1) NULL DEFAULT NULL ,
`use_ssl`  tinyint(1) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `user_groups` (
`user_id`  int(11) NOT NULL ,
`group_id`  int(11) NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `user_worktimes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user`  int(11) NOT NULL ,
`weekday`  int(11) NOT NULL ,
`start`  int(11) NOT NULL ,
`end`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `vacation` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) NOT NULL ,
`reason`  smallint(6) NOT NULL ,
`notes`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`useddays`  float NOT NULL ,
`begin`  int(11) NOT NULL ,
`end`  int(11) NOT NULL ,
`state`  int(11) NOT NULL ,
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `warehouse` (
`id`  int(32) NOT NULL AUTO_INCREMENT ,
`wh_name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`wh_customer`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`wh_input`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`wh_amount`  int(255) NOT NULL ,
`wh_amount_reserved`  int(255) NOT NULL DEFAULT 0 ,
`wh_recall`  int(32) NOT NULL ,
`wh_ordernumber`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`wh_status`  smallint(4) NOT NULL ,
`wh_comment`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`wh_contactperson`  int(11) NOT NULL ,
`wh_minimum`  int(11) NOT NULL ,
`wh_articleid`  smallint(6) NOT NULL ,
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `warehouse_reservations` (
`id`  int(32) NOT NULL AUTO_INCREMENT ,
`gid`  int(32) NOT NULL ,
`wh_id`  int(32) NOT NULL ,
`op_id`  int(32) NOT NULL ,
`article_id`  int(32) NOT NULL ,
`amount`  int(255) NOT NULL ,
`status`  smallint(4) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;

CREATE TABLE `warnlevel` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  int(11) NOT NULL ,
`title`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`text`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`deadline`  smallint(6) NOT NULL ,
`crt_user`  int(11) NOT NULL ,
`crt_date`  int(11) NOT NULL ,
`upd_user`  int(11) NOT NULL ,
`upd_date`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;