<?php 
namespace a8e4be7817a506b540ea06b1f967e1082;


/**
 * @apiDefine api2222
 * @apiDefine2 标题 描述 作者
 */
class Demo {
    /**
     * @apiDefine 名称111 描述 作者
     * @apiUrl post url
     *
     * @apiParam [字段名] array 描述 默认值
     * @apiParam 字段名 string 描述 默认值
     * @apiParam [字段名] int 描述 默认值
     *
     * @apiRemark [备注]
     *
     * @apiReturn
     *{
     *    "num": 100,
     *    "list": {
     *        "user": {
     *            "name": "中国人"
     *        }
     *    }
     *}
     */
    public function getName()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET')
        {
            echo json_encode(['不是get请求']);
            exit();
        }

        if (empty($_GET['user_id']))
        {
            echo json_encode(['user_id不能为空']);
            exit();
        }

        $userID = $_GET['user_id'];

        $result = [
            'code' => 200,
            'message' => 'ok',
            'data' => [
                'user_id' => $userID,
                'name' => 'hello',
            ],
        ];

        echo json_encode($result);
    }

    /**
     * @apiDefine 名称222 描述 作者
     * @apiUrl post url
     *
     * @apiParam [字段名] array 描述 默认值
     * @apiParam 字段名 string 描述 默认值
     * @apiParam [字段名] int 描述 默认值
     *
     * @apiRemark [备注]
     *
     * @apiReturn
     *{
     *    "num": 100,
     *    "list": {
     *        "user": {
     *            "name": "中国人"
     *        }
     *    }
     *}
     */
    public function setName()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            echo json_encode(['不是post请求']);
            exit();
        }

        if (empty($_POST['user_id']))
        {
            echo json_encode(['user_id不能为空']);
            exit();
        }

        $userID = $_POST['user_id'];

        $result = [
            'code' => 200,
            'message' => 'ok',
            'data' => [
                'user_id' => $userID,
                'name' => 'world',
//                'file_content' => file_get_contents($_FILES['upload_file']['tmp_name']),
            ],
        ];

        echo json_encode($result);
    }
}