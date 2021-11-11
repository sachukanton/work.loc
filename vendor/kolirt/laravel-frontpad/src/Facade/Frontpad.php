<?php

namespace Kolirt\Frontpad\Facade;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array newOrder($data)
 * @method static string getOrderStatusByClientPhone(string $clientPhone)
 * @method static string getOrderStatusByOrderId(string $orderId)
 * @method static array getClient(string $clientPhone)
 * @method static array getCertificate(string $certificate)
 * @method static Collection getProducts()
 */
class Frontpad extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'frontpad';
    }

}