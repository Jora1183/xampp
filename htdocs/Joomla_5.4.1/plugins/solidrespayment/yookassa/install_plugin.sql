-- Manual YooKassa Plugin Installation SQL Script
-- Execute this in phpMyAdmin or MySQL command line

-- Insert the plugin into extensions table
INSERT INTO `jos_extensions` (
    `package_id`,
    `name`,
    `type`,
    `element`,
    `folder`,
    `client_id`,
    `enabled`,
    `access`,
    `protected`,
    `locked`,
    `manifest_cache`,
    `params`,
    `custom_data`,
    `checked_out`,
    `checked_out_time`,
    `ordering`,
    `state`
) VALUES (
    0,
    'plg_solidrespayment_yookassa',
    'plugin',
    'yookassa',
    'solidrespayment',
    0,
    1,
    1,
    0,
    0,
    '{"name":"plg_solidrespayment_yookassa","type":"plugin","creationDate":"January 2026","author":"Custom Development","copyright":"Copyright (C) 2026. All Rights Reserved.","authorEmail":"contact@example.com","authorUrl":"https:\\/\\/example.com","version":"1.0.0","description":"PLG_SOLIDRESPAYMENT_YOOKASSA_XML_DESCRIPTION","group":"","filename":"yookassa"}',
    '{}',
    '',
    0,
    NULL,
    0,
    0
);

-- Get the extension_id that was just inserted
SELECT LAST_INSERT_ID() as extension_id;
