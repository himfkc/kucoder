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


namespace plugin\kucoder\app\kucoder\lib;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use support\think\Cache;

class Captcha
{
    /**
     * 生成验证码
     * @param int $length
     * @param int $width
     * @param int $height
     * @return array
     * @throws InvalidArgumentException|ReflectionException
     */
    public static function create(int $length = 4, int $width = 150, int $height = 50): array
    {
        // 验证码长度
        $phraseBuilder = new PhraseBuilder($length);
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        // 验证码宽高
        $builder->build($width, $height);
        // 获得验证码图片二进制数据
        $img_content = $builder->get();
        $img_base64 = "data:image/" . $builder->getImageType() . ";base64," . base64_encode($img_content);
        // 缓存验证码
        $uuid = KcHelper::uuid(true);
        Cache::set('captcha:' . $uuid, strtolower($builder->getPhrase()), 180);
        return [
            'uuid' => $uuid,
            'img_base64' => $img_base64
        ];
    }

    /**
     * 校验验证码
     * @param string $uuid
     * @param string $captcha
     * @return bool
     * @throws InvalidArgumentException|ReflectionException
     */
    public static function check(string $uuid, string $captcha): bool
    {
        $check = Cache::get('captcha:' . $uuid) === strtolower($captcha);
        $check && Cache::delete('captcha:' . $uuid);
        return $check;
    }
}