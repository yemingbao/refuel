<?php

namespace app\cityapi\controller;

use app\api\common\Common;
use think\Db;

/*
*
* 创建小程序导入数据
*/


class InitApplet
{


    public function init($sid, $applet_id)
    {


        $applet_id = (int)$applet_id;
        $sid = (int)$sid;
        $this->bannerData($sid,$applet_id);

        $classids = $this->classData($sid, $applet_id);
        // for ($i = 0; $i < count($bannerData); $i++) {
        //     $data = ['applet_id' => $applet_id, 'picture' => $bannerData[$i][0], 'sort' => $bannerData[$i][1]];
        //     Db::table('banners')->insert($data);
        // }
        return "成功了";
    }


    public function goodsData($id)
    {
        $res = Db::table('goods')->where('class_id', $id)->select();

        return $res;
    }

    public function goodsSpuData($id, $goods_id)
    {
        $res = Db::table('picture')->where('field_id', $id)
            ->where('table_id', 1)
            ->select();
        foreach ($res as $key => $value) {
            // $value = $value->toArrry();
            $value['picture_id'] = null;
            $value['field_id'] = $goods_id;
            Db::table('picture')->insertGetId($value);
        }
        return;
    }

    public function goodsbannerData($id, $goods_id)
    {
        $res = Db::table('goods_banners')->where('goods_id', $id)->select();
        foreach ($res as $key => $value) {
            // $value = $value->toArrry();
            $value['goods_banner_id'] = null;
            $value['goods_id'] = $goods_id;
            Db::table('goods_banners')->insertGetId($value);
        }
        return;
    }

    public function goodsattributeData($id, $goods_id)
    {
        $res = Db::table('goods_attribute')->where('goods_id', $id)->select();
        foreach ($res as $key => $value) {
            // $value = $value->toArrry();
            $value['goods_attribute_id'] = null;
            $value['goods_id'] = $goods_id;
            Db::table('goods_attribute')->insertGetId($value);
        }
        return;
    }

    public function goodsparameterData($id, $goods_id)
    {
        $res = Db::table('goods_parameter')->where('goods_id', $id)->select();
        foreach ($res as $key => $value) {
            // $value = $value->toArrry();
            $value['goods_parameter_id'] = null;
            $value['goods_id'] = $goods_id;
            Db::table('goods_parameter')->insertGetId($value);
        }
        return;
    }

    public function bannerData($sid, $id)
    {
        $res = Db::table('banners')
            ->where('applet_id', $sid)
            ->where('delete_time IS NULL')
            ->select();
        foreach ($res as $key => $value) {
            // $value = $value->toArrry();
            $value['banner_id'] = null;
            $value['applet_id'] = $id;
            Db::table('banners')->insertGetId($value);
        }
        return;
    }

    public function classData($sid, $id)
    {

        $res = Db::table('cat')
            ->where('applet_id', $sid)
            ->where('delete_time IS NULL')
            ->where('parent_id IS NULL')
            ->select();

        $classids = array();
            foreach ($res as $key => $value) {
                // $value = $value->toArrry();
                $one_cat_id = $value['cat_id'];
                $value['cat_id'] = null;
                $value['applet_id'] = $id;
                $goodsData = Db::table('info')
                    ->where(['applet_id' => $sid, 'cat_id' => $one_cat_id])
                    ->select();


                $new_one_cat_id = Db::table('cat')->insertGetId($value);


                foreach ($goodsData as $gk => $gv) {
                    $gv['applet_id'] = $id;
                    $gv['cat_id'] = $new_one_cat_id;
                    $old_goods_id = $gv['info_id'];
                    $gv['info_id'] = null;
                    $goods_id = Db::table('info')->insertGetId($gv);
                    $this->goodsSpuData($old_goods_id, $goods_id);
                }


                $subres = Db::table('cat')
                    ->where('applet_id', $sid)
                    ->where('delete_time IS NULL')
                    ->where('parent_id', $one_cat_id)
                    ->select();

                foreach ($subres as $sub_cat_k => $sub_cat_v) {
                    $sub_cat_id = $sub_cat_v['cat_id'];
                    $goodsData = Db::table('info')
                        ->where(['applet_id' => $sid, 'cat_id' => $sub_cat_id])
                        ->select();
                    $sub_cat_v['cat_id'] = null;
                    $sub_cat_v['applet_id'] = $id;
                    $sub_cat_v['parent_id'] = $new_one_cat_id;
                    $new_two_cat_id = Db::table('cat')->insertGetId($sub_cat_v);

                    foreach ($goodsData as $gk => $gv) {
                        $gv['applet_id'] = $id;
                        $gv['cat_id'] = $new_two_cat_id;
                        $old_goods_id = $gv['info_id'];
                        $gv['info_id'] = null;
                        $goods_id = Db::table('info')->insertGetId($gv);
                        $this->goodsSpuData($old_goods_id, $goods_id);
                    }
                }

            }

        return $classids;
    }
}


