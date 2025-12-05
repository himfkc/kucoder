<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\controller;

use plugin\kucoder\app\kucoder\controller\AdminBase;
use support\Response;

class IndexController extends AdminBase
{
    public function index():Response
    {
        $adminInfo          = $this->auth->userInfo();
        unset($adminInfo['token'], $adminInfo['refresh_token']);

        $menus = $this->auth->getUserMenus();
        if (!$menus) {
            $this->error('无后台菜单，请联系管理员解决');
        }
        $this->success('', [
            'adminInfo'  => $adminInfo,
            'menus'      => $menus,
            'siteConfig' => [
                'siteName' => get_sys_config('site_name'),
                'version'  => get_sys_config('version'),
                'cdnUrl'   => full_url(),
                'apiUrl'   => Config::get('buildadmin.api_url'),
                'upload'   => keys_to_camel_case(get_upload_config(), ['max_size', 'save_name', 'allowed_suffixes', 'allowed_mime_types']),
            ],
            'terminal'   => [
                'phpDevelopmentServer' => str_contains($_SERVER['SERVER_SOFTWARE'], 'Development Server'),
                'npmPackageManager'    => Config::get('terminal.npm_package_manager'),
            ]
        ]);
    }
}