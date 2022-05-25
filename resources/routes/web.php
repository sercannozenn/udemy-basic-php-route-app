<?php
use \app\Core\Route;


Route::get('/', function(){
    echo 'burası anasayfa';
});

//Route::get('/users', function(){
//echo "burası users sayfası";
//});

Route::get('/employee', 'EmployeeController@index');
Route::get('/employee/{id}', 'EmployeeController@show')->name('employee.show');
Route::get('/employee/ciftparametre/{name}/{parametre2}', 'EmployeeController@ciftParametre')
->where(['name' => '([0-9]+)', 'parametre2' => '([a-z])']);

//route('employee.index', ['{id}' => 99]);

Route::prefix('/users')->group(function(){
   Route::get('/', function(){
       echo 'burası prefix users anasayfa';
   });

   Route::get('/{id}', function($id){
       echo "burası prefix id anasayfa $id";
   });

    Route::get('/show', function(){
        echo 'burası prefix users show sayfası';
    });
});


Route::dispatch();