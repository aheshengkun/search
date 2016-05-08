<?php if (!defined('THINK_PATH')) exit();?><link href="/searchsys/Public/admin/css/skin.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="/searchsys/Public/admin/js/jquery-1.8.3.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <body style="background:#FFF;">
        <div class="wrap">
            <div class="mainBody">
            <form action="<?php echo U(MODULE_NAME.'/Access/accessEdit');?>" method="post">
                <div id="Right">
                    <div class="Item hr">
                        <div class="titlebt">权限分配</div>
                    </div>
                    <p>你正在为用户组：<b><?php echo ($info["name"]); ?></b> 分配权限 。</p>                    
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab">
                            <?php if(is_array($node)): $i = 0; $__LIST__ = $node;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$level1): $mod = ($i % 2 );++$i;?><tr>
                                    <td style="font-size: 14px;">
	                                    <label>
	                                    <input name="access[]" level="1" type="checkbox" obj="node_<?php echo ($level1["id"]); ?>" value='<?php echo ($level1["id"]); ?>_1'
	                                    <?php if($level1["access"]): ?>checked='checked'<?php endif; ?>/> 
		                                    <b>[应用]</b>
		                                    <?php echo ($level1["title"]); ?>
	                                    </label>
                                    </td>
                                </tr>
                                <?php if(is_array($level1['child'])): $i = 0; $__LIST__ = $level1['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$level2): $mod = ($i % 2 );++$i;?><tr>
                                        <td style="padding-left: 30px; font-size: 14px;">
                                        <label>
                                        <input name="access[]" level="2" type="checkbox" obj="node_<?php echo ($level1["id"]); ?>_<?php echo ($level2["id"]); ?>" value='<?php echo ($level2["id"]); ?>_2'
                                        <?php if($level2["access"]): ?>checked='checked'<?php endif; ?>/> 
                                        <b>[控制器]</b> 
                                        <?php echo ($level2["title"]); ?>
                                        </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 50px;">
                                            <?php if(is_array($level2['child'])): $i = 0; $__LIST__ = $level2['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$level3): $mod = ($i % 2 );++$i;?><label>
                                                <input name="access[]" level="3" type="checkbox" obj="node_<?php echo ($level1["id"]); ?>_<?php echo ($level2["id"]); ?>_<?php echo ($level3["id"]); ?>" value='<?php echo ($level3["id"]); ?>_3'
                                                <?php if($level3["access"]): ?>checked='checked'<?php endif; ?>/> 
                                                <?php echo ($level3["title"]); ?>
                                                </label>&nbsp;&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                        </table>
                        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>"/>
                    
                    <div class="commonBtnArea" >
                   		<input type="hidden" name="rid" value='<?php echo ($rid); ?>'/>
                   		<input type="submit" class="btn" style="margin:30px 0 30px 430px;" value="提交数据"/>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="clear"></div>
        <script type="text/javascript">
            $(function(){
                //为项目时候全选本项目所有操作
                $("input[level='1']").click(function(){
                    var obj=$(this).attr("obj")+"_";
                    $("input[obj^='"+obj+"']").prop("checked",$(this).prop("checked"));
                });
                //为模块时候全选本模块所有操作
                $("input[level='2']").click(function(){
                    var obj=$(this).attr("obj")+"_";
                    $("input[obj^='"+obj+"']").prop("checked",$(this).prop("checked"));
                    //分隔obj为数组
                    var tem=obj.split("_");
                    //将当前模块父级选中
                    if($(this).prop('checked')){
                        $("input[obj='node_"+tem[1]+"']").prop("checked","checked");
                    }
                });
                //为操作时只要有勾选就选中所属模块和所属项目
                $("input[level='3']").click(function(){
                    var tem=$(this).attr("obj").split("_");
                    if($(this).prop('checked')){
                        //所属项目
                        $("input[obj='node_"+tem[1]+"']").prop("checked","checked");
                        //所属模块
                        $("input[obj='node_"+tem[1]+"_"+tem[2]+"']").prop("checked","checked");
                    }
                });
                //清空当前已经选中的
                $(".empty").click(function(){
                    $("input[type='checkbox']").prop("checked",false);
                });
                $(".submit").click(function(){
                    commonAjaxSubmit();
                });
            });
        </script>
    </body>