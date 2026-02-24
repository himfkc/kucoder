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


namespace plugin\kucoder\app\kucoder\lib\upload;

use Throwable;
use Exception;
use plugin\kucoder\app\kucoder\interfaces\OssInterface;
use support\think\cache;
use plugin\kucoder\app\admin\model\Config;
use LocalUpload;

/**
 * kucoder上传类库 支持本地存储、oss存储 支持oss存储时本地备份
 */
class KcUpload
{
    private OssInterface $oss;

    private static self $instance;

    public static function getInstance(): object|OssInterface|string|null
    {
        $configs = config_in_db('kucoder');
        // $mode = $configs['upload_mode'];
        // $ossType = $configs['oss_type'];
        if($configs['upload_mode'] === 'local'){
            return  LocalUpload::class;
        }
        if($configs['upload_mode'] === 'oss'){
            $oss =  match($configs['oss_type']){
                'alioss' => function(){
                    return \plugin\alioss\api\PluginAlioss::class ?? null;
                },
                'txoss' => function(){
                    return \plugin\txoss\api\PluginTxoss::class ?? null;
                },
                'qnoss' => function(){
                    return \plugin\qnoss\api\PluginQnoss::class ?? null;
                },
                'hwoss' => function(){
                    return \plugin\hwoss\api\PluginHwoss::class ?? null;
                },
                default => function(){
                    return null;
                },
            };
            if(class_exists($oss())){
                return $oss();
            }
            return null;
        }
    }

    public static function upload(){
        try{
            $upload = self::getInstance();
            if($upload){
                return $upload->upload();
            }else{

            }
        }catch(Throwable $t){
            throw new Exception('上传失败：'.$t->getMessage());
        }
        
    }

}