<?php
declare(strict_types=1);


namespace plugin\kucoder\app\admin\controller\system;

use plugin\kucoder\app\kucoder\controller\AdminBase;

class DeptController extends AdminBase
{
    protected string $modelClass = "\\plugin\\kucoder\\app\\admin\\model\\Dept";
    //排序
    protected array $orderBy = ['sort'=>'asc'];
    //列表tree
    protected bool $listTree = true;
    //列表tree pid字段
    protected string $listTreePid = 'parent_id';

}