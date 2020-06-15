<?php


namespace scoreboard_system;


use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use scoreboard_system\models\Score;
use scoreboard_system\models\Scoreboard;
use scoreboard_system\models\ScoreboardSlot;

class ScoreboardSystem
{

    static public function setScoreboard(Player $player, Scoreboard $scoreboard): void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = $scoreboard->getSlot()->getText();
        $pk->objectiveName = $scoreboard->getSlot()->getText();
        $pk->displayName = $scoreboard->getTitle();
        $pk->criteriaName = "dummy";
        $pk->sortOrder = intval($scoreboard->getSort());
        $player->sendDataPacket($pk);

        foreach ($scoreboard->getScores() as $score) {
            self::setScore($player, $score);
        }
    }

    static public function deleteScoreboard(Player $player, ScoreboardSlot $slot): void {
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $slot->getText();
        $player->sendDataPacket($pk);
    }

    static public function updateScoreboard(Player $player, Scoreboard $scoreboard): void {
        self::deleteScoreboard($player, $scoreboard->getSlot());
        self::setScoreboard($player, $scoreboard);
    }

    static public function setScore(Player $player, Score $score): void {
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

    static public function deleteScore(Player $player, Score $score): void {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $score->getSlot()->getText();
        $entry->scoreboardId = $score->getId();

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_REMOVE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
    }

    static public function updateScore(Player $player, Score $score) {
        self::deleteScore($player, $score);
        self::setScore($player, $score);
    }
}