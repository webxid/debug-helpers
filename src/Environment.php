<?php

namespace WebXID\DebugHelpers;

class Environment
{
    static private $allowed_ips = null;
    static private $root_dir = null;

    #region Is Condition methods

    /**
     * @return bool
     */
    static public function isAllowedIP()
    {
        if (
            !self::$allowed_ips
            || in_array(self::getIP(), self::$allowed_ips)
        ) {
            return true;
        }

        return false;
    }

    #endregion

    #region Setters

    /**
     * @param array|string $ips_list
     */
    static public function setAllowedIPs($ips_list)
    {
        if (!$ips_list) {
            return;
        }

        self::$allowed_ips = (array) $ips_list;
    }

    /**
     * @param string $route
     */
    public static function setRootDir(string $route)
    {
        if (!$route || !is_dir($route)) {
            throw new \InvalidArgumentException('Invalid $route');
        }

        if (substr($route, -1) === '/') {
            $route = substr($route, 0, -1);
        }

        static::$root_dir = $route;
    }

    #endregion

    #region Getters

    /**
     * @return string
     */
    public static function getRootDir(): string
    {
        return static::$root_dir ?: (__DIR__ . '/..');
    }

    /**
     * @return string
     */
    static public function getIP(): string
    {
        if(isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    #endregion
}
