<?php

    namespace App\Models\File;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\File as FileStorage;
    use Illuminate\Support\Facades\Storage;

    class File extends BaseModel
    {

        const IMAGE_MIMETYPE = [
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
        protected $table = 'files_managed';
        protected $guarded = [];
        private $fid;
        protected $classIndex = 'file';

        public function __construct($fid = NULL)
        {
            parent::__construct();
            $this->fid = $fid;
        }

        public static function duplicate($id = NULL)
        {
            if (!is_null($id)) {
                $_entity = self::find($id);
                $_save = $_entity->toArray();
                unset($_save['id']);
                unset($_save['created_at']);
                unset($_save['updated_at']);
                $_duplicate = self::updateOrCreate([
                    'id' => NULL
                ], $_save);

                return $_duplicate->id;
            }

            return NULL;
        }

        public static function find_or_create($file_name, $path_to_file)
        {
            $_file_name = strtolower($file_name);
            $_file_path = "{$path_to_file}/{$file_name}";
            if (!($_file = self::where('filename', $_file_name)->first())) {
                if (Storage::disk('base')->exists($_file_path)) {
                    $_file_mime_type = Storage::disk('base')->mimeType($_file_path);
                    $_file_size = Storage::disk('base')->size($_file_path);
                    Storage::disk('base')->move($_file_path, "uploads/{$_file_name}");
                    $_file = self::updateOrCreate([
                        'id' => NULL
                    ], [
                        'filename' => $_file_name,
                        'filemime' => $_file_mime_type,
                        'filesize' => $_file_size,
                    ]);
                }
            }

            return $_file ? $_file : new File();
        }

        public function getBaseUrlAttribute()
        {
            return Storage::url("{$this->filename}");
        }

        public static function create_file_by_url($url, $folder = NULL)
        {
            $_response = NULL;
            $_file_url = storage_path($url);
            if (FileStorage::exists($_file_url)) {
                $_file_base_name = FileStorage::basename($_file_url);
                $_file_size = FileStorage::size($_file_url);
                $_file_extension = FileStorage::extension($_file_url);
                $_file_name = str_slug(str_replace(".{$_file_extension}", '', $_file_base_name)) . '-' . uniqid() . ".{$_file_extension}";
                $_item = File::where('base_name', $_file_base_name)
                    ->where('filesize', $_file_size)
                    ->first();
                if (!$_item) {
                    $_file_mime_type = FileStorage::mimeType($_file_url);
                    $_file_save_path = $folder ? "{$folder}/{$_file_name}" : $_file_name;
                    FileStorage::copy($_file_url, storage_path("app/public/{$_file_save_path}"));
                    $_item = new File();
                    $_item->fill([
                        'base_name' => $_file_base_name,
                        'filename'  => $_file_name,
                        'filemime'  => $_file_mime_type,
                        'filesize'  => $_file_size,
                    ]);
                    $_item->save();
                } else {
                    $_item = $_item->replicate();
                    $_item->save();
                }
                $_response = $_item;
            }

            return $_response;
        }

    }
