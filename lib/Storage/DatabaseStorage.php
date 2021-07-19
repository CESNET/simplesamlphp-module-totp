<?php

declare(strict_types=1);

/**
 * Database storage for module totp.
 */

namespace SimpleSAML\Module\totp\Storage;

use SimpleSAML\Configuration;
use SimpleSAML\Database;
use SimpleSAML\Module\totp\Storage;

class DatabaseStorage implements Storage
{
    protected const CONFIG_FILE = 'module_totp.php';

    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance(
            Configuration::getOptionalConfig(self::CONFIG_FILE)->getConfigItem('PerunStorage', [])
        );
    }

    public function store($userId, $secret, $label = '')
    {
        $this->db->write(
            'INSERT INTO AttributeFromSQLUnique (uid,attribute,value) '
            . 'VALUES (:uid,:attribute,:value)',
            [
                'uid' => $userId,
                'attribute' => 'totp_secret',
                'value' => $secret,
            ]
        );
    }
}
