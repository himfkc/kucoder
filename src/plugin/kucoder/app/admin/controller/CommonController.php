<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\controller;

use Exception;
use plugin\kucoder\app\kucoder\controller\AdminBase;
use plugin\kucoder\app\kucoder\lib\KcFile;
use support\Response;

class CommonController extends AdminBase
{
    /**
     * 不需要权限
     * @var array
     */
    protected array $noNeedRight = ['upload'];

    /**
     * 后端上传接口
     * @throws Exception
     */
    public function upload(): Response
    {
        $uploadedPath = KcFile::upload();
        return $this->ok('上传成功', $uploadedPath);
    }

}