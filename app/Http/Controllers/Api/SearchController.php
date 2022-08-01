<?php

namespace App\Http\Controllers\Api;

use App\Actions\SearchName;
use App\Http\Controllers\Controller;
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;
use GraphQL\Variable;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function nameSearch(Request $request)
    {
        $search = new SearchName;


        if ($request->exists('scientific_name')) {
            $result = $search($request->input('scientific_name'));
            return response()->json($result);
        }
        else {
            return response()->json([
                'code' => '400',
                'message' => "query string needs to contain 'scientific_name' parameter"
            ], 400);
        }
    }
}
