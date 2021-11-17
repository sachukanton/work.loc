
CREATE TABLE IF NOT EXISTS `shop_promo_code` (
  `id` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('all_basket','sale_product','product_null') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all_basket',
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `date_to` date NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

ALTER TABLE `shop_promo_code`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT primary key;

INSERT INTO `shop_promo_code` (`id`, `title`, `code`, `type`, `details`, `status`, `date_to`, `created_at`, `updated_at`) VALUES
(1, '{"ru":"\\u0421\\u043a\\u0438\\u0434\\u043a\\u0430 \\u043d\\u0430 \\u0432\\u0441\\u0435 \\u0440\\u043e\\u043b\\u043b\\u044b - 10%"}', '4563-85', 'sale_product', '10', 1, '2021-10-31', '2021-09-14 04:20:34', '2021-11-17 13:16:58'),
(2, '{"ru":"\\u0421\\u043a\\u0438\\u0434\\u043a\\u0430 \\u043d\\u0430 \\u0432\\u0441\\u044e \\u043a\\u043e\\u0440\\u0437\\u0438\\u043d\\u0443"}', '45789-89', 'all_basket', '', 1, '2021-11-27', '2021-11-17 13:35:16', '2021-11-17 13:41:18');
