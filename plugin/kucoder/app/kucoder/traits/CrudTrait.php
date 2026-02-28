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


namespace kucoder\traits;

use Exception;
use kucoder\constants\KcConst;
use kucoder\lib\KcHelper;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use support\Request;
use support\Response;
use support\think\Model;
use support\think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use Throwable;

trait CrudTrait
{
    use TableTrait;

    //实例化模型
    protected Model|null $model = null;

    //彻底删除调用
    private bool $isCalledByTrueDelete = false;

    /**
     * 查询
     * @return Response
     * @throws DbException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function index(): Response
    {
        try {
            $this->checkActionAccess();
            //前处理
            if (method_exists($this, 'index_before')) {
                call_user_func_array([$this, 'index_before'], []);
            }

            //请求参数
            [$page, $pageSize, $order, $where] = $this->_queryList();
            if ($this->listTree) {
                $pageSize = 9999;
            }

            //查询字段
            if ($this->withoutField && $this->field === '*') {
                $withoutFields = [];
                if (is_array($this->withoutField)) {
                    $withoutFields = array_map('trim', $this->withoutField);
                }
                if (is_string($this->withoutField)) {
                    $withoutFields = array_map('trim', explode(',', $this->withoutField));
                }
                // 字段排除
                $fields = $this->model->getFields();
                $this->field = array_diff(array_keys($fields), $withoutFields);
            }

            //with预处理
            $with = [];
            if ($this->with) {
                foreach ($this->with as $key => $value) {
                    $with[$key] = function (Query $query) use ($value) {
                        $query->field($value);
                    };
                }
            }

            //查询组装
            $items = $this->model
                ->field($this->field)
                ->withJoin($this->withJoin, $this->joinType)
                ->with($with)
                ->where($where)
                ->order($order);

            //默认情况下模型查询的数据不包含软删除数据 如果仅仅需要查询软删除的数据，可以使用onlyTrashed()
            $recycle = request()->get('recycle', 0);
            if ($recycle) {
                $items = $items->onlyTrashed();
            }
            //json 字段处理
            if ($this->jsonFields) {
                $items = $items->json($this->jsonFields);
            }

            if ($this->listTree && !$where && !$recycle) {
                // kc_dump('tree查询', $where);
                //列表tree
                $data = $items->select();
                $total = $data->count();
                $res = get_recursion_data($data, $this->model->getPk(), $this->listTreePid);
                // kc_dump('tree查询 res', $res);
            } else {
                // kc_dump('普通查询', $where);
                //分页查询
                $paginator = $items->paginate(
                    [
                        'list_rows' => $pageSize,
                        'page' => $page
                    ]);
                $res = $paginator->items();
                $total = $paginator->total();
            }
            // kc_dump('查询sql', $this->model->getLastSql());
            //后处理
            $resData = $res;
            if (method_exists($this, 'index_after')) {
                $resData = call_user_func_array([$this, 'index_after'], [&$res]);
            }
        } catch (Throwable $t) {
            kc_dump('查询异常', $this->errorInfo($t));
            // $this->throw($this->errorInfo($t));
            $this->throw($t->getMessage(), $t->getCode());
        }

        return $this->success('', [
            'list' => $resData,
            'total' => $total,
            'enumFieldData' => $this->getEnumFieldDataFromDb($this->model->getTable()),
        ]);
    }

    /**
     * 查看详情
     * @return Response
     * @throws Throwable
     */
    public function info(): Response
    {
        try {
            $this->checkActionAccess();
            //请求参数
            $data = $this->request->all();
            //前处理
            if (method_exists($this, 'info_before')) {
                call_user_func_array([$this, 'info_before'], [&$data]);
            }

            $pk = $this->model->getPk();
            if (!isset($data[$pk]) && !isset($data['id'])) {
                return $this->error('数据ID不存在');
            }
            $id = isset($data[$pk]) && $data[$pk] ? $data[$pk] : $data['id'];
            if (!$id) {
                return $this->error('参数错误');
            }
            //查询字段
            if ($this->withoutField && $this->field === '*') {
                if (is_string($this->withoutField)) {
                    $this->withoutField = explode(',', $this->withoutField);
                }
                $withoutFields = array_map('trim', $this->withoutField);
                // 字段排除
                $fields = $this->model->getFields();
                $this->field = array_diff(array_keys($fields), $withoutFields);
            }
            $item = $this->model
                ->field($this->field)
                ->withJoin($this->withJoin, $this->joinType)
                ->with($this->with)
                ->where($pk, $id)
                ->find()
                ->toArray();
            //前处理
            if (method_exists($this, 'info_after')) {
                call_user_func_array([$this, 'info_after'], $item);
            }
            return $this->success('', $item);
        } catch (Throwable $t) {
            return $this->error($t->getMessage());
        }
    }

