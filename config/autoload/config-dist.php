<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

return [
    'db' => [
        'username' => '',
        'password' => '',
        'dsn'      => 'mysql:dbname=;host=127.0.0.1',
    ],
    'api' => [
        'fornite' => [
            'url' => 'https://api.fortnitetracker.com/v1/profile/pc',
            'key' => '',
        ]
    ],
    'version' => [
        'css' => 1,
        'js'  => 1,
    ],
    'salt'    => '',
    'baseUrl' => '',
];
