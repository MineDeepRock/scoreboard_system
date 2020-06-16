<?php

namespace scoreboard_system\models;


use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;

class Scoreboard
{
    /**
     * @var ScoreboardSlot
     */
    static protected $slot;
    /**
     * @var string
     */
    static protected $title;
    /**
     * @var ScoreSortType
     */
    static protected $sortType;

    /**
     * @var Score[]
     */
    static protected $scores;

    public function __construct(ScoreboardSlot $slot, string $title, array $scores, ScoreSortType $sortType) {
        self::$slot = $slot;
        self::$title = $title;
        self::$sortType = $sortType;
        self::$scores = $scores;
    }

    protected static function __create(ScoreboardSlot $slot, string $title, array $scores, ScoreSortType $sortType): Scoreboard {
        return new Scoreboard($slot, $title, $scores, $sortType);
    }

    static function __send(Player $player, Scoreboard $scoreboard): void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = $scoreboard->getSlot()->getText();
        $pk->objectiveName = $scoreboard->getSlot()->getText();
        $pk->displayName = $scoreboard->getTitle();
        $pk->criteriaName = "dummy";
        $pk->sortOrder = $scoreboard->getSortType()->getValue();
        $player->sendDataPacket($pk);

        foreach ($scoreboard->getScores() as $score) {
            self::addScore($player, $score->getText(), $score->getValue(), $score->getId());
        }
    }

    static function __update(Player $player, Scoreboard $scoreboard) {
        self::delete($player);
        self::__send($player, $scoreboard);
    }

    static function delete(Player $player): void {
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = self::$slot->getText();
        $player->sendDataPacket($pk);
    }

    static function addScore(Player $player, string $text, int $value, int $id): void {
        $score = new Score(self::$slot, $text, $value, $id);
        self::$scores[] = $score;

        $entry = new ScorePacketEntry();
        $entry->objectiveName = $score->getSlot()->getText();
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $score->getText();
        $entry->score = $score->getValue();
        $entry->scoreboardId = $score->getId();

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
    }

    static function deleteScore(Player $player, int $targetId): void {
        /** @var Score $targetScore */

        $tempScores = self::$scores;
        self::$scores = [];
        foreach ($tempScores as $tempScore) {
            if ($tempScore->getId() !== $targetId) {
                self::$scores[] = $tempScore;
            }
        }

        $entry = new ScorePacketEntry();
        $entry->objectiveName = self::$slot->getText();
        $entry->scoreboardId = $targetId;

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_REMOVE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
    }

    static function updateScore(Player $player, string $text, int $value, int $id) {
        self::deleteScore($player, $id);
        self::addScore($player, $text, $value, $id);
    }

    /**
     * @return ScoreboardSlot
     */
    public function getSlot(): ScoreboardSlot {
        return self::$slot;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return self::$title;
    }

    /**
     * @return ScoreSortType
     */
    public function getSortType(): ScoreSortType {
        return self::$sortType;
    }

    /**
     * @return Score[]
     */
    public function getScores(): array {
        return self::$scores;
    }
}