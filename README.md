# ScoreboardSystem
スコアボードを簡単に実装できます

```php
class PlayerStatusScoreboard extends Scoreboard
{
    private static function create(string $name, int $money, int $level): Scoreboard {
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, "=========", 0, 0),
            new Score($slot, "Name:" . $name, 1, 1),
            new Score($slot, "Money:" . $money, 2, 2),
            new Score($slot, "Level:" . $level, 3, 3),
        ];
        return parent::__create($slot, "Server Name", $scores, ScoreSortType::smallToLarge());
    }

    static function send(Player $player, int $money, int $level) {
        $scoreboard = self::create($player->getName(), $money, $level);
        parent::__send($player, $scoreboard);
    }

    static function update(Player $player, int $money, int $level) {
        $scoreboard = self::create($player->getName(), $money, $level);
        parent::__update($player, $scoreboard);
    }
}
/***
$player = null;
$money = 1000;
$level = 5;
PlayerStatusScoreboard::send($player, $money, $level);
$money = 2000;
PlayerStatusScoreboard::update($player, $money, $level);

PlayerStatusScoreboard::addScore($player, "ようこそ", 4, 4);
PlayerStatusScoreboard::updateScore($player, "ようこそ○○サーバーへ", 4, 4);
PlayerStatusScoreboard::deleteScore($player, 4);
***?
```