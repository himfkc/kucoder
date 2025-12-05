<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\event;

use Psr\SimpleCache\InvalidArgumentException;

class Menu
{
    /**
     * @throws InvalidArgumentException
     */
    function deleteCache($data): void
    {
        // dump('事件data',$data);
        if(is_array($data['key'])) {
            foreach ($data['key'] as $key) {
                delete_cache($key);
            }
        } else {
            delete_cache($data['key']);
        }
    }


}