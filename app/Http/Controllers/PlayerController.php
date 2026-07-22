<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    public function create(Request $request){
        if($request->user()->player()->exists()){
            return redirect()->route("dashboard");
        }

        return view("create");
    }

    public function store(Request $request){
        if($request->user()->player()->exists()){
            return redirect()->route("dashboard")->withErrors(["player"=>"You already have player"]);
        }

        
        $validated = $request->validate([
            "name"=>["required", "string", "max:30"],
            "character_class"=> [
                "required",
                Rule::in(["warrior", "mage", "rogue"])
                ]
                ]);
                
                $icon = match($validated["character_class"]){
                    "warrior"=>"🛡️",
                    "mage"=>"🧙",
                    "rogue"=>"🗡️",
                    default => "👨‍💻"
                };

        $stats = match($validated["character_class"]){
            'warrior' => [
                'max_hp' => 30,
                'max_mp' => 10,
                'attack' => 10,
                'defense' => 8,
                'speed' => 3,
            ],

            'mage' => [
                'max_hp' => 18,
                'max_mp' => 25,
                'attack' => 6,
                'defense' => 4,
                'speed' => 5,
            ],

            'rogue' => [
                'max_hp' => 22,                
                'max_mp' => 16,
                'attack' => 8,
                'defense' => 5,
                'speed' => 9,
            ],
        };

        $request->user()->player()->create(
            [
                "name"=>$validated["name"],
                "character_class"=>$validated["character_class"],
                "icon"=>$icon,
                ...$stats
            ]
        );

        return redirect()->route("dashboard")->with("success", "Your character has been created!!");
    }
}
