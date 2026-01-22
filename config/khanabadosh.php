<?php

return [
    'source_url' => env('KHANABADOSH_SOURCE_URL', 'https://khanabadoshonline.com'),
    'order_notification_emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ORDER_NOTIFICATION_EMAILS', 'info@khanabadoshfashion.ca,anastanveer557@gmail.com'))
    ))),
];
