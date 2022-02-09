<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Crypt
{
    /**
     * Get salt
     * @return bool|string
     */
    public function salt()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    /**
     * Generate password
     * @param $password
     * @param $salt
     * @return string
     */
    public function generate_password($password, $salt)
    {
        return crypt($password, '$2a$10$' . $salt);
    }

    /**
     * Check password
     * @param $hash
     * @param $password
     * @return bool
     */
    public function check_password($hash, $password)
    {
        $newHash = crypt($password, $hash);

        return ($hash == $newHash);
    }
}
