<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

use App\Http\Requests\FullwipeTeamMatch;
use App\Http\Requests\FullwipeGroupName;
use App\Http\Requests\FullwipeRoundName;

class FullwipeController extends Controller
{
    public function getTeamMatch(FullwipeTeamMatch $request)
    {
        $validated = $request->validated();
        $teamId = $validated['team_id'];

        $cache = Redis::get('fullwipematch' . $teamId);

        if($cache){
            return response()->json(json_decode($cache));
         }

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'matches=0-99',
            'Accept' => 'application/json'
        ])->get("https://api.toornament.com/organizer/v2/matches", [
                    'tournament_ids' => env('TOORNAMENT_FULLWIPE'),
                    'participant_ids' => $teamId
                ]);

        if ($response->successful()) {
            $matches = $response->json();

            Redis::set('fullwipematch' . $teamId, json_encode($matches));
            Redis::expire('fullwipematch' . $teamId, 300);

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Something went wrong with the API call.";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getGroupName(FullwipeGroupName $request)
    {
        $validated = $request->validated();
        $groupId = $validated['group_id'];

        $cache = Redis::get('fullwipe_group_name' . $groupId);

        if($cache){
            return response()->json($cache);
        }

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
        ])->get("https://api.toornament.com/organizer/v2/groups/$groupId");

        if ($response->successful()) {
            $group = $response->json();
            $groupName = $group['name'];

            
            Redis::set('fullwipe_group_name' . $groupId, $groupName);
            Redis::expire('fullwipe_group_name' . $groupId, 300);

            return response()->json($groupName);
        } else {
            $errorMessage = $response->json()["message"] ?? "Something went wrong with the API call.";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getRoundName(FullwipeRoundName $request)
    {

        $validated = $request->validated();
        $roundId = $validated['round_id'];

        $cache = Redis::get('fullwipe_round_name' . $roundId);

        if($cache){
            return response()->json($cache);
        }

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
        ])->get("https://api.toornament.com/organizer/v2/rounds/$roundId");

        if ($response->successful()) {
            $round = $response->json();

            $roundName = $round['name'];

            Redis::set('fullwipe_round_name' . $roundId, $roundName);
            Redis::expire('fullwipe_round_name' . $roundId, 300);

            return response()->json($roundName);
        } else {
            $errorMessage = $response->json()["message"] ?? "Something went wrong with the API call.";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }
}
