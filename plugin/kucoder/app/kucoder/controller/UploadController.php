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
use kucoder\lib\upload\KcUpload;
use support\Response;
use Throwable;

class UploadController extends Base
{
    /**
     * 后端上传接口
     * @throws Exception|Throwable
     */
    public function upload(): Response
    {
        // 上传登录校验
        $uid = 0;
        $app = $this->request->post('app') ?: 'admin';
        if (config_app('kucoder', 'upload_need_login')) {
            $auth = match ($app) {
                KcConst::API_APP => ApiAuth::getInstance(),
                KcConst::APP_MINI_APP => AppMiniAuth::getInstance(),
                default => AdminAuth::getInstance(),
            };
            if (!$auth->isLogin()) {
                return $this->error('请先登录');
            }
            $uid = $auth->getId();
            kc_dump('上传登录验证成功:',['uid'=>$uid,'app'=>$app]);
        }
        $upload = KcUpload::upload($uid, $app);
        return $this->ok('上传成功', $upload);
    }
}