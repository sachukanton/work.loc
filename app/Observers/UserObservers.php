<?php

    namespace App\Observers;

    use App\Models\User\Profile;
    use App\Models\User\User;
    use Carbon\Carbon;

    class UserObservers
    {

        public function created(User $_item)
        {
        }

        public function saved(User $_item)
        {
            $_save_profile = request()->get('profile');
            if ($_save_profile) {
                $_save_profile['user_id'] = $_item->id;
                $_save_profile['birthday'] = isset($_save_profile['birthday']) && $_save_profile['birthday'] ? Carbon::parse($_save_profile['birthday']) : NULL;
                if (isset($_save_profile['avatar_fid']) && $_save_profile['avatar_fid']) {
                    $_avatar_fid = array_shift($_save_profile['avatar_fid']);
                    $_save_profile['avatar_fid'] = (int)$_avatar_fid['id'];
                }
                Profile::updateOrCreate([
                    'user_id' => $_item->id
                ], $_save_profile);
            }
        }

        public function deleting(User $_item)
        {
        }

    }