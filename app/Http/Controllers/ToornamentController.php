<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

use App\Http\Requests\ToornamentRankTeam;
use App\Http\Requests\ToornamentUniqueDivision;
use App\Http\Requests\ToornamentAllMatchDivision;

class ToornamentController extends Controller
{
    public function getMatches()
    {
        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'matches=0-99'
        ])->get("https://api.toornament.com/organizer/v2/matches", [
                    'tournament_ids' => env("TOORNAMENT_ID_S2"),
                ]);

        if ($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getGroups()
    {
        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'groups=0-49'
        ])->get("https://api.toornament.com/organizer/v2/groups", [
                    'tournament_ids' => env("TOORNAMENT_ID_S2"),
                ]);

        if ($response->successful()) {
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

    public function getRank(ToornamentRankTeam $request)
    {
        $validated = $request->validated();
        $tournamentId = $validated['tournament_ids'];
        $stageId = $validated['stage_ids'];
        $groupId = $validated['group_ids'];

        $cache = Redis::get('rank' . $stageId);

        if ($cache) {
            return response()->json(json_decode($cache));
        }

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'items=0-49'
        ])->get("https://api.toornament.com/organizer/v2/ranking-items", [
                    'tournament_ids' => $tournamentId,
                    "stage_ids" => $stageId,
                    "group_ids" => $groupId
                ]);

        if ($response->successful()) {
            $matches = $response->json();

            Redis::set('rank' . $stageId, json_encode($matches));
            Redis::expire('rank' . $stageId, 43200);

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getUniqueDivision(ToornamentUniqueDivision $request)
    {
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

        if ($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getAllMatchFromDivision(ToornamentAllMatchDivision $request)
    {
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

        if ($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getComingMatches()
    {
        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'matches=0-99'
        ])->get("https://api.toornament.com/organizer/v2/matches", [
                    'tournament_ids' => env("TOORNAMENT_ID_S2"),
                    'statuses' => 'pending',
                    'is_scheduled' => '1',
                    'sort' => 'schedule'
                ]);

        if ($response->successful()) {
            $matches = $response->json();

            return $matches;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }

    public function getStreamMatch(Request $request)
    {
        $cache = Redis::get('streamMatch');

        if ($cache) {
            return response()->json(json_decode($cache));
        }
        $jsonData = $request->json()->all();

        $streamArray = [];

        foreach ($jsonData as $item) {
            $response = Http::withHeaders([
                'X-Api-Key' => env('TOORNAMENT_API_KEY'),
                'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            ])->get("https://api.toornament.com/organizer/v2/matches/{$item['match_id']}/streams");

            if ($response->successful()) {
                array_push($streamArray, $response->json());
            }

        }

        $streamContent = [];
        foreach ($streamArray as $index => $stream) {
            if (is_array($stream) && !empty ($stream)) {
                // Here you can add your logic for processing non-empty arrays
                // For demonstration, let's assume you're making a new request using the first item in the stream array
                $streamQuery = Http::withHeaders([
                    'X-Api-Key' => env('TOORNAMENT_API_KEY'),
                    'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
                ])->get("https://api.toornament.com/organizer/v2/streams/{$stream[0]}");

                // Process the new response
                if ($streamQuery->successful()) {
                    $newData = $streamQuery->json();

                    // Push the result into $newArray along with the index
                    $streamContent[] = [
                        'result' => $newData,
                        'match' => $jsonData[$index]
                    ];
                }
            }
        }

        Redis::set('streamMatch', json_encode($streamContent));
        Redis::expire('streamMatch', 3600);

        return response()->json($streamContent, $response->status());
    }

    public function getMatchesVideo(Request $request)
    {
        $matchIds = $request->input('match_ids');

        $response = Http::withHeaders([
            'X-Api-Key' => env('TOORNAMENT_API_KEY'),
            'Authorization' => env('TOORNAMENT_ACCESS_TOKEN'),
            'Range' => 'videos=0-49'
        ])->get("https://api.toornament.com/organizer/v2/videos", [
                    'tournament_ids' => env("TOORNAMENT_ID_S2"),
                    'match_ids' => $matchIds,
                    'category' => 'replay'
                ]);

        if ($response->successful()) {
            $vod = $response->json();

            return $vod;
        } else {
            $errorMessage = $response->json()["message"] ?? "Une erreur API est survenue";

            return response()->json(['error' => $errorMessage], $response->status());
        }
    }
}
