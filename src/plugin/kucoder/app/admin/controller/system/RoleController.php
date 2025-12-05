<?php
declare(strict_types=1);


namespace plugin\kucoder\app\admin\controller\system;

use plugin\kucoder\app\kucoder\controller\AdminBase;

class RoleController extends AdminBase
{
    protected string $modelClass = "\\plugin\\kucoder\\app\\admin\\model\\Role";
    protected array $orderBy = ['sort' => 'asc'];

}