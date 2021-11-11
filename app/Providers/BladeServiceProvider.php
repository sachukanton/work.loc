<?php

    namespace App\Providers;

    use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
    use Illuminate\Support\Facades\Blade;

    class BladeServiceProvider extends ServiceProvider
    {

        public function boot()
        {
            Blade::directive('l', function ($expression) {
                return "<?php echo _l({$expression}); ?>";
            });
            Blade::directive('formField', function ($expression) {
                return "<?php echo field_render({$expression}); ?>";
            });
            Blade::directive('blockRender', function ($expression) {
                return "<?php echo block_render({$expression}); ?>";
            });
            Blade::directive('bannerRender', function ($expression) {
                return "<?php echo banner_render({$expression}); ?>";
            });
            Blade::directive('advantageRender', function ($expression) {
                return "<?php echo advantage_block_render({$expression}); ?>";
            });
            Blade::directive('faqBlockRender', function ($expression) {
                return "<?php echo faq_block_render({$expression}); ?>";
            });
            Blade::directive('sliderRender', function ($expression) {
                return "<?php echo slider_render({$expression}); ?>";
            });
            Blade::directive('formRender', function ($expression) {
                return "<?php echo form_render({$expression}); ?>";
            });
            Blade::directive('menuRender', function ($expression) {
                return "<?php echo menu_render({$expression}); ?>";
            });
            Blade::directive('imageRender', function ($expression) {
                return "<?php echo image_render({$expression}); ?>";
            });
            Blade::directive('variable', function ($expression) {
                return "<?php echo variable({$expression}); ?>";
            });
        }

    }
