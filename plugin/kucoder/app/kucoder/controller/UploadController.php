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


namespace plugin\kucoder\app\kucoder\controller;

use Exception;
use plugin\kucoder\app\kucoder\auth\AdminAuth;
use plugin\kucoder\app\kucoder\auth\ApiAuth;
use plugin\kucoder\app\kucoder\auth\AppMiniAuth;
use plugin\kucoder\app\kucoder\constants\KcConst;
use plugin\kucoder\app\kucoder\lib\KcFile;
use plugin\kucoder\app\kucoder\lib\upload\KcUpload;
use plugin\kucoder\app\kucoder\traits\ResponseTrait;
use support\Response;
use Throwable;

class UploadController extends Base
{
    use ResponseTrait;

    /**
     * 后端上传接口
     * @throws Exception|Throwable
     */
    public function upload(): Response
    {
        // 上传登录校验
        if(config_app('kucoder','upload_need_login')){
            $auth = match(request()->app) {
                KcConst::API_APP => ApiAuth::getInstance(),
                KcConst::APP_MINI_APP => AppMiniAuth::getInstance(),
                default => AdminAuth::getInstance(),
            };
            if(!$auth->isLogin()){
                return $this->error('请先登录');
            }
            kc_dump('上传登录验证成功');
        }
        // $uploadedPath = KcFile::upload();
        $uploadedPath = KcUpload::upload();
        return $this->ok('上传成功', $uploadedPath);
    }
}