<?php

    namespace App\Models\User;

    use App\Library\BaseModel;

    class Profile extends BaseModel
    {

        protected $table = 'users_profile';
        protected $guarded = [];
        public $timestamps = FALSE;
        protected $dates = [
            'birthday'
        ];

        public function __construct()
        {
            parent::__construct();
        }

        public function _user()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

        public function _user_avatar_render($default_image = 'images/no-user-avatar.svg')
        {
            if ($this->avatar_fid) {
                $_avatar = $this->_avatar_asset('account_avatar_big');
            } else {
                $_avatar = image_render($default_image, 'account_avatar_big');
            }

            return $_avatar;
        }

    }
