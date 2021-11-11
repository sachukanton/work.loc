<?php

    namespace App\Models\File;

    use App\Library\BaseModel;

    class FilesReference extends BaseModel
    {

        protected $table = 'files_related';
        protected $guarded = [];
        protected $entity;
        public $timestamps = FALSE;

        public function __construct($entity = NULL)
        {
            parent::__construct();
            $this->entity = $entity;
        }

        public function set()
        {
            if ($this->entity) {
                $_medias = request()->input('medias');
                $_files = request()->input('files');
                $this->entity->_files_related()->detach();
                $_attach = NULL;
                if ($_medias) foreach ($_medias as $_file) $_attach[$_file['id']] = ['type' => 'medias'];
                if ($_files) foreach ($_files as $_file) $_attach[$_file['id']] = ['type' => 'files'];
                if ($_attach) $this->entity->_files_related()->attach($_attach);
            }

            return NULL;
        }

    }
