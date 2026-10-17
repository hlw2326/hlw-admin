<?php

declare(strict_types=1);

/**
 * +----------------------------------------------------------------------
 * | HlwAdmin Plugin for HlwAdmin
 * +----------------------------------------------------------------------
 * | 版权所有 2014~2026 HlwAdmin [ hlwadmin.top ]
 * +----------------------------------------------------------------------
 * | 官方网站: https://hlwadmin.top
 * +----------------------------------------------------------------------
 */

namespace app\index\controller;

use think\admin\base\Controller;
use think\admin\service\ApiCryptService;

/**
 * API 信封加密联调控制器.
 *
 * 请求入口：
 * - GET  /index/api/publicKey 获取服务端 RSA 公钥
 * - POST /index/api/index     携带 x-client-* 请求头验证并回显已解密内容
 */
class Api extends Controller
{
    /**
     * 获取 API 信封加密所需的服务端 RSA 公钥。
     */
    public function publicKey(): void
    {
        $service = ApiCryptService::instance();
        $this->success('获取服务端公钥成功', [
            'algorithm'  => 'RSA',
            'public_key' => $service->getPublicKey(),
        ]);
    }

    /**
     * 回显 ApiDecrypt 中间件解密、验签后注入的请求数据。
     */
    public function index(): void
    {
        $this->success('API 加密请求验证成功', [
            'encrypted' => boolval($this->request->encrypted ?? false),
            'token'     => (string) ($this->request->token ?? ''),
            'device'    => (array) ($this->request->device ?? []),
            'data'      => (array) ($this->request->data ?? []),
        ]);
    }
}
