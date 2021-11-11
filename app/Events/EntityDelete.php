<?php

    namespace App\Events;

    use Illuminate\Queue\SerializesModels;

    class EntityDelete
    {

        use SerializesModels;

        public $entity;

        public function __construct($entity)
        {
            $this->entity = $entity;
        }

    }