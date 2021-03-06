<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shop settings
    |--------------------------------------------------------------------------
    */

    // Таблица товаров
    'product_table' => 'products',

    // Поля товара для отображения в корзине
    'cart_elements' => [
        'id',
        'title',
        'slug',
        'img',
        'old_price',
        'price',
    ],

    // Виды фильтра
    'filter_type' => [
        'checkbox', // Первое значение по-умолчанию
        'radio',
        'select',
        'range',
    ],

    // Виды доставки
    'delivery' => [
        'courier', // Курьером
        'pickup', // Самовывоз
    ],


    // Коды ответа от Сбербанка
    'sberank_order_status' => [
        0 => 'Заказ зарегистрирован, но не оплачен',
        1 => 'Предавторизованная сумма захолдирована (для двухстадийных платежей)', // Успешно оплачено
        2 => 'Проведена полная авторизация суммы заказа', // Успешно оплачено
        3 => 'Авторизация отменена',
        4 => 'По транзакции была проведена операция возврата',
        5 => 'Инициирована авторизация через ACS банка-эмитента',
        6 => 'Авторизация отклонена',
    ],

];
