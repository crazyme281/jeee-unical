<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

function starts_with($value, $prefix) {
    return Str::startsWith($value ?? '', $prefix);
}

function str_random($length = 16) {
    return Str::random($length);
}

function str_slug($value, $separator = '-') {
    return Str::slug($value, $separator);
}

function array_where($values, $callback) {
    return Arr::where($values, $callback);
}

function setting($name) {
    return config('settings.'.$name);
}

function issue_price($type = 'default') { // type in default, format, pay
    $price = setting('issue_price');
    switch ($type) {
        case 'format':
            return "₦" . number_format($price);
        case 'pay':
            return $price * 100; // convert to kobo for paystack
        default:
            return $price;
    }
}

function volume_price($type = 'default') { // type in default, format, pay
    $price = setting('volume_price');
    switch ($type) {
        case 'format':
            return "₦" . number_format($price);
        case 'pay':
            return $price * 100; // convert to kobo for paystack
        default:
            return $price;
    }
}

function volume_price_multi($type = 'default') { // type in default, format, pay
    $price = setting('volume_price_multi');
    switch ($type) {
        case 'format':
            return "₦" . number_format($price);
        case 'pay':
            return $price * 100; // convert to kobo for paystack
        default:
            return $price;
    }
}
