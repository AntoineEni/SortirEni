<?php


namespace App\Service;


class StateEnum
{
    const STATE_CREATE = 100;
    const STATE_OPEN = 200;
    const STATE_CLOSE = 300;
    const STATE_RUNNING = 400;
    const STATE_FINISHED = 500;
    const STATE_ARCHIVED = 600;
    const STATE_CANCELED = 700;

    public static function canCancel() {
        return array(self::STATE_OPEN, self::STATE_CLOSE);
    }
}