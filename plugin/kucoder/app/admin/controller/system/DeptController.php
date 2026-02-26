<?php
declare(strict_types=1);

// +----------------------------------------------------------------------
// | Kucoder [ MAKE WEB FAST AND EASY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2026~9999 https://kucoder.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kucoder
// +----------------------------------------------------------------------



namespace plugin\kucoder\app\admin\controller\system;

use kucoder\controller\AdminBase;

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