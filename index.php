<?php
$data  = array_filter(explode(PHP_EOL, file_get_contents("./code")));
$lists = [];
foreach ($data as $key => $value) {
    [$denomination, $code, $status] = array_filter(explode("-", $value));
    $lists[$key]['denomination'] = $denomination;
    $lists[$key]['code']         = $code;
    $lists[$key]['status']       = $status;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>测试 - layui</title>
    <link rel="stylesheet" href="layui/css/layui.css">
    <style>
        .noselect {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body>
<div class="layui-container">
    <div class="layui-progress" style="margin: 15px 0 30px;">
        <div class="layui-progress-bar" lay-percent="100%"></div>
    </div>

    <table class="layui-table">
        <colgroup>
            <col width="50">
            <col width="130">
            <col width="80">
        </colgroup>
        <thead>
        <tr>
            <th>昵称</th>
            <th>加入时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lists as $value) { ?>
            <tr>
                <td><?php echo $value['denomination']; ?></td>
                <td class="noselect">
                    <?php echo $value['status'] == 1 ? $value['code'] : "<s>" . $value['code'] . "</s>"; ?>
                </td>
                <td>
                    <?php if ($value['status'] == 1) { ?>
                        <a href="javascript:;" class="copy" attr="<?php echo $value['code']; ?>">复制</a>
                    <?php } ?>
                    <a href="javascript:;" class="delte" attr="<?php echo $value['code']; ?>">删除</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<div class="layui-container">
    <div class="layui-progress" style="margin: 15px 0 30px;">
        <div class="layui-progress-bar" lay-percent="100%"></div>
    </div>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">输入框</label>
            <div class="layui-input-block">
                <input type="text" name="title" required lay-verify="required" placeholder="请输入标题"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">选择框</label>
            <div class="layui-input-block">
                <select name="city" lay-verify="required">
                    <option value='200'>200</option>
                    <option value='500'>500</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
</div>
<script src="layui/jquery.js"></script>
<script src="layui/layui.js"></script>
<script>
    function copyText(text) {
        var textarea = document.createElement("input");//创建input对象
        var currentFocus = document.activeElement;//当前获得焦点的元素
        document.body.appendChild(textarea);//添加元素
        textarea.value = text;
        textarea.focus();
        if (textarea.setSelectionRange)
            textarea.setSelectionRange(0, textarea.value.length);//获取光标起始位置到结束位置
        else
            textarea.select();
        try {
            var flag = document.execCommand("copy");//执行复制
        } catch (eo) {
            var flag = false;
        }
        document.body.removeChild(textarea);//删除元素
        currentFocus.focus();
        return flag;
    }

    layui.use('form', function () {
        var form = layui.form;
        form.on('submit(formDemo)', function (data) {
            layer.msg(JSON.stringify(data.field));
            return false;
        });
    });
    var host = window.location.protocol + "//" + window.location.host;/*获取当前域名*/
    $(".copy").click(function () {
        var flag = copyText($(this).attr('attr'));
        layer.msg(flag ? "复制成功！" : "复制失败！");
        // layer.confirm('复制之后将自动删除,', function (index) {
        //     // var text = document.getElementById("text").innerText;
        //     // var input = document.getElementById("input");
        //     // input.value = text; // 修改文本框的内容
        //     // input.select(); // 选中文本
        //     // document.execCommand("copy"); // 执行浏览器复制命令
        //     //
        //     layer.msg("复制成功!" + $(this).attr("attr"));
        // });
    });
</script>
</body>
</html>
