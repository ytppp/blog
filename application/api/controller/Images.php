<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/4/13
 * Time: 15:48
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use think\Image;
use think\Db;

class Images extends Base
{
    /**
     * 保存图片到服务器
     * @return \think\response\Json
     */
    public function saveAvatar()
    {
        // 接受图片信息
        $imgInfo = Request::file('avatar');
        // 计算图片的md5散列值
        $md5 = $imgInfo->hash('md5');
        $imgObj = Db::table('blog_images')->where(['md5' => $md5])->find();
        // 判断图片是否重复上传
        if (!empty($imgObj)) { // 如果不为空，说明这个图片已经上传了，返回数据库中该图片的信息给前端
            $this->addSiteLog('从blog_images数据库中查找名为{md5}的图片成功', 'api/images/saveAvatar', 1);
            return json(
                [
                    'status' => 1,
                    'msg'    => '上传成功',
                    'data'   => [
                        'img_id'  => $imgObj['id'],
                        'img_url' => $imgObj['path']
                    ]
                ]
            );
        } else { // 图片第一次上传
            $info = $imgInfo->validate(
                [
                    'size' => 2000000,
                    'ext'  => 'jpg,jpeg,png,gif'
                ]
            )->move('uploads/');
            $image = Image::open('uploads/'.$info->getSaveName());
            $image->thumb(50, 50)->save('uploads/'.$info->getSaveName());
            if ($info) {
                $data = [
                    'path'        => '/uploads/'.$info->getSaveName(),
                    'md5'         => $md5,
                    'status'      => 1,
                    'create_time' => time(),
                ];
                if($img_id=Db::name('blog_images')->insertGetId($data)){
                    $this->addSiteLog('上传名为{$info->getSaveName()}的图片成功', 'api/images/saveAvatar', 1);
                    return json(
                        [
                            'status' => 1,
                            'msg'    => '上传成功',
                            'data'   => [
                                'img_id'  => $img_id,
                                'img_url' => '/uploads/'.$info->getSaveName()
                            ]
                        ]
                    );
                }else{
                    $this->addSiteLog('上传名为{$info->getSaveName()}的图片失败', 'api/images/saveAvatar', 0);
                    return json(
                        [
                            'status' => -1,
                            'msg'    => '上传图片失败'
                        ]
                    );
                }
            } else {
                $this->addSiteLog('上传图片失败', 'api/images/saveAvatar', 0);
                return json(
                    [
                        'status' => -1,
                        'msg'    => $imgInfo->getError()
                    ]
                );
            }
        }
    }

    // 删除图片
    public function delAvatar() {
    }

    // 富文本编辑器内的图片上传并压缩
    public function upload()
    {
        $images = []; // 保存图片在服务器上的地址
        $errors = []; // 保存错误信息
        $files = Request::file();
        foreach ($files as $file) {
            // 计算图片的md5散列值
            $md5 = $file->hash('md5');
            $imgObj = Db::table('blog_images')->where(['md5' => $md5])->find();
            // 判断图片是否重复上传
            if (!empty($imgObj)) {
                $this->addSiteLog('从blog_images数据库中查找名为{md5}的图片成功', 'api/images/upload', 1);
                $path = 'http://'.$_SERVER['SERVER_NAME'].$imgObj['path'];
                array_push($images, $path);
            } else {
                $info = $file->validate(
                    [
                        'size' => 2000000,
                        'ext'  => 'jpg,jpeg,png,gif'
                    ]
                )->move('uploads/');
                $image = Image::open('uploads/'.$info->getSaveName());
                $image->thumb(400, 300)->save('uploads/'.$info->getSaveName());
                if ($info) {
                    $data = [
                        'path'        => '/uploads/'.$info->getSaveName(),
                        'md5'         => $md5,
                        'status'      => 1,
                        'create_time' => time(),
                    ];
                    if($img_id = Db::name('blog_images')->insertGetId($data)){
                        $this->addSiteLog('上传名为{$info->getSaeName()}的图片成功', 'api/images/upload', 1);
                        $path = 'http://'.$_SERVER['SERVER_NAME'].'/uploads/'.$info->getSaeName();
                        array_push($images, $path);
                    } else {
                        $this->addSiteLog('上传图片失败', 'api/images/upload', 0);
                        array_push($errors, '上传图片失败');
                    }
                } else {
                    array_push($errors, $file->getError());
                }
            }
        }

        if (0 == count($errors)) {
            $msg['errno'] = 0;
            $msg['data'] = $images;
            return json($msg);
        } else {
            $msg['errno'] = 1;
            $msg['data'] = $images;
            return json($msg);
        }
    }
}