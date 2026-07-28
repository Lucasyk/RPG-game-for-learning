<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Monster;
use App\Models\Player;
use App\Models\Enemy;
use App\Models\BattleSave;


class BattleController extends Controller
{
   //After login the page where they can choose what to do.
   public function index(Request $request){
        $user = $request->user();
        $player = $user->player;

        $battle = $request->session()->get('battle');

        return view("dashboard", compact("player", "battle"));
   }
   //The battle start should show the battle page
   public function show(Request $request)
{
    $player = $request->user()->player;

    if (!$player) {
        return redirect('/dashboard')->withErrors([
            'player' => "You don't have a character yet.",
        ]);
    }

    $battle = $request->session()->get('battle');

    $battleIsOutdated =
        !$battle ||
        !isset($battle['player']['level']) ||
        !isset($battle['player']['exp']) ||
        !isset($battle['player']['exp_to_next_level']) ||
        !isset($battle['enemy']['exp_reward']);

    if ($battleIsOutdated) {
        $enemy = Enemy::inRandomOrder()->firstOrFail();

        $battle = $this->createBattle($player, $enemy);

        $request->session()->put('battle', $battle);
    }

    return view('battle.show', compact('battle'));
}

   //Handling attacks   
   public function attack(Request $request){
      //Retrieve data from session
      $battle = $request->session()->get("battle");
      //If there is no battle then should throw error
      if(!$battle){
         return response()->json([
            "message"=>"There is no battle."
         ], 404);
      }
      //If the battle status is not ongoing that means battle is over so should stop here
      if ($battle['status'] !== 'ongoing') {
         return response()->json([
            'battle' => $battle,
         ]);
      }
      if (
    $battle['player']['speed'] >=
    $battle['enemy']['speed']
) {
    // Player attacks first
    $this->strike(
        $battle['player'],
        $battle['enemy'],
        $battle['log']
    );

    // Enemy can only attack if still alive
    if ($battle['enemy']['hp'] > 0) {
        $this->strike(
            $battle['enemy'],
            $battle['player'],
            $battle['log']
        );
    }
} else {
    // Enemy attacks first
    $this->strike(
        $battle['enemy'],
        $battle['player'],
        $battle['log']
    );

    // Player can only attack if still alive
    if ($battle['player']['hp'] > 0) {
        $this->strike(
            $battle['player'],
            $battle['enemy'],
            $battle['log']
        );
    }
}

if ($battle['enemy']['hp'] <= 0) {

   $this->handleVictory($request, $battle);

   $request->session()->put('battle', $battle);

   return response()->json([
        'battle' => $battle,
    ]);

} elseif ($battle['player']['hp'] <= 0) {
    $battle['status'] = 'lost';

    $battle['log'][] =
        "{$battle['player']['name']} was defeated!";
}   

         $request->session()->put('battle', $battle);

         return response()->json([
            'battle' => $battle,
         ]);

      $request->session()->put("battle", $battle);

      return response()->json([
         "battle"=>$battle,
      ]);

   }

   //End battle and reset
   public function endBattle(Request $request)
{
    $battle = $request->session()->get('battle');

    if (!$battle) {
        return response()->json([
            'message' => 'There is no battle to end.',
        ], 404);
    }

    if (($battle['status'] ?? null) === 'ongoing') {
        return response()->json([
            'message' => 'The battle is still ongoing.',
        ], 422);
    }

    $result = $battle['status'] ?? 'unknown';

    $request->session()->forget('battle');

    return response()->json([
        'message' => 'Battle ended successfully.',
        'result' => $result,
        'redirect' => route('dashboard'),
    ]);
}

