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


namespace plugin\kucoder\app\admin\event;

use Psr\SimpleCache\InvalidArgumentException;

class Menu
{
    /**
     * @throws InvalidArgumentException
     */
    function deleteCache($data): void
    {
        // kc_dump('事件data',$data);
        if(is_array($data['key'])) {
            foreach ($data['key'] as $key) {
                delete_cache($key);
            }
        } else {
            delete_cache($data['key']);
        }
    }


}