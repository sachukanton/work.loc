<?php

    namespace App\Models\Components;

    use Illuminate\Database\Eloquent\Model;

    class QueueDone extends Model
    {

        protected $primaryKey = 'key';
        protected $table = 'queue_done';
        protected $guarded = [];
        public $timestamps = FALSE;

        public static function callback($type)
        {
            $_response = [
                'added'     => NULL,
                'updated'   => NULL,
                'not_added' => NULL
            ];
            $_item = QueueDone::where('key', $type)
                ->first();
            if ($_item) {
                $_response['added'] = $_item['added'];
                $_response['updated'] = $_item['updated'];
                $_response['not_added'] = $_item['not_added'];
                $_item->update([
                    'added'     => 0,
                    'updated'   => 0,
                    'not_added' => 0,
                ]);
            }

            return $_response;
        }

    }