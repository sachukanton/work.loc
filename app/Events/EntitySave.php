<?php

    namespace App\Events;

    use Illuminate\Queue\SerializesModels;

    class EntitySave
    {

        use SerializesModels;

        public $entity;

        public function __construct($entity)
        {
            $this->entity = $entity;
        }

    }