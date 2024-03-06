<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use App\Http\Requests\FullwipeTeamMatch;
use App\Http\Requests\FullwipeGroupName;
use App\Http\Requests\FullwipeRoundName;

class FullwipeController extends Controller
{
    public function getTeamMatch(FullwipeTeamMatch $request)
    {
        $validated = $request->validated();
        $teamId = $validated['team_id'];

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

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
        ])->get("https://api.toornament.com/organizer/v2/groups/$groupId");

        if ($response->successful()) {
            $group = $response->json();
            $groupName = $group['name'];

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

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
        ])->get("https://api.toornament.com/organizer/v2/rounds/$roundId");

        if ($response->successful()) {
            $round = $response->json();

            $roundName = $round['name'];

            return response()->json($roundName);
        } else {
            $errorMessage = $response->json()["message"] ?? "Something went wrong with the API call.";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }
}
