<?php


namespace scoreboard_system\models;


class ScoreSortType
{
    private $value;

    public function __construct(int $value) {
        $this->value = $value;
    }

    static function smallToLarge() : ScoreSortType {
        return new ScoreSortType(0);
    }

    static function largeToSmall() : ScoreSortType {
        return new ScoreSortType(1);
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }
}