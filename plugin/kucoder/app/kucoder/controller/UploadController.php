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


namespace kucoder\controller;

use Exception;
use kucoder\auth\AdminAuth;
use kucoder\auth\ApiAuth;
use kucoder\auth\AppMiniAuth;
use kucoder\constants\KcConst;
use kucoder\lib\KcFile;
use kucoder\lib\upload\KcUpload;
use kucoder\traits\ResponseTrait;
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
        $upload= KcUpload::upload();
        return $this->ok('上传成功', $upload);
    }
}