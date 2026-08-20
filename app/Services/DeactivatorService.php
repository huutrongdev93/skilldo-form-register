<?php
namespace FormRegister\Services;

Class DeactivatorService
{
    public static function uninstall(): void
    {
        schema()->drop('generate_form_register');
        schema()->drop('form_register_result');
        schema()->drop('form_register_result_metadata');
    }
}