<?php

namespace scoreboard_system\models;


class Scoreboard
{
    /**
     * @var ScoreboardSlot
     */
    protected $slot;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var ScoreSortType
     */
    protected $sortType;

    /**
     * @var Score[]
     */
    private $scores;

    public function __construct(ScoreboardSlot $slot, string $title, array $scores, ScoreSortType $sortType) {
        $this->slot = $slot;
        $this->title = $title;
        $this->sortType = $sortType;
        $this->scores = $scores;
    }

    /**
     * @return ScoreboardSlot
     */
    public function getSlot(): ScoreboardSlot {
        return $this->slot;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @return ScoreSortType
     */
    public function getSortType(): ScoreSortType {
        return $this->sortType;
    }

    /**
     * @return Score[]
     */
    public function getScores(): array {
        return $this->scores;
    }
}