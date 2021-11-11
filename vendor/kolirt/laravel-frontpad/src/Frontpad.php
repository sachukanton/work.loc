<?php

namespace Kolirt\Frontpad;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Frontpad
{

    private $client;
    private $api = 'https://app.frontpad.ru/api/index.php';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->api,
            'timeout'  => config('frontpad.timeout', 3),
            'headers'  => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * Create new order.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function newOrder(array $data)
    {
        $request = $this->client->post('?new_order', [
            'form_params' => array_merge([
                'secret' => config('frontpad.secret')
            ], $data)
        ]);

        $response = collect($this->prepareResponse($request));

        return collect($response)->only(['order_id', 'order_number'])->toArray();
    }

    /**
     * Get client info by phone number.
     *
     * @param string $clientPhone
     * @return array
     * @throws Exception
     */
    public function getClient(string $clientPhone)
    {
        $request = $this->client->post('?get_client', [
            'form_params' => [
                'secret'       => config('frontpad.secret'),
                'client_phone' => $clientPhone
            ]
        ]);
        $response = collect($this->prepareResponse($request));
        return $response->only([
            'name',
            'street',
            'home',
            'pod',
            'et',
            'apart',
            'mail',
            'descr',
            'card',
            'sale',
            'score'
        ])->toArray();
    }

    /**
     * Get certificate info.
     *
     * @param string $certificate
     * @return array
     * @throws Exception
     */
    public function getCertificate(string $certificate)
    {
        $request = $this->client->post('?get_certificate', [
            'form_params' => [
                'secret'      => config('frontpad.secret'),
                'certificate' => $certificate
            ]
        ]);
        $response = collect($this->prepareResponse($request));
        return $response->only(['product_id', 'name', 'price', 'sale', 'amount'])->toArray();
    }

    /**
     * Get all products which has a code.
     *
     * @return Collection
     * @throws Exception
     */
    public function getProducts()
    {
        $request = $this->client->post('?get_products', [
            'form_params' => [
                'secret' => config('frontpad.secret')
            ]
        ]);

        $response = $this->prepareResponse($request);
        $result = [];

        foreach ($response->product_id as $key => $productId) {
            $result[] = [
                'product_id' => $productId,
                'name'       => $response->name[$key],
                'price'      => $response->price[$key],
            ];
        }

        return collect($result);
    }

    /**
     * Prepare input data from response.
     *
     * @param $response
     * @return mixed
     * @throws Exception
     */
    private function prepareResponse($response)
    {
        $result = json_decode($response->getBody()->getContents());

        if (($result->result ?? null) !== 'success') {
            throw new Exception('Error: ' . ($result->error ?? '-'));
        }

        return $result;
    }

}
