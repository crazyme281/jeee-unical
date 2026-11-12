@php
    $field['type'] = 'upload_multiple';
    $field['value'] = array_column($field['value'] ?? [], 'path');
@endphp
@include('crud::fields.upload_multiple')
