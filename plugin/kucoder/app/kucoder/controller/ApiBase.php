<?php

// +----------------------------------------------------------------------
// | Kucoder [ MAKE WEB FAST AND EASY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2026~9999 https://kucoder.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kucoder
// +----------------------------------------------------------------------


namespace plugin\kucoder\app\kucoder\controller;

use plugin\kucoder\app\kucoder\auth\ApiAuth;
use plugin\kucoder\app\kucoder\constants\KcConst;
use plugin\kucoder\app\kucoder\lib\KcHelper;
use plugin\kucoder\app\kucoder\traits\ApiCrudTrait;
use support\exception\BusinessException;
use support\think\Model;
use Throwable;

class ApiBase extends Base
{
    use ApiCrudTrait;

    /**
     * 应用名称
     */
    protected string $app = KcConst::API_APP;

    /*
     * 身份验证方式 ApiBase基类控制器默认使用cookie验证 可在请求或子类重写为token验证
     */
    protected string $authType = 'cookie';

    /**
     * @var ApiAuth 权限类
     */
    protected ApiAuth $auth;

    /**
     * @var array 无需登录的接口
     */
    protected array $noNeedLogin = [];

    /**
     * @var array 无需鉴权的接口
     */
    protected array $noNeedRight = [];

    // 开启路由后 若路由中未指定插件名则需要子类控制器指定插件名 否则鉴权失败  未开启路由则自动获取插件名
    protected ?string $pluginName = null;
    //允许访问的方法列表
    protected array $allowAccessActions = ['index', 'add', 'edit', 'delete', 'trueDel','info'];
    //是否验证
    protected bool $needValidate = true;
    //验证类::class
    protected string $validateClass = '';
    //验证类后缀 默认为空
    protected string $validateClassSuffix = '';
    //模型类
    protected string $modelClass = '';
    //实例化模型
    protected Model|null $model = null;
    //withJoin关联查询 不适用于多对多关联模型
    protected array $withJoin = [];
    //withJoin关联方式
    protected string $joinType = 'left';
    //with关联查询 适用于多对多关联模型
    protected array $with = [];
    //写入更新时排除字段
    protected array $excludeFields = ['module', 'controller', 'action', 'platform'];
    protected string|array $preExcludeFields = ['create_time', 'update_time', 'delete_time'];
    //创建者
    protected string $create_uid = 'create_uid';
    //更新者
    protected string $update_uid = 'update_uid';
    //列表默认排序
    protected array $orderBy = [];
    //默认查询字段
    protected string|array $field = '*';
    //查询排除字段
    protected string|array $withoutField = '';
    //列表tree
    protected bool $listTree = false;
    //列表tree pid字段
    protected string $listTreePid = 'pid';
    //json字段
    protected array $jsonFields = [];

    /**
     * @throws Throwable
     * @throws BusinessException
     */
    public function __construct()
    {
        kc_dump('执行apibase控制器的构造函数');
        parent::__construct();
        kc_dump('执行apibase控制器的构造函数--执行完毕');
        $this->_init();
    }

    private function _init(): void
    {
        try {
            $apiAuthConfig= KcConst::API_AUTH_CONFIG;
            if(KcHelper::isLocal()){
                $apiAuthConfig['table']['user'] = 'p_member';
            }
            $this->auth = ApiAuth::getInstance($apiAuthConfig);
            // kc_dump('apiBase auth:', $this->auth);
            $this->checkLoginAndRight();
        } catch (Throwable $e) {
            // 构造函数不能return返回值 会被忽略
            $this->throw($e->getMessage(), $e->getCode());
        }
    }

}