   private function createBattle(Player $player, Enemy $enemy):array
   {
      $level = (int) ($player->level ?? 1);
      $exp = (int) ($player->exp ?? 0);
      return [
         "player" => [
            "id"=>$player->id,
            "name"=>$player->name,
            "character_class"=>$player->character_class,
            "icon"=>$player->icon,
            'level' => $level,
            'exp' => $exp,
            'exp_to_next_level' => $this->expRequired($player->level),
            "hp"=>$player->max_hp,
            "max_hp"=>$player->max_hp,
            "max_mp"=>$player->max_mp,
            "mp"=>$player->max_mp,
            "attack"=>$player->attack,
            "defense"=>$player->defense,
            "speed"=>$player->speed,
            "fire_res"=>$player->fire_res,
            "water_res"=>$player->water_res,
            "electric_res"=>$player->electric_res,
         ],
         "enemy" => [
            "id"=>$enemy->id,
            "name"=>$enemy->name,
            "max_hp"=>$enemy->max_hp,
            "hp"=>$enemy->max_hp,
            "max_mp"=>$enemy->max_mp,
            "mp"=>$enemy->max_mp,
            "attack"=>$enemy->attack,
            "defense"=>$enemy->defense,
            "speed"=>$enemy->speed,
            "fire_res"=>$enemy->fire_res,
            "water_res"=>$enemy->water_res,
            "electric_res"=>$enemy->electric_res,
            "exp_reward"=>$enemy->exp_reward,
         ],
         "status"=>"ongoing",
         "log"=>[
            "{$player->name} encountered {$enemy->name}..."
         ]
      ];
   }
   
   private function expRequired(int $level){
      return  $level * 100;
   }

   public function grantExp(
    Player $player,
    int $gainedExp
): array {
    $player->level = (int) ($player->level ?? 1);
    $player->exp = (int) ($player->exp ?? 0);

    $oldLevel = $player->level;
    $levelsGained = 0;

    $player->exp += $gainedExp;

    while (
        $player->exp >= $this->expRequired($player->level)
    ) {
        $requiredExp =
            $this->expRequired($player->level);

        $player->exp -= $requiredExp;
        $player->level++;
        $levelsGained++;

        $player->max_hp += 5;
        $player->max_mp += 2;
        $player->attack += 2;
        $player->defense += 2;
        $player->speed += 1;
    }

    $player->save();
    $player->refresh();

    return [
        'exp_gained' => $gainedExp,
        'old_level' => $oldLevel,
        'new_level' => $player->level,
        'levels_gained' => $levelsGained,
        'current_exp' => $player->exp,
        'exp_to_next_level' =>
            $this->expRequired($player->level),

        'stats' => [
            'max_hp' => $player->max_hp,
            'max_mp' => $player->max_mp,
            'attack' => $player->attack,
            'defense' => $player->defense,
            'speed' => $player->speed,
        ],
    ];
}

   private function strike(
           array &$attacker,
           array &$defender,
           array &$log,
       ): void {
           $damage = max(
               1,
               $attacker["attack"] - $defender["defense"]
           );  
   
           $defender["hp"] = max(
               0,
               $defender["hp"] - $damage,
           );
   
           $log[] = "{$attacker['name']} attacks {$defender['name']} for {$damage} damage.";
       }

