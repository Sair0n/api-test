<?php

namespace App\Console\Commands;

use App\Models\TopPos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class CollectTopHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:top-history {dateFrom} {dateTo?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect Application Top Category Positions from apptica.com';


    public function handle()
    {
        if ($this->validateInput($this->argument('dateFrom'))){
            $url = 'https://api.apptica.com/package/top_history/1421444/1?date_from=' . $this->argument('dateFrom') . '&date_to=';
        } else {
            return;
        }

        if($this->argument('dateTo')){
            if ($this->validateInput($this->argument('dateTo'))){
                $url .=  $this->argument('dateTo') . '&B4NKGg=fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l';
            } else {
                return;
            }
        } else {
            $url .=  $this->argument('dateFrom') . '&B4NKGg=fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l';
        }


        $apiResponse = Http::get($url);
        if($apiResponse['status_code'] == 200){
            foreach ($apiResponse['data'] as $cat => $value) {

                $period = [];

                foreach ($value as $subcat => $v) {

                    foreach ($v as $day => $pos) {
                        if (is_null($pos)) continue;
                        if (empty($period[$day])){
                            $period[$day] = $pos;
                        }
                        if ($period[$day] > $pos){
                            $period[$day] = $pos;
                        }
                        TopPos::firstOrCreate(['category' => $subcat, 'parentCategory' => $cat, 'position' => $pos, 'date' => $day]);
                    }
                }

                foreach ($period as $day => $position) {
                    if (is_null($position)) continue;
                    TopPos::firstOrCreate(['category' => $cat, 'parentCategory' => NULL, 'position' => $position, 'date' => $day]);
                }

            }
        } else {
            $this->error('Request failed');
            return;
        }


        $this->info('Success ' . $this->argument('dateFrom') . ' - ' .  $this->argument('dateTo'));
    }


    public function validateInput($value)
    {
        $validator = Validator::make(['date' => $value], ['date' => 'required|date_format:Y-m-d']);
        if ($validator->fails()) {
            $this->error($validator->errors());
            return false;
        }else{
            return true;
        }
    }

}
