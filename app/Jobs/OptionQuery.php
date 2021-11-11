<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Matrix\Exception;

class OptionQuery implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $query;

    public function __construct($query = NULL)
    {
        $this->query = $query;
    }

    public function handle()
    {
        try {
            $_exists = DB::table('shop_param_items_count')
                ->where('alias', $this->query['alias'])
                ->exists();
            if ($_exists == FALSE) {
                $_option_count = collect(DB::select(DB::raw($this->query['query'])))->first()->count ?? 0;
                DB::table('shop_param_items_count')
                    ->insert([
                        'alias'       => $this->query['alias'],
                        'query'       => $this->query['query'],
                        'category'    => $this->query['category'],
                        'count'       => $_option_count,
                        'recalculate' => 0
                    ]);
            }
        } catch (Exception $exception) {
        }
    }

}
