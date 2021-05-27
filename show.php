<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>e袋洗</title>
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css">
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
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
        <legend><?php echo $userName; ?></legend>
    </fieldset>
    <table class="layui-table">
        <colgroup>
            <col width="50">
            <col width="130">
            <col width="80">
        </colgroup>
        <thead>
        <tr>
            <th>面额</th>
            <th>卡密</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lists as $code => $value) { ?>
            <?php if (! $isAdmin && $value['status'] != 1) {
                continue;
            } ?>
            <tr>
                <td>
                    <b style='color: <?php echo $value['denomination'] == 200 ? "red" : "green"; ?>;'>
                        <?php echo $value['denomination']; ?>
                    </b>
                </td>
                <td class="noselect">
                    <?php echo $value['status'] == 1 ? $code : "<s>" . $value['plaintext'] . "</s>"; ?>
                </td>
                <td>
                    <?php if ($value['status'] == 1) { ?>
                        <a href="javascript:;" class="copy" attr="<?php echo $code; ?>">复制</a>
                    <?php } else {
                        echo "<b style='color: red;'>{$value['status']}</b>";
                    } ?>
                    <?php if ($isAdmin) { ?>
                        <a href="javascript:;" class="delete" attr="<?php echo $code; ?>">删除</a>
                    <?php } ?>
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
            <label class="layui-form-label">卡密</label>
            <div class="layui-input-block">
                <input type="tel" name="code" required lay-verify="required" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">面额</label>
            <div class="layui-input-block">
                <select name="denomination" lay-verify="required">
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
<script src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script>
<script src="https://www.layuicdn.com/layui/layui.js"></script>
<script>
    layui.use('form', function () {
        layui.form.on('submit(formDemo)', function (data) {
            $.post("<?php echo $domain; ?>index.php?method=add&user=<?php echo $loginUser;?>", {params: JSON.stringify(data.field)},
                function (ret) {
                    if (JSON.parse(ret).status) {
                        window.location.reload();
                    } else {
                        alert(JSON.parse(ret).msg)
                    }
                });
            return false;
        });
    });
    $(".delete").click(function () {
        var code = $(this).attr('attr');
        layer.confirm('删除后不可恢复', function (index) {
            $.post("<?php echo $domain; ?>index.php?method=delete&user=<?php echo $loginUser;?>", {code: code},
                function (ret) {
                    window.location.reload();
                });
        });
    });
    $(".copy").click(function () {
        var code = $(this).attr('attr');
        layer.confirm('复制之后将自动删除', function (index) {
            $.post("<?php echo $domain; ?>index.php?method=copy&user=<?php echo $loginUser;?>", {code: code},
                function (ret) {
                    if (JSON.parse(ret).status) {
                        layer.prompt({
                                btn: ["确认"],
                                title: '剪切确认成功之后关闭',
                                closeBtn: false,
                                btnAlign: 'c',
                                value: JSON.parse(ret).msg,
                                yes: function (index, layero) {
                                    // 获取文本框输入的值
                                    var value = layero.find(".layui-layer-input").val();
                                    if (value) {
                                        alert("请剪切卡密：" + value);
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            }
                        );
                    } else {
                        alert(JSON.parse(ret).msg)
                        window.location.reload();
                    }
                });
            return false;
        });
    });
</script>
</body>
</html>
