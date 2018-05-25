<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>开始使用layui</title>
    <link rel="stylesheet" href="./html/layui/css/layui.css">
</head>
<body>

<div class="layui-container" style="height: 100%;">
    <div class="layui-row" style="height: 3%;"></div>
    <div class="layui-row">
        <div class="layui-col-md3" style="padding-top: 4%;">
            <ul id="tree"></ul>
        </div>
        <div class="layui-col-md9">
            <div class="layui-tab" lay-allowClose="true" lay-filter="content-tab" id="content-tab">
                <ul class="layui-tab-title">
<!--                    <li class="layui-this">网站设置</li>-->
<!--                    <li>用户管理</li>-->
<!--                    <li>权限分配</li>-->
<!--                    <li>商品管理</li>-->
<!--                    <li>订单管理</li>-->
                </ul>
                <div class="layui-tab-content">
<!--                    <div class="layui-tab-item layui-show">内容1</div>-->
<!--                    <div class="layui-tab-item">内容2</div>-->
<!--                    <div class="layui-tab-item">内容3</div>-->
<!--                    <div class="layui-tab-item">内容4</div>-->
<!--                    <div class="layui-tab-item">内容5</div>-->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="./html/layui/layui.js"></script>
<script src="./html/static/main.js"></script>
<script src="./html/static/jquery-3.3.1.min.js"></script>
<script>
    layui.use('tree', function () {
        var json = '<?php echo $json?>';
        json = JSON.parse(json);

        layui.tree({
            elem: '#tree' //传入元素选择器
            , nodes: json
            ,click: function(node){
                if(!node.hasOwnProperty('children')){
                    console.log(node); //node即为当前点击的节点数据

                    layui.use('element', function(){
                        var element = layui.element;

                        if($("li[lay-id='"+node.id+"']").length === 0){
                            element.tabAdd('content-tab', {
                                title: node.name
                                ,content: '<div class="layui-tab-item layui-show"><iframe src="./detail.php?route=' + node.route + '" width="100%" height="2000" frameborder="0"></iframe></div>' //支持传入html
                                ,id: node.id
                            });
                        }

                        element.tabChange('content-tab', node.id); //切换到 lay-id="yyy" 的这一项
                    });
                }
            }
        });
    });
</script>
</body>
</html>