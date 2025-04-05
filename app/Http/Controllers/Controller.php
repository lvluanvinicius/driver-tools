<?php
namespace App\Http\Controllers;

use App\Traits\AdvancedQueries;
use App\Traits\JsonResponseTrait;
use App\Traits\Permission;

abstract class Controller
{
    use JsonResponseTrait, AdvancedQueries, Permission;
}
