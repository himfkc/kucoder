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


namespace kucoder\interfaces;


/**
 * 对象存储(OSS)统一接口
 * 支持阿里云OSS、腾讯云COS、七牛云、华为云OBS等主流云厂商
 */
interface OssInterface
{
    /**
     * 上传文件
     * @param string $localFile 本地文件路径
     * @param string $remoteObject 远程对象名称(不包含bucket前缀)
     * @param array $options 额外选项(如content-type,headers等)
     * @return bool
     */
    public function uploadFile(string $localFile, string $remoteObject, array $options = []): bool;

    /**
     * 上传文件内容
     * @param string $content 文件内容
     * @param string $remoteObject 远程对象名称
     * @param array $options 额外选项
     * @return bool
     */
    public function uploadContent(string $content, string $remoteObject, array $options = []): bool;

    /**
     * 下载文件到本地
     * @param string $remoteObject 远程对象名称
     * @param string $localFile 本地保存路径
     * @return bool
     */
    public function downloadFile(string $remoteObject, string $localFile): bool;

    /**
     * 获取文件内容
     * @param string $remoteObject 远程对象名称
     * @return string|false
     */
    public function downloadContent(string $remoteObject): string|false;

    /**
     * 删除文件
     * @param string $remoteObject 远程对象名称
     * @return bool
     */
    public function deleteFile(string $remoteObject): bool;

    /**
     * 批量删除文件
     * @param array $remoteObjects 远程对象名称数组
     * @return array 返回删除结果 ['success' => [], 'failed' => []]
     */
    public function deleteFiles(array $remoteObjects): array;

    /**
     * 检查文件是否存在
     * @param string $remoteObject 远程对象名称
     * @return bool
     */
    public function fileExists(string $remoteObject): bool;

    /**
     * 复制文件
     * @param string $sourceObject 源对象名称
     * @param string $targetObject 目标对象名称
     * @return bool
     */
    public function copyFile(string $sourceObject, string $targetObject): bool;

    /**
     * 移动/重命名文件
     * @param string $sourceObject 源对象名称
     * @param string $targetObject 目标对象名称
     * @return bool
     */
    public function moveFile(string $sourceObject, string $targetObject): bool;

    /**
     * 获取文件元数据
     * @param string $remoteObject 远程对象名称
     * @return array|false 返回包含size,type,etag,lastModified等信息的数组
     */
    public function getMetadata(string $remoteObject): array|false;

    /**
     * 获取文件访问URL
     * @param string $remoteObject 远程对象名称
     * @param int $expires URL有效期(秒),0表示永久访问
     * @param array $options 额外选项
     * @return string
     */
    public function getUrl(string $remoteObject, int $expires = 0, array $options = []): string;

    /**
     * 获取临时访问URL(签名URL)
     * @param string $remoteObject 远程对象名称
     * @param int $expires URL有效期(秒)
     * @param array $options 额外选项
     * @return string
     */
    public function getSignedUrl(string $remoteObject, int $expires = 3600, array $options = []): string;

    /**
     * 列出指定前缀的文件
     * @param string $prefix 对象前缀
     * @param int $maxKeys 最大返回数量
     * @param string|null $marker 分页标记
     * @return array 返回文件列表
     */
    public function listFiles(string $prefix = '', int $maxKeys = 100, ?string $marker = null): array;

    /**
     * 设置文件访问权限
     * @param string $remoteObject 远程对象名称
     * @param string $acl 权限类型(public-read,private等,具体值由各云厂商实现定义)
     * @return bool
     */
    public function setAcl(string $remoteObject, string $acl): bool;

    /**
     * 获取文件访问权限
     * @param string $remoteObject 远程对象名称
     * @return string|false
     */
    public function getAcl(string $remoteObject): string|false;

    /**
     * 获取Bucket名称
     * @return string
     */
    public function getBucket(): string;

    /**
     * 设置Bucket名称
     * @param string $bucket
     * @return self
     */
    public function setBucket(string $bucket): self;

    /**
     * 获取存储区域
     * @return string
     */
    public function getRegion(): string;

    /**
     * 设置存储区域
     * @param string $region
     * @return self
     */
    public function setRegion(string $region): self;

    /**
     * 获取访问域名
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * 检查连接是否正常
     * @return bool
     */
    public function checkConnection(): bool;

}