       public function skill(Request $request)
{
    $battle = $request->session()->get('battle');

    if (!$battle) {
        return response()->json([
            'message' => 'There is no active battle.',
        ], 404);
    }

    if (($battle['status'] ?? null) !== 'ongoing') {
        return response()->json([
            'battle' => $battle,
        ]);
    }

    $characterClass =
        $battle['player']['character_class'] ?? null;

    $currentMp = (int) (
        $battle['player']['mp'] ?? 0
    );

    $mpCost = $this->skillCost($characterClass);

    if ($mpCost === null) {
        return response()->json([
            'message' => 'This class does not have a skill.',
            'battle' => $battle,
        ], 422);
    }

    if ($currentMp < $mpCost) {
        return response()->json([
            'message' => 'Not enough MP!',
            'battle' => $battle,
        ], 422);
    }

    $battle['player']['mp'] =
        $currentMp - $mpCost;

    switch ($characterClass) {
        case 'warrior':
            $this->powerStrike($battle);
            break;

        case 'mage':
            $this->fireball($battle);
            break;

        case 'rogue':
            $this->doubleSlash($battle);
            break;

        default:
            return response()->json([
                'message' => 'Unknown character class.',
                'battle' => $battle,
            ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Enemy defeated
    |--------------------------------------------------------------------------
    */

    if ($battle['enemy']['hp'] <= 0) {
        // Use the same victory and EXP logic as attack().
        $this->handleVictory($request, $battle);

        $request->session()->put('battle', $battle);

        return response()->json([
            'battle' => $battle,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Enemy retaliates
    |--------------------------------------------------------------------------
    */

    $this->strike(
        $battle['enemy'],
        $battle['player'],
        $battle['log']
    );

    if ($battle['player']['hp'] <= 0) {
        $battle['status'] = 'lost';

        $battle['log'][] =
            "{$battle['player']['name']} was defeated!";
    }

    $request->session()->put('battle', $battle);

    return response()->json([
        'battle' => $battle,
    ]);
}

       private function skillCost(?string $characterClass): ?int
       {
         return match($characterClass){
           "warrior"=>4,
           "mage"=>7,
           "rogue"=>5,
           default => null, 
         };
       }

       private function powerStrike(array &$battle): void
       {
         $damage = max(
            1,
            ($battle["player"]["attack"] * 2) - $battle["enemy"]["defense"]
         );

         $battle["enemy"]["hp"] = max(
            0,
            $battle["enemy"]["hp"] - $damage,
         );

         $battle["log"][] = "{$battle['player']['name']} used Power Strike!!";

         $battle["log"][] = "{$battle['player']['name']} strikes {$battle['enemy']['name']} for {$damage} damage!!";
       }

       private function fireball(array &$battle): void
       {
         $fireResistance = max(
            0,
            (int) ($battle["enemy"]["fire_res"] ?? 0)
         );

         $damage = max(
            1,
            ($battle["player"]["attack"] * 2) + 5 - $fireResistance
         );

         $battle["enemy"]["hp"] = max(
            0,
            $battle["enemy"]["hp"] - $damage,
         );

         $battle["log"][] = "{$battle['player']['name']} used Fireball!!";

         $battle["log"][] = "{$battle['enemy']['name']} took {$damage} fire damage!!";
       }

       private function doubleSlash(array &$battle):void
       {
         $battle['log'][] =
        "{$battle['player']['name']} used Double Slash!";

    for ($hit = 1; $hit <= 2; $hit++) {
        if ($battle['enemy']['hp'] <= 0) {
            break;
        }

        $damage = max(
            1,
            $battle['player']['attack']
                - intdiv(
                    $battle['enemy']['defense'],
                    2
                )
        );

        $battle['enemy']['hp'] = max(
            0,
            $battle['enemy']['hp'] - $damage
        );

        $battle['log'][] =
            "Hit {$hit} dealt {$damage} damage!";

       }
}

private function handleVictory(
    Request $request,
    array &$battle
): void {
    $battle['status'] = 'won';

    $battle['log'][] =
        "{$battle['enemy']['name']} was defeated!";

    $player = $request->user()->player;

    $expReward = (int) (
        $battle['enemy']['exp_reward'] ?? 10
    );

    $rewards = $this->grantExp(
        $player,
        $expReward
    );

    $battle['rewards'] = $rewards;

    $battle['player']['level'] =
        $rewards['new_level'];

    $battle['player']['exp'] =
        $rewards['current_exp'];

    $battle['player']['exp_to_next_level'] =
        $rewards['exp_to_next_level'];

    if (isset($rewards['stats'])) {
        foreach (
            ['max_hp', 'max_mp', 'attack', 'defense', 'speed']
            as $stat
        ) {
            $battle['player'][$stat] =
                $rewards['stats'][$stat];
        }
    }

    $battle['log'][] =
        "{$player->name} gained {$expReward} EXP!";

    if ($rewards['levels_gained'] > 0) {
        $battle['log'][] =
            "{$player->name} reached level {$rewards['new_level']}!";
    }
}
}
