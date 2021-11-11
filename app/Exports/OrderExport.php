<?php

    namespace App\Exports;

    use App\Models\Shop\Order;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\ShouldAutoSize;
    use Maatwebsite\Excel\Concerns\WithHeadings;

    class OrderExport implements FromCollection, WithHeadings, ShouldAutoSize
    {

        public function collection()
        {
            $_collect = [];
            $_order_id = request()->get('order_id');
            $_order = Order::find($_order_id);
            if ($_order) {
                foreach ($_order->_products as $_product) {
                    $_product_data = $_product->_product;
                    $_collect[] = [
                        ($_product_data->model ? : $_product_data->sku) ?? NULL,
                        $_product->product_name,
                        $_product->quantity,
                        $_product->price,
                        $_product->amount,
                    ];
                }
            }
            $_collect[] = [
                '',
                '',
                '',
                '',
                '',
            ];
            $_collect[] = [
                '',
                '',
                '',
                'Total:',
                $_order->amount
            ];

            return collect($_collect);
        }

        public function headings(): array
        {
            return [
                'SKU',
                'Product name',
                'Quantity',
                'Price',
                'Amount',
            ];
        }

    }