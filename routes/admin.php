<?php

use SkillDo\Support\Facades\Route;

Route::middleware('auth:admin')
    ->prefix('admin')
    ->group(function ()
    {
        Route::get('/form-register', 'FormRegister\Controllers\Admin\FormRegisterController@index')->name('admin.formRegister.index');
        Route::get('/form-register/add', 'FormRegister\Controllers\Admin\FormRegisterController@add')->name('admin.formRegister.add');
        Route::get('/form-register/edit/{id}', 'FormRegister\Controllers\Admin\FormRegisterController@edit')->name('admin.formRegister.edit');
        Route::get('/form-register/sample', 'FormRegister\Controllers\Admin\FormRegisterController@sample')->name('admin.formRegister.sample');
    });

Route::middleware('auth:admin')
    ->prefix('admin')
    ->group(function ()
    {
        Route::get('/form-register-result', 'FormRegister\Controllers\Admin\FormRegisterResultController@index')->name('admin.form_register_result.index');
    });