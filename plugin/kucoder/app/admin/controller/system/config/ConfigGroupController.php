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


namespace plugin\kucoder\app\admin\controller\system\config;

use Exception;
use plugin\kucoder\app\admin\model\ConfigGroup;
use kucoder\controller\AdminBase;
use support\think\Cache;

/**
 * 配置分组控制器
 */
class ConfigGroupController extends AdminBase
{
    protected string $modelClass = ConfigGroup::class;

    /**
     * @throws Exception
     */
    protected function add_before($params): void
    {
        $id = ConfigGroup::where('name', $params['name'])->where('plugin',$params['plugin'])->value('id');
        if($id){
            throw new Exception('分组已存在');
        }
    }

    protected function save_after(array $data): void
    {
        Cache::delete($this->app.':config:groups');
    }

    protected function delete_after(array $data): void
    {
        Cache::delete($this->app.':config:groups');
    }
}