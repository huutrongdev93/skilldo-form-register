<?php

use SkillDo\Support\Path;

class GenerateFormRegister
{
    public function active(): void
    {
        \FormRegister\Services\ActivatorService::activate();
    }

    public function uninstall(): void
    {
        \FormRegister\Services\DeactivatorService::uninstall();
    }
}