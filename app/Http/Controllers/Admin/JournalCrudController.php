<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

abstract class JournalCrudController extends CrudController
{
    use CreateOperation, DeleteOperation, ListOperation, UpdateOperation;

    protected function setupCreateOperation()
    {
        $this->crud->setValidation($this->requestClass);
    }

    protected function setupUpdateOperation()
    {
        $this->crud->setValidation($this->requestClass);
    }
}
