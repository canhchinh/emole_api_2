<?php


namespace App\Helpers;


trait Traits
{
    /** Helper $instance **/
    private static $instance = null;

    /**
     * @return Helper|null
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
