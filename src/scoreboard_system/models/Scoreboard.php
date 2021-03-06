<?php

namespace scoreboard_system\models;


use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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

    //setting
    static private $autoIndex;

    public function __construct(ScoreboardSlot $slot, string $title, array $scores, ScoreSortType $sortType, bool $autoIndex = true) {
        self::$slot = $slot;
        self::$title = $title;
        self::$sortType = $sortType;
        self::$scores = $scores;
        self::$autoIndex = $autoIndex;
    }

    protected static function __create(ScoreboardSlot $slot, string $title, array $scores, ScoreSortType $sortType, bool $autoIndex = true): Scoreboard {
        return new Scoreboard($slot, $title, $scores, $sortType, $autoIndex);
    }

    static function __send(Player $player, Scoreboard $scoreboard): void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = $scoreboard->getSlot()->getText();
        $pk->objectiveName = $scoreboard->getSlot()->getText();
        $pk->displayName = $scoreboard->getTitle();
        $pk->criteriaName = "dummy";
        $pk->sortOrder = $scoreboard->getSortType()->getValue();
        $player->sendDataPacket($pk);

        foreach ($scoreboard->getScores() as $index => $score) {
            if (self::$autoIndex) {
                $score = new Score($score->getSlot(), $score->getText(), $index, $index);
            }

            self::addScore($player, $score);
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

    static function addScore(Player $player, Score $score): void {
        self::$scores[] = $score;

        $entry = new ScorePacketEntry();
        $entry->objectiveName = $score->getSlot()->getText();
        $entry->type = $entry::TYPE_FAKE_PLAYER;

        if (self::hasSameTextScore($score->getText())) {
            $entry->customName = $score->getText() . str_repeat(TextFormat::RESET, self::countSameTextScore($score->getText()));
        } else {
            $entry->customName = $score->getText();
        }

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

    static function updateScore(Player $player, Score $score) {
        self::deleteScore($player, $score->getId());
        self::addScore($player, $score);
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

    private static function hasSameTextScore(string $text): bool {
        foreach (self::$scores as $score) {
            if ($score->getText() === $text) {
                return true;
            }
        }

        return false;
    }

    private static function countSameTextScore(string $text): int {

        $count = 0;

        foreach (self::$scores as $score) {
            if ($score->getText() === $text) {
                $count++;
            }
        }

        return $count;
    }
}