    /**
     * 新增
     * @return Response
     * @throws Throwable
     */
    public function add(): Response
    {
        $this->checkActionAccess();
        if (!$this->request->isPost()) {
            return $this->error('请求方法错误');
        }
        //请求数据预处理
        $param = $this->request->post();
        try {
            if (method_exists($this, 'save_before')) {
                //请注意，传入call_user_func()的参数不能为引用传递
                call_user_func_array([$this, 'save_before'], [&$param, $this->request->action]);
            }
            if (method_exists($this, 'add_before')) {
                call_user_func_array([$this, 'add_before'], [&$param]);
            }
        } catch (Throwable $t) {
            return $this->error($t->getMessage());
        }
        // kc_dump('add的params:' . json_encode($param, JSON_UNESCAPED_UNICODE));

        //字段处理
        $data = $this->excludeFields($param);
        // kc_dump('add的data:' . json_encode($param, JSON_UNESCAPED_UNICODE));
        //添加者user_id
        if ($this->create_uid) {
            $data[$this->create_uid] = $this->auth->getId();
        }
        //写入数据库
        $this->model->startTrans();
        try {
            //模型验证
            $this->validate('add');
            //json字段
            if ($this->jsonFields) {
                foreach ($this->jsonFields as $field) {
                    if (isset($data[$field]) && is_array($data[$field])) {
                        $data[$field] = json_encode($data[$field], JSON_UNESCAPED_UNICODE);
                    }
                }
            }
            $res = $this->model->save($data, true);
            $data['insertGetId'] = $this->model->getKey();
            //后置操作
            // kc_dump('add结果res:' . $res);
            // kc_dump('add新增的id:' . $data['insertGetId']);
            if (!$data['insertGetId']) {
                $this->throw('添加失败', null, true);
            }
            if (method_exists($this, 'save_after')) {
                call_user_func_array([$this, 'save_after'], [&$data, $this->request->action]);
            }
            if (method_exists($this, 'add_after')) {
                call_user_func_array([$this, 'add_after'], [&$data]);
            }
            $this->model->commit();
            $this->_log();
        } catch (Throwable $t) {
            // kc_dump('add失败', $t->getMessage());
            kc_dump('add失败', $this->errorInfo($t));
            $this->model->rollback();
            $this->_log(status: 0, msg: '添加失败' . $t->getMessage());
            return $this->error($t->getMessage());
        }

        return $this->success('添加成功');
    }

