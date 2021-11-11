<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Library\BaseController;
    use App\Models\Structure\Page;
    use Illuminate\Foundation\Auth\VerifiesEmails;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\View;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    class VerificationController extends BaseController
    {
        use VerifiesEmails;

        protected $redirectTo = 'account';

        public function __construct()
        {
            parent::__construct();
            $this->middleware('auth.verify_email');
            $this->middleware('signed')
                ->only('verify');
            $this->middleware('throttle:6,1')
                ->only('verify', 'resend');
        }

        public function show(Request $request)
        {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect($this->redirectPath());
            } else {
                $_template = [
                    "frontend.{$this->deviceTemplate}.user.verification_email",
                    'frontend.default.user.verification_email'
                ];
                $_item = new Page();
                $_wrap = wrap()->get();
                $_item->setWrap([
                    'seo.robots'      => 'noindex, nofollow',
                    'seo.url_alias'   => 'email/verify',
                    'seo.title'       => trans('pages.titles.verification'),
                    'page.title'      => trans('pages.titles.verification'),
                    'alias'           => 'email/verify',
                    'page.breadcrumb' => collect([
                        [
                            'name'     => trans('pages.titles.home'),
                            'url'      => _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')),
                            'position' => 1
                        ],
                        [
                            'name'     => trans('pages.titles.verification'),
                            'url'      => NULL,
                            'position' => 2
                        ]
                    ]),
                ]);
                $_wrap = wrap()->render();

                return View::first($_template, compact('_item', '_wrap'));
            }
        }

    }
