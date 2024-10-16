CREATE TABLE `master`
(
    `key`   VARCHAR(128),
    `value` VARCHAR(512),
    `data`  LONGBLOB,
    PRIMARY KEY (`key`)
);

CREATE TABLE `categories`
(
    `id`   INT UNSIGNED,
    `name` VARCHAR(512) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `accounts`
(
    `no`          VARCHAR(128),
    `name`        VARCHAR(512) NOT NULL,
    `real`        BOOL,
    `category_id` INT UNSIGNED,

    INDEX (`category_id`),

    FOREIGN KEY (`category_id`)
        REFERENCES `categories` (`id`)
        ON DELETE SET NULL,

    PRIMARY KEY (`no`)
);

CREATE TABLE `contacts`
(
    `id`      INT UNSIGNED AUTO_INCREMENT,
    `name`    VARCHAR(512) NOT NULL,
    `mail`    VARCHAR(512),
    `phone`   VARCHAR(512),
    `address` TEXT,
    PRIMARY KEY (`id`)
);

CREATE TABLE `invoices`
(
    `id`              INT UNSIGNED AUTO_INCREMENT,
    `type`            INT UNSIGNED   NOT NULL,
    `date`            DATE           NOT NULL,
    `amount`          DECIMAL(10, 2) NOT NULL,
    `description`     VARCHAR(512)   NOT NULL,
    `reference`       VARCHAR(512),
    `document`        LONGBLOB,
    `document_name`   VARCHAR(512),
    `no_document`     BOOL,
    `finished`        BOOL,
    `contact_id`      INT UNSIGNED,
    `contact_address` TEXT,
    `closed`          BOOL,

    INDEX (`contact_id`),
    INDEX (`date`),

    FOREIGN KEY (`contact_id`)
        REFERENCES `contacts` (`id`)
        ON DELETE SET NULL,

    PRIMARY KEY (`id`)
);

CREATE TABLE `ledgers`
(
    `id`              INT UNSIGNED,
    `date`            DATE           NOT NULL,
    `account_no`      VARCHAR(128)   NOT NULL,
    `offset_no`       VARCHAR(128)   NOT NULL,
    `amount`          DECIMAL(10, 2) NOT NULL,
    `description`     VARCHAR(512),
    `reference`       VARCHAR(512),
    `canceled`        BOOL,
    `canceled_reason` VARCHAR(512),
    `closed`          BOOL,

    INDEX (`account_no`),
    INDEX (`offset_no`),
    INDEX (`date`),

    FOREIGN KEY (`account_no`)
        REFERENCES `accounts` (`no`)
        ON DELETE RESTRICT,

    FOREIGN KEY (`offset_no`)
        REFERENCES `accounts` (`no`)
        ON DELETE RESTRICT,

    PRIMARY KEY (`id`)
);

CREATE TABLE `ledgers_x_invoices`
(
    `ledger_id`  INT UNSIGNED,
    `invoice_id` INT UNSIGNED,

    INDEX (`ledger_id`),
    INDEX (`invoice_id`),

    FOREIGN KEY (`ledger_id`)
        REFERENCES `ledgers` (`id`)
        ON DELETE RESTRICT,
    FOREIGN KEY (`invoice_id`)
        REFERENCES `invoices` (`id`)
        ON DELETE RESTRICT,

    PRIMARY KEY (`ledger_id`, `invoice_id`)
);

CREATE TABLE `numbers`
(
    `no`        VARCHAR(512),
    `increment` INT UNSIGNED,
    PRIMARY KEY (`no`)
);
