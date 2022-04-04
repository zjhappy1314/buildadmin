<?php

namespace app\admin\controller;

use think\Exception;
use think\exception\FileException;
use app\common\library\Upload;
use app\common\controller\Backend;
use think\facade\Db;

class Ajax extends Backend
{
    protected $noNeedPermission = ['upload', 'buildSuffixSvg', 'getTablePk'];

    public function initialize()
    {
        parent::initialize();
    }

    public function upload()
    {
        $file = $this->request->file('file');
        try {
            $upload                = new Upload($file);
            $attachment            = $upload->upload(null, $this->auth->id);
            $attachment['fullurl'] = full_url($attachment['url']);
            unset($attachment['createtime'], $attachment['quote']);
        } catch (Exception | FileException $e) {
            $this->error($e->getMessage());
        }

        $this->success(__('File uploaded successfully'), [
            'file' => $attachment
        ]);
    }

    public function buildSuffixSvg()
    {
        $suffix     = $this->request->param('suffix', 'file');
        $background = $this->request->param('background');
        $content    = build_suffix_svg((string)$suffix, (string)$background);
        return response($content, 200, ['Content-Length' => strlen($content)])->contentType('image/svg+xml');
    }

    public function getTablePk($table = null)
    {
        if (!$table) {
            $this->error(__('Parameter error'));
        }
        $tablePk = Db::table($table)->getPk();
        $this->success('', ['pk' => $tablePk]);
    }
}