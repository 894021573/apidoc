<?php
/**
 *
 * @author: 洪涛
 * @date: 2018/1/29
 */

require_once 'ParseDocNote.php';
require_once 'Generate.php';
$dirs = require_once __DIR__ . '/../config/main.php';

$p = new \ParseDocNote($dirs);

// 获取注释中的文档定义内容
$notes = $p->getNotes();

// 处理内容
foreach ($notes as $title => $note) {

    // 处理每个类的文档内容
    foreach ($note as $classIndex => $classItem) {
        foreach ($notes[$title][$classIndex]['class_notes'] as $k => $v) {
            if ($k != 'apiDefine') {
                unset($notes[$title][$classIndex]['class_notes'][$k]);
            }
        }

        // 只处理apiDefine关键字后面的内容
        $classDefine = explode(' ', current($classItem['class_notes']['apiDefine']));
        $notes[$title][$classIndex]['class_notes']['api_title'] = isset($classDefine[0]) ? $classDefine[0] : '';
        $notes[$title][$classIndex]['class_notes']['api_description'] = isset($classDefine[1]) ? $classDefine[1] : '';
        $notes[$title][$classIndex]['class_notes']['api_author'] = isset($classDefine[2]) ? $classDefine[2] : '';

        unset($notes[$title][$classIndex]['class_notes']['apiDefine']);

        // 处理每个方法的文档内容
        foreach ($classItem['method_notes'] as $methodIndex => $methodItem) {

            // 处理apiParam关键字的内容
            $tempParams = [];
            foreach ($methodItem['apiParam'] as $paramItem) {
                $params = explode(' ', $paramItem);
                $temp['name'] = isset($params[0]) ? str_replace(['[', ']'], '', $params[0]) : '';
                $temp['isRequired'] = isset($params[0]) && preg_match('/^\[.*\]$/', $params[0]) ? '否' : '是';
                $temp['type'] = isset($params[1]) ? $params[1] : '';
                $temp['description'] = isset($params[2]) ? $params[2] : '';
                $temp['default'] = isset($params[3]) ? $params[3] : '';
                $tempParams[] = $temp;
            }
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['api_param'] = $tempParams;

            // 处理apiDefine关键字的内容
            $methodDefine = explode(' ', current($methodItem['apiDefine']));
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['api_title'] = isset($methodDefine[0]) ? $methodDefine[0] : '';
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['api_description'] = isset($methodDefine[1]) ? $methodDefine[1] : '';
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['api_author'] = isset($methodDefine[2]) ? $methodDefine[2] : '';
            unset($notes[$title][$classIndex]['method_notes'][$methodIndex]['apiDefine']);

            // 处理apiUrl关键字的内容
            $methodUrl = explode(' ', current($methodItem['apiUrl']));
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['api_method_type'] = isset($methodUrl[0]) ? $methodUrl[0] : '';
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['api_request_url'] = isset($methodUrl[1]) ? $methodUrl[1] : '';
            unset($notes[$title][$classIndex]['method_notes'][$methodIndex]['apiUrl']);

            // 处理apiRemark关键字的内容
            $remark = explode(' ', current($methodItem['apiRemark']));
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['api_remark'] = isset($remark[0]) ? $remark[0] : '';

            // 生成命名空间
            $notes[$title][$classIndex]['method_notes'][$methodIndex]['unique_id'] = md5(urlencode($title . $classIndex . $methodIndex));
        }
    }
}

//echo json_encode($notes);exit();

$generate = new Generate($notes);
