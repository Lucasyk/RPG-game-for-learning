<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monster;
use App\Models\Player;
use App\Models\Enemy;


class BattleController extends Controller
{
   //After login the page where they can choose what to do.
   public function index(Request $request){
        $user = $request->user();
        $player = $user->player;

        return view("dashboard", compact("player"));
   }
   //The battle start should show the battle page
   public function show(Request $request){
      $player = $request->user()->player;
      $enemy = Enemy::inRandomOrder()->firstOrFail();

      if(!$player){
         return redirect("/dashboard")->withErrors(["player"=>"You don't have character yet...."]);
      }

      $battle = $request->session()->get("battle");

      if(!$battle){
         $battle = $this->createBattle($player, $enemy);

         $request->session()->put("battle", $battle);
      }

    return view("battle.show", compact("battle"));
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
      if($battle["status"] !== "ongoing"){
         return response()->json($battle);
      }
      //Player attack calculation
      $playerDamage = max(1, $battle["player"]["attack"] - $battle["enemy"]["defense"]);
      $battle["enemy"]["hp"] = max(0, $battle["enemy"]["hp"] - $playerDamage);
      $battle["log"][] = "The {$battle['player']['name']} dealt {$playerDamage} to {$battle['enemy']['name']} ";
      //Check if monster is defeated
      if ($battle['enemy']['hp'] <= 0) {
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

         $battle['log'][] =
            "{$player->name} gained {$expReward} EXP!";

         if ($rewards['levels_gained'] > 0) {
            $battle['log'][] =
                  "{$player->name} reached level {$rewards['new_level']}!";
         }

         $request->session()->put('battle', $battle);

         return response()->json([
            'battle' => $battle,
         ]);
}

      //Enemy turn
      $enemyDamage = max(1, $battle["enemy"]["attack"] - $battle["player"]["defense"]);
      $battle["player"]["hp"] = max(0, $battle["player"]["hp"] - $enemyDamage);
      $battle["log"][] = "The {$battle['enemy']['name']} dealt {$enemyDamage} damage!!!";

      if($battle["player"]["hp"] <= 0){
         $battle["status"] = "lost";
         $battle["log"][] = "{$battle['player']['name']} was defeated!!";
      };

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
      return [
         "player" => [
            "id"=>$player->id,
            "name"=>$player->name,
            "character_class"=>$player->character_class,
            "icon"=>$player->icon,
            'level' => $player->level,
            'exp' => $player->exp,
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
   public function grantExp(Player $player, int $gainedExp){
    $oldLevel = $player->level;
    $levelsGained = 0;

    $player->exp += $gainedExp;

    while (
        $player->exp >= $this->expRequired($player->level)
    ) {
        $requiredExp = $this->expRequired($player->level);

        $player->exp -= $requiredExp;
        $player->level++;
        $levelsGained++;

        // Stat increases for each level
        $player->max_hp += 5;
        $player->max_mp += 2;
        $player->attack += 2;
        $player->defense += 2;
        $player->speed += 1;
    }

    $player->save();

    return [
        'exp_gained' => $gainedExp,
        'old_level' => $oldLevel,
        'new_level' => $player->level,
        'levels_gained' => $levelsGained,
        'current_exp' => $player->exp,
        'exp_to_next_level' => $this->expRequired(
            $player->level
        ),
    ];
   }
}

