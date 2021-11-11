<?php

    namespace App\Listeners;

    use App\Events\EntitySave;
    use App\Models\Components\DisplayRules;
    use App\Models\File\FilesReference;
    use App\Models\Seo\SearchIndex;
    use App\Models\Seo\TmpMetaTags;
    use App\Models\Seo\UrlAlias;
    use App\Models\Structure\Tag;

    class EntitySaveProcessing
    {

        public function handle(EntitySave $event)
        {
            update_last_modified_timestamp();
            $_request = request();
            if(method_exists($event->entity, '_alias')) {
                $_url_alias = new UrlAlias($event->entity);
                $_url_alias->set();
                $_url_alias = new TmpMetaTags($event->entity);
                $_url_alias->set();
            }
            if(method_exists($event->entity, '_files_related')) {
                $_medias = new FilesReference($event->entity);
                $_medias->set();
            }
            if ($_request->has('display_rules')) {
                $_rules = new DisplayRules($event->entity);
                $_rules->updating_rules();
            }
            if(method_exists($event->entity, '_tags')) {
                $_tags = new Tag($event->entity);
                $_tags->set();
            }
            if(method_exists($event->entity, '_search_index')) {
                $_search = new SearchIndex($event->entity);
                $_search->set();
            }
        }

    }