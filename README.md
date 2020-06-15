# ScoreboardSystem
スコアボードを簡単に実装できます

```php
use scoreboard_system\ScoreboardSystem;
use scoreboard_system\models\Score;
use scoreboard_system\models\Scoreboard;
use scoreboard_system\models\ScoreboardSlot;
use scoreboard_system\models\ScoreSortType;

class LobbyScoreboard extends Scoreboard
{
    public function __construct(int $count) {
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, "=========", 0, 0),
            new Score($slot, "参加人数:" . $count, 1, 1),
        ];
        parent::__construct($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge());
    }
}

ScoreboardSystem::setScoreboard($player, new LobbyScoreboard($count));
```
