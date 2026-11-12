<?php

return [
    'view_namespace' => 'backpack.theme-tabler::',
    'view_namespace_fallback' => 'backpack.theme-tabler::',
    'project_logo' => 'JETSI',
    'show_getting_started' => false,
    'show_powered_by' => false,
    'developer_name' => '',
    'developer_link' => false,
    'project_name' => env('APP_NAME', 'JETSI'),
    'logo_lg' => 'JETSI',
    'logo_mini' => 'JETSI',
    'home_link' => '/',
    'menu_items' => [
        ['title' => 'Dashboard', 'link' => '/admin/dashboard'],
        ['title' => 'Issues', 'link' => '/admin/issue'],
        ['title' => 'Articles', 'link' => '/admin/article'],
        ['title' => 'Manuscripts', 'link' => '/admin/manuscript'],
        ['title' => 'Users', 'link' => '/admin/user'],
        ['title' => 'Subscribers', 'link' => '/admin/subscriber'],
        ['title' => 'Settings', 'link' => '/admin/setting'],
    ],
];
