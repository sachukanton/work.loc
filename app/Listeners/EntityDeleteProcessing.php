<?php

    namespace App\Listeners;

    use App\Events\EntityDelete;

    class EntityDeleteProcessing
    {

        public function handle(EntityDelete $event)
        {
            if (isset($event->entity->_alias->id)) $event->entity->_alias->delete();
            if (isset($event->entity->_search_index) && $event->entity->_search_index->isNotEmpty()) $event->entity->_search_index()->delete();
        }
        
    }