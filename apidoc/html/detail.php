<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>开始使用layui</title>
    <link rel="stylesheet" href="./html/layui/css/layui.css">
</head>
<body>

<?php
$routes = explode('|', $_GET['route']);
$title = $routes[0];
$classIndex = $routes[1];
$methodIndex = $routes[2];

$classArr = $projects[$title][$classIndex];
$classAuthor = $classArr['class_notes']['api_author'];
$classDescription = $classArr['class_notes']['api_description']; // 类描述暂未在页面体现

$methodArr = $classArr['method_notes'][$methodIndex];

$apiTitle = $methodArr['api_title'];
$apiDescription = $methodArr['api_description'];
$apiAuthor = $methodArr['api_author'];

$apiMethodType = $methodArr['api_method_type'];
$apiRequestUrl = $methodArr['api_request_url'];

$apiParam = $methodArr['api_param'];
$apiRemark = $methodArr['api_remark'];
$apiReturn = $methodArr['apiReturn'];

?>
<div class="layui-container" style="height: 100%;">

    <div class="layui-row">
        <div class="layui-col-md12" style="padding-top: 4%;">
            <h1><strong><?php echo $apiTitle ?></strong></h1>(作者：<?php echo $apiAuthor ? $apiAuthor : $classAuthor ?>)
        </div>
    </div>

    <div class="layui-row">
        <div class="layui-col-md12" style="padding-top: 4%;">
            <h5><strong>描述：</strong></h5><?php echo $apiDescription ?>
        </div>
    </div>

    <div class="layui-row">
        <div class="layui-col-md12" style="padding-top: 4%;">
            <h5><strong>请求URL：</strong></h5><?php echo '[' . $apiMethodType . ']' . '    ' . $apiRequestUrl ?>
        </div>
    </div>

    <div class="layui-row">
        <div class="layui-col-md12" style="padding-top: 4%;">
            <h5><strong>备注：</strong></h5><?php echo $apiRemark ?>
        </div>
    </div>

    <div class="layui-row">
        <div class="layui-col-md12" style="padding-top: 4%;">
            <h5><strong>参数：</strong></h5>
            <table class="layui-table" lay-skin="line">
                <thead>
                <tr>
                    <th>参数名</th>
                    <th>类型</th>
                    <th>必选</th>
                    <th>说明</th>
                    <th>默认值</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($apiParam as $param):
                    ?>
                    <tr>
                        <td><?php echo $param['name'] ?></td>
                        <td><?php echo $param['type'] ?></td>
                        <td><?php echo $param['isRequired'] ?></td>
                        <td><?php echo $param['description'] ?></td>
                        <td><?php echo $param['default'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="layui-row">
        <div class="layui-col-md12" style="padding-top: 4%;">
            <div style="margin-bottom: 30px">
                <h5><strong>返回示例：</strong></h5>
                <input class="layui-btn layui-btn-sm layui-btn-primary" id="with-comment" type="Button" value="生成星号"
                       style="display: inline-block;float: right;"/>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a class="layui-btn layui-btn-sm layui-btn-primary" href="http://www.json.cn/"
                   style="display: inline-block;float: right;margin-right: 10px;" target="_blank"> Json 在线格式化</a>
            </div>
            <div>
                <textarea rows="30" cols="100" id="return-text"><?php echo $apiReturn; ?></textarea>
            </div>
        </div>
    </div>
</div>

<script src="./html/layui/layui.js"></script>
<script src="./html/static/main.js"></script>
<script src="./html/static/jquery-3.3.1.min.js"></script>

<script>
    $("#with-comment").click(function () {
        var returnText = $("#return-text").val();

        if ($(this).val() == "生成星号") {
            returnText = returnText.replace(/\n/g, "\n*");
            $("#return-text").val('*' + returnText);
            $(this).val("取消星号")
        } else {
            returnText = returnText.replace(/[\*]*/g, "");
            $("#return-text").val(returnText);
            $(this).val("生成星号")
        }
    });
</script>

</body>
</html>
