@php
    $field['type'] = 'textarea';
    $field['value'] = is_array($field['value'] ?? null) ? json_encode($field['value'], JSON_PRETTY_PRINT) : ($field['value'] ?? '[]');
    $field['hint'] = 'Keep the list format. Each author has names, email, institution, and department.';
@endphp
@include('crud::fields.textarea')
