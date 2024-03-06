<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Http\Requests\ToornamentRankTeam;
use App\Http\Requests\ToornamentUniqueDivision;
use App\Http\Requests\ToornamentAllMatchDivision;

class ToornamentController extends Controller
{
    public function getMatches(){
        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'matches=0-99'
        ])->get("https://api.toornament.com/organizer/v2/matches", [
            'tournament_ids' => env("TOORNAMENT_ID_S2"),
        ]);

        if($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getGroups(){
        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'groups=0-49'
        ])->get("https://api.toornament.com/organizer/v2/groups", [
            'tournament_ids' => env("TOORNAMENT_ID_S2"),
        ]);

        if($response->successful()) {
            $groups = $response->json();

            // Filter out objects where the name property starts with "Division"
            $filteredData = array_filter($groups, function ($item) {
                return strpos($item['name'], 'Division') === 0;
            });

            // Reindex the array
            $filteredData = array_values($filteredData);

            // Convert the filtered array to JSON
            $jsonFilteredData = json_encode($filteredData, JSON_PRETTY_PRINT);

            return $jsonFilteredData;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getRank(ToornamentRankTeam $request){
        $validated = $request->validated();
        $tournamentId = $validated['tournament_ids'];
        $stageId = $validated['stage_ids'];
        $groupId = $validated['group_ids'];

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'items=0-49'
        ])->get("https://api.toornament.com/organizer/v2/ranking-items", [
            'tournament_ids' => $tournamentId,
            "stage_ids" => $stageId,
            "group_ids" => $groupId
        ]);

        if($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getUniqueDivision(ToornamentUniqueDivision $request){
        $validated = $request->validated();
        $stageId = $validated['stage_ids'];

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'items=0-49'
        ])->get("https://api.toornament.com/organizer/v2/ranking-items", [
            'tournament_ids' => env("TOORNAMENT_ID_S2"),
            "stage_ids" => $stageId,
        ]);

        if($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }        
    }

    public function getAllMatchFromDivision(ToornamentAllMatchDivision $request){
        $validated = $request->validated();
        $stageId = $validated['stage_ids'];

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'matches=0-99'
        ])->get("https://api.toornament.com/organizer/v2/matches", [
            'tournament_ids' => env("TOORNAMENT_ID_S2"),
            "stage_ids" => $stageId,
        ]);

        if($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }
}
