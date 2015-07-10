/**
 * Created by yangxingquan on 2015/7/8.
 *
 */

var nullmsg = '不能为空';
var succmsg = '验证成功';
var errormsg = '验证失败';


function checkValidate(_id){
    var items = $('#'+_id).find(":text,:password");
    var ret = true;
    items.each(function(){
        var item = $(this);
        if(!item.prop('validate_result')){
            doValidate(item);
            //给出返回结果
            ret = false;
            //跳出循环
            return false;
        }
    });

    //返回结果
    return ret;
}


function clearValidate(_id){
    $('#'+_id).find('._check_tips').html('');
    $('#'+_id).find(":text,:password").val('');
}


function checkValidateItem(_id){
    var item = $('#'+_id);
    doValidate(item);
    if(!item.prop('validate_result')){
        return false;
    }
    return true;
}

function clearValidateItem(_id){
    $('#'+_id).html('');
    $('#'+_id).val('');
}


function registValidate(_id){
    var items = $('#'+_id).find(":text,:password");
    items.each(function(){
        preValidate($(this));
    });
}


function preValidate(item){
    //在表单添加验证结果
    item.prop('validate_result',false);
    //在表单标签后添加提示标签
    var inhtml = "<span id='"+item.attr('id')+"_tips' class='_check_tips' style='line-height:40px;margin-left:10px;white-space:nowrap'></span>";
    if(item.next().length){
        item.next().after(inhtml);
    }else{
        item.after(inhtml);
    }

    if(item.attr('recheck')){
        var recheck = $('#'+item.attr('recheck'));
        var tips = $('#'+item.attr('id')+'_tips');
        recheck.change(function(){
            if(item.val().length){
                if(item.attr('errormsg')){
                    tips.html(item.attr('errormsg'));
                }else{
                    tips.html(errormsg);
                }
            }else{
                tips.html('');
            }

            item.prop('validate_result',false);
        });
    }

    //当失去焦点时检查
    item.blur(function(){
        doValidate(item);
    });

}



function doValidate(item){
    var tips = $('#'+item.attr('id')+'_tips');

    tips.html('');

    //检查是否为空
    if(!item.val().length){
        if(item.attr('nullmsg')){
            tips.html(item.attr('nullmsg'));
        }else{
            tips.html(nullmsg);
        }

        item.prop('validate_result',false);
        return;
    }


    //检查是否符合正则式
    if(item.attr('matchtype')){
        var patt = new RegExp(item.attr('matchtype'));
        if(!patt.test(item.val())){
            if(item.attr('errormsg')){
                tips.html(item.attr('errormsg'));
            }else{
                tips.html(errormsg);
            }

            item.prop('validate_result',false);
            return;
        }
    }

    if(item.attr('recheck')){
        var recheck = $('#'+item.attr('recheck'));
        if(item.val()!=recheck.val()){
            if(item.attr('errormsg')){
                tips.html(item.attr('errormsg'));
            }else{
                tips.html(errormsg);
            }

            item.prop('validate_result',false);
            return;
        }
    }


    //检查是否需要ajax验证
    if(item.attr('ajaxurl')){
        $.ajax({
            url:item.attr('ajaxurl'),
            type:"POST",
            timeout:1000,
            dataType:"json",
            data:{
                'name':item.attr('name'),
                'param':item.val()
            },

            success:function(data){
                if (data.status){
                    if(data.info!=''){
                        tips.html(data.info);
                    }else if(item.attr('succmsg')){
                        tips.html(item.attr('succmsg'));
                    }else{
                        tips.html(succmsg);
                    }
                    item.prop('validate_result',true);
                }else{
                    if(data.info!=''){
                        tips.html(data.info);
                    }else if(item.attr('errormsg')){
                        tips.html(item.attr('errormsg'));
                    }else{
                        tips.html(errormsg);
                    }
                    item.prop('validate_result',false);
                }
            },

            error:function(req, sta, err){
                //console.log(sta);
                item.prop('validate_result',false);
            }
        });

    }else{
        if(item.attr('succmsg')){
            tips.html(item.attr('succmsg'));
        }else{
            tips.html(succmsg);
        }

        item.prop('validate_result',true);
    }
}
