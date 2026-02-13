<?php

return array (
  0 =>
  array (
    'title' => '系统管理',
    'icon' => 'link',
    'type' => 'dir',
    'plugin' => 'kucoder',
    'path' => 'system',
    'component' => '',
    'name' => 'KucoderSystem',
    'query' => '',
    'link_url' => '',
    'link_target' => '',
    'keepalive' => 0,
    'sort' => 1,
    'show' => 1,
    'remark' => '系统管理目录',
    'children' =>
    array (
      0 =>
      array (
        'title' => '管理员管理',
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/user',
        'component' => 'system/user',
        'name' => 'KucoderSystemUser',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/export',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/import',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/resetPwd',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/user/recycle',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => NULL,
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
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/role',
        'component' => 'system/role',
        'name' => 'KucoderSystemRole',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/role/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          1 =>
          array (
            'title' => '角色新增',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/role/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          2 =>
          array (
            'title' => '角色修改',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/role/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          3 =>
          array (
            'title' => '角色删除',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/role/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          4 =>
          array (
            'title' => '角色导出',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/role/export',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 5,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          5 =>
          array (
            'title' => '回收站',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/role/recycle',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => NULL,
            'keepalive' => 0,
            'sort' => 999,
            'show' => 1,
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
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/menu',
        'component' => 'system/menu',
        'name' => 'KucoderSystemMenu',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
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
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/menu/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          1 =>
          array (
            'title' => '菜单新增',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/menu/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          2 =>
          array (
            'title' => '菜单修改',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/menu/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          3 =>
          array (
            'title' => '菜单删除',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/menu/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          4 =>
          array (
            'title' => '回收站',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/menu/recycle',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => NULL,
            'keepalive' => 0,
            'sort' => 999,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          5 =>
          array (
            'title' => '导出插件菜单',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/menu/exportPluginMenu',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => NULL,
            'keepalive' => 0,
            'sort' => 999,
            'show' => 1,
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
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/dept',
        'component' => 'system/dept',
        'name' => 'KucoderSystemDept',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
        'keepalive' => 1,
        'sort' => 4,
        'show' => 1,
        'remark' => '部门管理菜单',
        'children' =>
        array (
          0 =>
          array (
            'title' => '部门查询',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dept/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          1 =>
          array (
            'title' => '部门新增',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dept/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          2 =>
          array (
            'title' => '部门修改',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dept/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          3 =>
          array (
            'title' => '部门删除',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dept/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          4 =>
          array (
            'title' => '回收站',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dept/recycle',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => NULL,
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
        'title' => '岗位管理',
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/post',
        'component' => 'system/post',
        'name' => 'KucoderSystemPost',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
        'keepalive' => 1,
        'sort' => 5,
        'show' => 0,
        'remark' => '岗位管理菜单',
        'children' =>
        array (
          0 =>
          array (
            'title' => '岗位查询',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/post/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          1 =>
          array (
            'title' => '岗位新增',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/post/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          2 =>
          array (
            'title' => '岗位修改',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/post/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          3 =>
          array (
            'title' => '岗位删除',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/post/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          4 =>
          array (
            'title' => '岗位导出',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/post/export',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 5,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
        ),
      ),
      5 =>
      array (
        'title' => '字典管理',
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/dict',
        'component' => 'system/dict',
        'name' => 'KucoderSystemDict',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
        'keepalive' => 1,
        'sort' => 6,
        'show' => 0,
        'remark' => '字典管理菜单',
        'children' =>
        array (
          0 =>
          array (
            'title' => '字典查询',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dict/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          1 =>
          array (
            'title' => '字典新增',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dict/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          2 =>
          array (
            'title' => '字典修改',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dict/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          3 =>
          array (
            'title' => '字典删除',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dict/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          4 =>
          array (
            'title' => '字典导出',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/dict/export',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 5,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
        ),
      ),
      6 =>
      array (
        'title' => '配置参数',
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/config',
        'component' => 'system/config',
        'name' => 'KucoderSystemConfig',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
        'keepalive' => 1,
        'sort' => 7,
        'show' => 1,
        'remark' => '参数设置菜单',
        'children' =>
        array (
          0 =>
          array (
            'title' => '参数查询',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/config/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          1 =>
          array (
            'title' => '参数新增',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/config/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          2 =>
          array (
            'title' => '参数修改',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/config/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          3 =>
          array (
            'title' => '参数删除',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/config/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          4 =>
          array (
            'title' => '参数导出',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/config/export',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 5,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
        ),
      ),
      7 =>
      array (
        'title' => '通知公告',
        'icon' => 'link',
        'type' => 'menu',
        'plugin' => 'kucoder',
        'path' => 'system/notice',
        'component' => 'system/notice',
        'name' => 'KucoderSystemNotice',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
        'keepalive' => 1,
        'sort' => 8,
        'show' => 0,
        'remark' => '通知公告菜单',
        'children' =>
        array (
          0 =>
          array (
            'title' => '公告查询',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/notice/index',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 1,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          1 =>
          array (
            'title' => '公告新增',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/notice/add',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 2,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          2 =>
          array (
            'title' => '公告修改',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/notice/edit',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 3,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
          3 =>
          array (
            'title' => '公告删除',
            'icon' => '#',
            'type' => 'button',
            'plugin' => 'kucoder',
            'path' => 'system/notice/delete',
            'component' => '',
            'name' => '',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 0,
            'sort' => 4,
            'show' => 1,
            'remark' => '',
            'children' =>
            array (
            ),
          ),
        ),
      ),
      8 =>
      array (
        'title' => '日志管理',
        'icon' => 'link',
        'type' => 'dir',
        'plugin' => 'kucoder',
        'path' => 'system/log',
        'component' => '',
        'name' => 'KucoderSystemLog',
        'query' => '',
        'link_url' => '',
        'link_target' => '',
        'keepalive' => 0,
        'sort' => 9,
        'show' => 1,
        'remark' => '日志管理菜单',
        'children' =>
        array (
          0 =>
          array (
            'title' => '操作日志',
            'icon' => 'link',
            'type' => 'menu',
            'plugin' => 'kucoder',
            'path' => 'monitor/operlog',
            'component' => 'monitor/operlog',
            'name' => 'KucoderMonitorOperlog',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
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
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/operlog/index',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 1,
                'show' => 1,
                'remark' => '',
                'children' =>
                array (
                ),
              ),
              1 =>
              array (
                'title' => '操作删除',
                'icon' => '#',
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/operlog/delete',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 2,
                'show' => 1,
                'remark' => '',
                'children' =>
                array (
                ),
              ),
              2 =>
              array (
                'title' => '详细信息',
                'icon' => '#',
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/operlog/detail',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 3,
                'show' => 1,
                'remark' => '',
                'children' =>
                array (
                ),
              ),
              3 =>
              array (
                'title' => '日志导出',
                'icon' => '#',
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/operlog/export',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 4,
                'show' => 1,
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
            'icon' => 'link',
            'type' => 'menu',
            'plugin' => 'kucoder',
            'path' => 'monitor/loginlog',
            'component' => 'monitor/loginlog',
            'name' => 'KucoderMonitorLoginlog',
            'query' => '',
            'link_url' => '',
            'link_target' => '',
            'keepalive' => 1,
            'sort' => 2,
            'show' => 1,
            'remark' => '登录日志菜单',
            'children' =>
            array (
              0 =>
              array (
                'title' => '登录查询',
                'icon' => '#',
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/loginlog/index',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 1,
                'show' => 1,
                'remark' => '',
                'children' =>
                array (
                ),
              ),
              1 =>
              array (
                'title' => '登录删除',
                'icon' => '#',
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/loginlog/delete',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 2,
                'show' => 1,
                'remark' => '',
                'children' =>
                array (
                ),
              ),
              2 =>
              array (
                'title' => '日志导出',
                'icon' => '#',
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/logininlog/export',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 3,
                'show' => 1,
                'remark' => '',
                'children' =>
                array (
                ),
              ),
              3 =>
              array (
                'title' => '账户解锁',
                'icon' => '#',
                'type' => 'button',
                'plugin' => 'kucoder',
                'path' => 'system/loginlog/unlock',
                'component' => '',
                'name' => '',
                'query' => '',
                'link_url' => '',
                'link_target' => '',
                'keepalive' => 0,
                'sort' => 4,
                'show' => 1,
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
);