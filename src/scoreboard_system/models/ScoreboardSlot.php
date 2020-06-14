<?php

namespace scoreboard_system\models;


class ScoreboardSlot
{
    private $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    static public function sideBar() : ScoreboardSlot {
        return new ScoreboardSlot("sidebar");
    }

    static public function list() : ScoreboardSlot {
        return new ScoreboardSlot("list");
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }
}