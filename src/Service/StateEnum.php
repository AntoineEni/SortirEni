<?php


namespace App\Service;

/**
 * Contains enum of all possible event state
 * Class StateEnum
 * @package App\Service
 */
class StateEnum
{
    const STATE_CREATE = 100;
    const STATE_OPEN = 200;
    const STATE_CLOSE = 300;
    const STATE_RUNNING = 400;
    const STATE_FINISHED = 500;
    const STATE_ARCHIVED = 600;
    const STATE_CANCELED = 700;

    /**
     * Get all state which allow an user to cancel an event
     * @return array
     */
    public static function canCancel() {
        return array(self::STATE_OPEN, self::STATE_CLOSE);
    }

    /**
     * Get all event state
     * @return array
     */
    public static function arrayEnumState(){
        return array(
            self::STATE_CREATE,
            self::STATE_ARCHIVED,
            self::STATE_CANCELED,
            self::STATE_RUNNING,
            self::STATE_FINISHED,
            self::STATE_OPEN,
            self::STATE_CLOSE,
        );
    }
}