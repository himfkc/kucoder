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


use support\think\Cache;
use support\think\Db;

trait TableTrait
{
    public function getEnumFieldDataFromDb(string $table): array
    {
        return Cache::remember('admin:enumFieldData:' . $table, function () use ($table) {
            $table_schema = get_database();
            kc_dump('database:' . $table_schema, 'table:' . $table);
            $sql = "SELECT COLUMN_NAME,COLUMN_COMMENT from `information_schema`.`COLUMNS`"
                . " WHERE TABLE_SCHEMA = ? and TABLE_NAME = ? and (DATA_TYPE = 'enum' or DATA_TYPE='set' or DATA_TYPE='tinyint') ";
            // kc_dump($sql);
            $fieldData = Db::query($sql, [$table_schema, $table]);
            $fieldData = array_column($fieldData, 'COLUMN_COMMENT', 'COLUMN_NAME');

            // $data = [0 => []];
            $data = [];
            //enumField
            foreach ($fieldData as $name => $comment) {
                if (!str_contains($comment, ':')) continue;
                $c = explode(':', $comment)[1];
                $c = str_replace(['，', ' '], [',', ''], $c);
                $c2 = explode(',', $c);
                foreach ($c2 as $c3) {
                    if ($c3) {
                        $d = explode('=', $c3);
                        // $data[0][$name][$d[0]] = $d[1];
                        $data[$name][$d[0]] = $d[1];
                    }
                }
            }
            //enumTag
            /*$enumTag = [];
            if (is_array($data[0]) && count($data[0]) > 0) {
                $enumField = $data[0];
                $fields = Db::name('crud_log')->where('table_name', $noPrefixTable)->value('fields');
                $fields = json_decode($fields, true);
                $fields = array_filter($fields, function ($v) use ($enumField) {
                    return array_key_exists($v['name'], $enumField);
                });
                foreach ($fields as $v) {
                    if (isset($v['fieldStatusTagType']) && $v['fieldStatusTagType']) {
                        $tagArr = explode('&', $v['fieldStatusTagType']);
                        foreach ($tagArr as $tags) {
                            [$key, $tag] = explode('=', $tags);
                            $enumTag[$v['name']][$key] = $tag;
                        }
                    }
                }
                $data[1] = $enumTag;
            }*/

            return $data;
        }, get_env('cache_expire_time'));
    }

}