<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use App\Models\TopPos;
use Illuminate\Support\Facades\Http;

class TopPosController extends Controller
{
    public function index(ApiRequest $request)
    {
        $rows = TopPos::where('date', '=', $request->date)
            ->whereNull('parentCategory')
            ->get();

        if ($rows->isNotEmpty()){
            $data = [];
            foreach ($rows as $row) {
                $data[$row->category] = $row->position;
            }

            $result = [
                'status_code' => 200,
                'message' => 'ok',
                'data' => $data
            ];

        } else {
            $url = 'https://api.apptica.com/package/top_history/1421444/1?date_from=' . $request->date . '&date_to=' . $request->date . '&B4NKGg=fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l';
            $apiResponse = Http::get($url);
            if($apiResponse['status_code'] == 200){
                foreach ($apiResponse['data'] as $key => $cat) {

                    $top = NULL;

                    foreach ($cat as $k => $subcat) {
                        TopPos::create(['category' => $k, 'parentCategory' => $key, 'position' => $subcat[$request->date], 'date' => $request->date]);
                        $top = $top ?? $subcat[$request->date];
                        if ($top > $subcat[$request->date]){
                            $top = $subcat[$request->date];
                        }
                    }

                    TopPos::create(['category' => $key, 'position' => $top, 'date' => $request->date]);
                    $data[$key] = $top;
                }

                $result = [
                    'status_code' => 200,
                    'message' => 'ok',
                    'data' => $data,
                ];

            } else {
                $result = $apiResponse->json();
            }

        }


        return response()->json($result);
    }
}
