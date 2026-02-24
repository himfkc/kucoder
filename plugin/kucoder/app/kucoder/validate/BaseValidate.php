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


namespace plugin\kucoder\app\kucoder\validate;

use support\think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Validate;

class BaseValidate extends Validate
{
    // 唯一性验证时是否排除软删除数据
    protected bool $excludeDeleted = true;

    // 软删除字段名
    protected string $excludeDeletedField = 'delete_time';

    /**
     *  验证字段唯一性 thinkorm内置的unique:table,field 验证规则默认查询已软删除的数据 此处验证唯一不包含软删除数据 并添加了查询条件
     * 'path|路由path'=>'checkPath:menu.button', $value提交的值, $rule即menu.button，$data所有提交数据，$field要验证的字段path, $fieldName要验证的字段标题’路由path‘
     * 'name|标识'=>'checkUnique:table,status=1&delete_time=null,xxx=yyy', 则$rule为table,status=1&delete_time=null,xxx=yyy
     * @param $value
     * @param $rule
     * @param array $data
     * @param string $field
     * @param string $fieldName
     * @return string|bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function checkUnique($value, $rule, array $data = [], string $field = '', string $fieldName = ''): string|bool
    {
        if (!$value) {
            return true;
        }
        $ruleArr = explode(',', $rule);
        $table = $ruleArr[0];
        $where = [[$field, '=', $value]];
        if ($this->excludeDeleted) {
            $where[] = [$this->excludeDeletedField, '=', null];
        }
        $whereStr = isset($ruleArr[1]) ? $ruleArr[1] : [];
        if ($whereStr) {
            $whereArr = explode('&', $whereStr);
            $sep = ['=', '<>', '>', '<', '>=', '<='];
            foreach ($whereArr as $item) {
                foreach ($sep as $s) {
                    if (str_contains($item, $s)) {
                        $itemArr = explode($s, $item);
                        $where[] = [$itemArr[0], $s, $itemArr[1]];
                        //跳过当前子循环（内层循环）的剩余代码，并立即跳到 外层循环 的下一次迭代开始处
                        continue 2;
                    }
                }
            }
        }
        $res = Db::name($table)->where($where)->find();
        if ($res) {
            return "{$fieldName}已存在";
        }
        return true;
    }
}