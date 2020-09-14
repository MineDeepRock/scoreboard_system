<?php


namespace scoreboard_system\models;


class Score
{
    /**
     * @var ScoreboardSlot
     */
    private $slot;
    /**
     * @var string
     */
    private $text;
    /**
     * @var int
     */
    private $value;
    /**
     * @var int
     */
    private $id;

    public function __construct(ScoreboardSlot $slot, string $text, ?int $value = null, ?int $id = null) {
        $this->slot = $slot;
        $this->text = $text;
        $this->value = $value;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return NULL|int
     */
    public function getValue(): ?int {
        return $this->value;
    }

    /**
     * @return NULL|int
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return ScoreboardSlot
     */
    public function getSlot(): ScoreboardSlot {
        return $this->slot;
    }

}