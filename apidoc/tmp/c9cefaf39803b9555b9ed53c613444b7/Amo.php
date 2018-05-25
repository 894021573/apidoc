<?php 
namespace abbba138d1bf5ead5daeec6888323e4f1;


/**
 * @apiDefine AAA
 */
class Amo {
    /**
     * @apiDefine AAA
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

}