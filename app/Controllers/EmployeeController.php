<?php

namespace app\Controllers;

class EmployeeController
{
    public function index()
    {
        route('employee.show', ['{id}' => 12]);
    }

    public function show($id)
    {
        echo "Employeenin ID değeri: $id";
    }

    public function list($name)
    {
        echo "Employeenin name değeri: $name";
    }

    public function ciftParametre($name, $parametre2)
    {
        echo "Employeenin name değeri: $name Parametre2: $parametre2";
    }
}