    /**
     * 修改
     * @return Response
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException|Throwable
     */
    public function edit(): Response
    {
        $this->checkActionAccess();
        if (!$this->request->isPost()) {
            return $this->error('请求方法错误');
        }
        //请求数据预处理
        $param = $this->request->post();
        try {
            if (method_exists($this, 'save_before')) {
                //请注意，传入call_user_func()的参数不能为引用传递
                call_user_func_array([$this, 'save_before'], [&$param, $this->request->action]);
            }
            if (method_exists($this, 'edit_before')) {
                call_user_func_array([$this, 'edit_before'], [&$param]);
            }
        } catch (Throwable $t) {
            return $this->error($t->getMessage());
        }

        //字段处理
        $data = $this->excludeFields($param);
        //修改者user_id
        if ($this->update_uid) {
            $data[$this->update_uid] = $this->auth->getId();
        }
        //无主键则无法更新
        $pk = $this->model->getPk();
        if (!isset($data[$pk]) && !isset($data['id'])) {
            return $this->error('更新数据异常');
        }
        $id = isset($data[$pk]) && $data[$pk] ? $data[$pk] : $data['id'];
        if (isset($data['delete_restore']) && $data['delete_restore']) {
            //回收站恢复 恢复软删除的数据 withTrashed包含软删除的数据
            $model = $this->model->withTrashed()->find($id);
        } else {
            $model = $this->model->find($id);
        }
        if (!$model) return $this->error('数据异常');

        $this->model->startTrans();
        try {
            if (isset($data['delete_restore']) && $data['delete_restore']) {
                //回收站恢复 恢复软删除的数据 withTrashed包含软删除的数据
                $model->restore();
            } else {
                if (!isset($data['edit_status'])) {
                    //模型验证
                    $this->validate('edit');
                    //json字段
                    if ($this->jsonFields) {
                        foreach ($this->jsonFields as $field) {
                            if (isset($data[$field]) && is_array($data[$field])) {
                                $data[$field] = json_encode($data[$field], JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }
                // kc_dump('edit的数据', $data);
                $res = $model->save($data, [$pk => $id]);
                //更新成功返回true 如果提交的数据与数据库数据一致则更新结果返回0
                if (!in_array($res, [true, 0])) {
                    $this->throw('更新失败', null, true);
                }
                //写入后置操作
                if (method_exists($this, 'save_after')) {
                    call_user_func_array([$this, 'save_after'], [&$data, $this->request->action]);
                }
                if (method_exists($this, 'edit_after')) {
                    call_user_func_array([$this, 'edit_after'], [&$data]);
                }
            }
            $this->model->commit();
            $this->_log();
        } catch (Throwable $t) {
            kc_dump('edit失败', $this->errorInfo($t));
            $this->model->rollback();
            $this->_log(status: 0, msg: '修改失败' . $t->getMessage());
            return $this->error('修改失败 ' . $t->getMessage());
        }
        return $this->success('修改成功');
    }

    /**
     * 删除
     * @return Response
     * @throws Exception
     */
    public function delete(): Response
    {
        $this->checkActionAccess();
        if (!$this->request->isPost()) {
            return $this->error('请求方法错误');
        }
        //预处理
        $param = $this->request->post();
        try {
            if (method_exists($this, 'delete_before')) {
                call_user_func_array([$this, 'delete_before'], [&$param]);
            }
        } catch (Throwable $t) {
            $this->error($t->getMessage());
        }

        //删除事务
        $this->model->startTrans();
        try {
            if (!isset($param['id']) || !$param['id']) {
                $this->throw('异常操作');
            }
            if (!$this->isCalledByTrueDelete) {
                //软删除
                if (!is_array($param['id'])) {
                    $model = $this->model->find($param['id']);
                    $del = $model->delete();
                } else {
                    $del = $this->model->destroy($param['id']);
                }
            } else {
                //彻底删除
                if (!is_array($param['id'])) {
                    $model = $this->model->withTrashed()->find($param['id']);
                    $del = $model->force()->delete();
                } else {
                    $del = $this->model->destroy($param['id'], true);
                }
            }

            if (!$del) {
                $this->throw('删除失败');
            }
            //删除后操作
            if (method_exists($this, 'delete_after')) {
                call_user_func_array([$this, 'delete_after'], [&$param]);
            }
            $this->model->commit();
            $this->_log();
        } catch (Throwable $t) {
            $this->model->rollback();
            $this->_log(status: 0, msg: '删除失败' . $t->getMessage());
            $this->throw($t->getMessage());
        }
        return $this->success('删除成功');
    }

    /**
     * 彻底真正删除
     * @return Response
     * @throws Exception
     */
    public function trueDel(): Response
    {
        $this->checkActionAccess();
        $this->isCalledByTrueDelete = true;
        return $this->delete();
    }

    /**
     * 操作日志
     *  param $app_type 应用类型 0=后台admin应用,1=客户端api应用,2=客户端小程序app应用
     *  param $status 操作结果 1=成功,0=失败
     *  param $msg 操作结果信息
     *  param $title 操作标题
     */
    protected function _log(int $status = 1, string $msg = '', string $title = ''): void
    {
        $app_type = match ($this->app) {
            KcConst::ADMIN_APP => 0,
            KcConst::API_APP => 1,
            KcConst::APP_MINI_APP => 2,
            default => 0,
        };
        $this->oLog->add($app_type, $status, $msg, $title);
    }

    /**
     * 查询条件
     * @return array
     */
    protected function _queryList(): array
    {
        try {
            $full_url = $this->request->fullurl();
            $decodeFullUrl = urldecode($full_url);
            $query = parse_url($decodeFullUrl, PHP_URL_QUERY);

            //$limit
            $page = $this->request->input('pageNum', 1);
            $pageSize = $this->request->get('pageSize', 10);
            $offset = ($page - 1) * $pageSize;
            $limit = [$offset, $pageSize];
            // kc_dump('limit', $limit);

            $mainModelAlias = $this->model->getTable();
            // kc_dump('数据表别名', $mainModelAlias);

            //$order
            $orderArr = [];
            $order = $this->request->input('order');
            if ($order) {
                list($orderField, $orderDirection) = explode(' ', $order);
                if ($orderField && in_array($orderDirection, ['asc', 'desc'])) {
                    $orderArr = [$mainModelAlias . '.' . $orderField, $orderDirection];
                }
            }
            $pk = $this->model->getPk();
            if (!$this->orderBy) {
                $this->orderBy = [$mainModelAlias . '.' . $pk => 'desc'];
            }
            $order = array_merge($this->orderBy, $orderArr);
            // kc_dump('order', $order);

            $where = [];

            //日期时间区间查询 dateBetween
            $dateBetween = $this->request->input('dateBetween');
            // kc_dump('dateBetween', $dateBetween); //['create_time'=>'2025-05-05,2025-06-13','update_time'=>'2025-05-05,2025-06-13']
            if ($dateBetween) {
                foreach ($dateBetween as $key => $value) {
                    $timeArr = explode(',', $value);
                    $where[] = [$mainModelAlias . '.' . $key, '>=', $timeArr[0]];
                    $where[] = [$mainModelAlias . '.' . $key, '<=', $timeArr[1]];
                }
            }

            //query去除字段
            $queryArr = [];
            if ($query) {
                $queryArr = explode('&', $query);
                $queryArr = array_filter($queryArr, function ($item) {
                    return !str_starts_with($item, 'pageNum=')
                        && !str_starts_with($item, 'pageSize=')
                        && !str_starts_with($item, 'order=')
                        && !str_starts_with($item, 'dateBetween')
                        && !str_starts_with($item, 'recycle=');
                });
                // kc_dump('queryArr', $queryArr);
            }

            //$where
            $otherFieldArr = [];
            if ($queryArr) {
                $otherFieldArr = array_map(function ($item) use ($mainModelAlias) {
                    $itemArr = explode('=', $item);
                    if (count($itemArr) === 2) {
                        $key = trim($itemArr[0]);
                        $value = trim($itemArr[1]);
                        // kc_dump('值[' . $value . ']的类型为:' . gettype($value));
                        if (preg_match('/^[\x{4e00}-\x{9fa5}\x{3400}-\x{4dbf}\x{20000}-\x{2a6df}]+$/u', $value)) {
                            //匹配汉字
                            return [$mainModelAlias . '.' . $key, 'like', '%' . $value . '%'];
                        } else if (preg_match('/^[0-9]+$/', $value)) {
                            //匹配数字
                            return [$mainModelAlias . '.' . $key, '=', $value];
                        } else if (preg_match('/^[a-zA-Z]+$/', $value)) {
                            //匹配字母
                            return [$mainModelAlias . '.' . $key, 'like', '%' . $value . '%'];
                        }
                    }
                    return [];
                }, $queryArr);
            }
            $where = array_merge($where, $otherFieldArr);
            // kc_dump('where', $where);
        } catch (Throwable $t) {
            $this->throw($t->getMessage());
        }


        return [$page, $pageSize, $order, $where];
    }

    /**
     * 验证
     * @param string $scene
     * @param array $param
     */
    protected function validate(string $scene = '', array $param = []): void
    {
        if ($this->needValidate) {
            if (!$this->validateClass || !class_exists($this->validateClass)) {
                $controller = (string)$this->request->controller;
                if (!str_contains($controller, '\\')) {
                    $controller = $this->request->controller_class;
                }
                $search = ["\\controller\\"];
                $replace = ["\\validate\\"];
                $plugin = $this->pluginName ?: get_pluginName();
                if ($controllerSuffix = config("plugin.{$plugin}.app.controller_suffix")) {
                    $search[] = $controllerSuffix;
                    $replace[] = $this->validateClassSuffix;
                }
                $this->validateClass = str_replace($search, $replace, $controller);
            }
            if (class_exists($this->validateClass)) {
                $validate = new $this->validateClass;
                // kc_dump('验证器', $validate);
                $validate->useZh(); //使用中文提示
                $validate->setDb(Db::connect(config('think-orm.default')));  //设置验证器Db
                if ($scene && $validate->hasScene($scene)) {
                    $validate->scene($scene);
                }
                if (!$validate->check($param ?: $this->request->all())) {
                    $this->throw($validate->getError());
                }
            }
        }
    }

    /**
     * 排除入库字段
     * @param array $params
     * @return array
     */
    protected function excludeFields(array $params): array
    {
        if (!is_array($this->preExcludeFields)) {
            $this->preExcludeFields = explode(',', (string)$this->preExcludeFields);
        }

        $this->preExcludeFields = array_merge($this->preExcludeFields, $this->excludeFields);

        foreach ($this->preExcludeFields as $field) {
            if (array_key_exists($field, $params)) {
                unset($params[$field]);
            }
        }
        return $params;
    }

    /**
     * @throws Exception
     */
    protected function checkActionAccess(): void
    {
        //是否允许访问
        if (!in_array($this->request->action, $this->allowAccessActions)) {
            throw new Exception('禁止访问该方法！');
        }
    }


}