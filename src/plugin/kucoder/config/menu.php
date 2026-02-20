<?php

return array (
  0 => 
  array (
    'title' => '系统管理',
    'icon' => 'ep:Setting',
    'plugin' => 'kucoder',
    'type' => 'dir',
    'path' => 'system',
    'component' => '',
    'name' => '',
    'query' => '',
    'link_url' => '',
    'keepalive' => 0,
    'sort' => 1,
    'show' => 1,
    'remark' => '系统管理目录',
    'children' => 
    array (
      0 => 
      array (
        'title' => '管理员管理',
        'icon' => 'ep:UserFilled',
        'plugin' => 'kucoder',
        'type' => 'menu',
        'path' => 'system/user',
        'component' => 'system/user',
        'name' => 'kucoder_system_user',
        'query' => '',
        'link_url' => '',
        'keepalive' => 1,
        'sort' => 1,
        'show' => 1,
        'remark' => '用户管理菜单',
        'children' => 
        array (
          0 => 
          array (
            'title' => '用户查询',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'title' => '用户新增',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'title' => '用户修改',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'title' => '用户删除',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'title' => '用户导出',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/export',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 5,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          5 => 
          array (
            'title' => '用户导入',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/import',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 6,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          6 => 
          array (
            'title' => '重置密码',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/resetPwd',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 7,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          7 => 
          array (
            'title' => '回收站',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/user/trueDel',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
        ),
      ),
      1 => 
      array (
        'title' => '角色管理',
        'icon' => 'peoples',
        'plugin' => 'kucoder',
        'type' => 'menu',
        'path' => 'system/role',
        'component' => 'system/role',
        'name' => 'kucoder_system_role',
        'query' => '',
        'link_url' => '',
        'keepalive' => 1,
        'sort' => 2,
        'show' => 1,
        'remark' => '角色管理菜单',
        'children' => 
        array (
          0 => 
          array (
            'title' => '角色查询',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/role/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'title' => '角色新增',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/role/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'title' => '角色修改',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/role/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'title' => '角色删除',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/role/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'title' => '角色导出',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/role/export',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 5,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          5 => 
          array (
            'title' => '回收站',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/role/trueDel',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
        ),
      ),
      2 => 
      array (
        'title' => '菜单管理',
        'icon' => 'ep:Menu',
        'plugin' => 'kucoder',
        'type' => 'menu',
        'path' => 'system/menu',
        'component' => 'system/menu',
        'name' => 'kucoder_system_menu',
        'query' => '',
        'link_url' => '',
        'keepalive' => 1,
        'sort' => 3,
        'show' => 1,
        'remark' => '菜单管理菜单',
        'children' => 
        array (
          0 => 
          array (
            'title' => '菜单查询',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/menu/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'title' => '菜单新增',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/menu/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'title' => '菜单修改',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/menu/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'title' => '菜单删除',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/menu/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'title' => '回收站',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/menu/trueDel',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          5 => 
          array (
            'title' => '导出插件菜单',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/menu/exportPluginMenu',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
        ),
      ),
      3 => 
      array (
        'title' => '部门管理',
        'icon' => 'tree',
        'plugin' => 'kucoder',
        'type' => 'menu',
        'path' => 'system/dept',
        'component' => 'system/dept',
        'name' => 'kucoder_system_dept',
        'query' => '',
        'link_url' => '',
        'keepalive' => 1,
        'sort' => 99,
        'show' => 1,
        'remark' => '部门管理菜单',
        'children' => 
        array (
          0 => 
          array (
            'title' => '部门查询',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/dept/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'title' => '部门新增',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/dept/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'title' => '部门修改',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/dept/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'title' => '部门删除',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/dept/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'title' => '回收站',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/dept/trueDel',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
        ),
      ),
      4 => 
      array (
        'title' => '会员管理',
        'icon' => 'user',
        'plugin' => 'kucoder',
        'type' => 'menu',
        'path' => 'system/member',
        'component' => 'system/member',
        'name' => 'kucoder_system_member',
        'query' => '',
        'link_url' => '',
        'keepalive' => 1,
        'sort' => 100,
        'show' => 1,
        'remark' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => '查询',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/member/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '#',
            'keepalive' => 0,
            'sort' => 0,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'title' => '新增',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/member/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '#',
            'keepalive' => 0,
            'sort' => 0,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'title' => '修改',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/member/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '#',
            'keepalive' => 0,
            'sort' => 0,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'title' => '删除',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/member/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '#',
            'keepalive' => 0,
            'sort' => 0,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'title' => '回收站',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/member/trueDel',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '#',
            'keepalive' => 0,
            'sort' => 0,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          5 => 
          array (
            'title' => '详情',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'system/member/info',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
        ),
      ),
      5 => 
      array (
        'title' => '配置管理',
        'icon' => 'ep:Setting',
        'plugin' => 'kucoder',
        'type' => 'dir',
        'path' => 'system/config',
        'component' => '',
        'name' => '',
        'query' => '',
        'link_url' => '',
        'keepalive' => 0,
        'sort' => 998,
        'show' => 1,
        'remark' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => '系统配置',
            'icon' => 'system',
            'plugin' => 'kucoder',
            'type' => 'menu',
            'path' => 'system/config/config',
            'component' => 'system/config/config',
            'name' => 'kucoder_system_config_config',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 7,
            'show' => 1,
            'remark' => '参数设置菜单',
            'children' => 
            array (
              0 => 
              array (
                'title' => '配置查询',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/config/index',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 1,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              1 => 
              array (
                'title' => '配置新增',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/config/add',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 2,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              2 => 
              array (
                'title' => '配置修改',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/config/edit',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 3,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              3 => 
              array (
                'title' => '配置删除',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/config/delete',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 4,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              4 => 
              array (
                'title' => '刷新配置缓存',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/config/refreshCache',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 999,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
            ),
          ),
          1 => 
          array (
            'title' => '配置分组',
            'icon' => 'dict',
            'plugin' => 'kucoder',
            'type' => 'menu',
            'path' => 'system/config/configGroup',
            'component' => 'system/config/configGroup',
            'name' => 'kucoder_system_config_configGroup',
            'query' => '',
            'link_url' => '',
            'keepalive' => 1,
            'sort' => 999,
            'show' => 1,
            'remark' => '',
            'children' => 
            array (
              0 => 
              array (
                'title' => '查询',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/configGroup/index',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '#',
                'keepalive' => 0,
                'sort' => 0,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              1 => 
              array (
                'title' => '详情',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/configGroup/info',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '#',
                'keepalive' => 0,
                'sort' => 0,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              2 => 
              array (
                'title' => '新增',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/configGroup/add',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '#',
                'keepalive' => 0,
                'sort' => 0,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              3 => 
              array (
                'title' => '修改',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/configGroup/edit',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '#',
                'keepalive' => 0,
                'sort' => 0,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              4 => 
              array (
                'title' => '删除',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/configGroup/delete',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '#',
                'keepalive' => 0,
                'sort' => 0,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              5 => 
              array (
                'title' => '回收站',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/config/configGroup/trueDel',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '#',
                'keepalive' => 0,
                'sort' => 0,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
            ),
          ),
        ),
      ),
      6 => 
      array (
        'title' => '日志管理',
        'icon' => 'skill',
        'plugin' => 'kucoder',
        'type' => 'dir',
        'path' => 'system/log',
        'component' => '',
        'name' => '',
        'query' => '',
        'link_url' => '',
        'keepalive' => 0,
        'sort' => 999,
        'show' => 1,
        'remark' => '日志管理菜单',
        'children' => 
        array (
          0 => 
          array (
            'title' => '操作日志',
            'icon' => 'skill',
            'plugin' => 'kucoder',
            'type' => 'menu',
            'path' => 'system/log/operLog',
            'component' => 'system/log/operLog',
            'name' => 'kucoder_system_log_operLog',
            'query' => '',
            'link_url' => '',
            'keepalive' => 1,
            'sort' => 1,
            'show' => 1,
            'remark' => '操作日志菜单',
            'children' => 
            array (
              0 => 
              array (
                'title' => '操作查询',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/log/operLog/index',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 1,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              1 => 
              array (
                'title' => '操作删除',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/log/operLog/delete',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 2,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              2 => 
              array (
                'title' => '清空日志',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/log/operLog/clean',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 3,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
            ),
          ),
          1 => 
          array (
            'title' => '登录日志',
            'icon' => 'skill',
            'plugin' => 'kucoder',
            'type' => 'menu',
            'path' => 'system/log/loginLog',
            'component' => 'system/log/loginLog',
            'name' => 'kucoder_system_log_loginLog',
            'query' => '',
            'link_url' => '',
            'keepalive' => 1,
            'sort' => 2,
            'show' => 1,
            'remark' => '登录日志菜单',
            'children' => 
            array (
              0 => 
              array (
                'title' => '登录日志列表',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/log/loginLog/index',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 1,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              1 => 
              array (
                'title' => '登录日志删除',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/log/loginLog/delete',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 2,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
              2 => 
              array (
                'title' => '登录日志清空',
                'icon' => '#',
                'plugin' => 'kucoder',
                'type' => 'button',
                'path' => 'system/log/loginLog/clean',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'keepalive' => 0,
                'sort' => 999,
                'show' => 0,
                'remark' => '',
                'children' => 
                array (
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  1 => 
  array (
    'title' => '插件管理',
    'icon' => 'ep:Link',
    'plugin' => 'kucoder',
    'type' => 'dir',
    'path' => 'plugin',
    'component' => '',
    'name' => '',
    'query' => '',
    'link_url' => '',
    'keepalive' => 0,
    'sort' => 999,
    'show' => 1,
    'remark' => '',
    'children' => 
    array (
      0 => 
      array (
        'title' => '插件市场',
        'icon' => 'ep:Grid',
        'plugin' => 'kucoder',
        'type' => 'menu',
        'path' => 'plugin/market',
        'component' => 'plugin/market',
        'name' => 'kucoder_plugin_market',
        'query' => '',
        'link_url' => '',
        'keepalive' => 1,
        'sort' => 999,
        'show' => 1,
        'remark' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => '购买插件',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'plugin/market/buy',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
        ),
      ),
      1 => 
      array (
        'title' => '插件列表',
        'icon' => 'ep:List',
        'plugin' => 'kucoder',
        'type' => 'menu',
        'path' => 'plugin/pluginLocal',
        'component' => 'plugin/pluginLocal',
        'name' => 'kucoder_plugin_pluginLocal',
        'query' => '',
        'link_url' => '',
        'keepalive' => 0,
        'sort' => 999,
        'show' => 1,
        'remark' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => '查询',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'plugin/pluginLocal/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'title' => '导入本地调试插件',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'plugin/pluginLocal/importLocalPlugin',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'title' => '安装插件',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'plugin/pluginLocal/install',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'title' => '升级插件',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'plugin/pluginLocal/update',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'title' => '卸载插件',
            'icon' => '#',
            'plugin' => 'kucoder',
            'type' => 'button',
            'path' => 'plugin/pluginLocal/uninstall',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'keepalive' => 0,
            'sort' => 999,
            'show' => 0,
            'remark' => '',
            'children' => 
            array (
            ),
          ),
        ),
      ),
    ),
  ),
);