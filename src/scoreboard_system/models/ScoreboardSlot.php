<?php

namespace scoreboard_system\models;


class ScoreboardSlot
{
    private $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    static function sideBar() : ScoreboardSlot {
        return new ScoreboardSlot("sidebar");
    }

    static function list() : ScoreboardSlot {
        return new ScoreboardSlot("list");
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }
}