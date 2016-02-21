/*
@功能：购物车页面js
@作者：diamondwang
@时间：2013年11月14日
*/

$(function(){
	
	//减少
	$(".reduce_num").click(function(){
		var amount = $(this).parent().find(".amount");
		if (parseInt($(amount).val()) <= 1){
			alert("商品数量最少为1");
		} else{
			$(amount).val(parseInt($(amount).val()) - 1);
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});
		$("#total").text(total.toFixed(2));
                
                //更新购物车中的商品的数量信息
                var goods_id =  $(this).parent().find(".amount").attr('gid');
                var amount = $(amount).val();
                sync_car(goods_id,amount);
	});

	//增加
	$(".add_num").click(function(){
		var amount = $(this).parent().find(".amount");
		$(amount).val(parseInt($(amount).val()) + 1);
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
                
                //更新购物车中的商品的数量信息
                var goods_id =  $(this).parent().find(".amount").attr('gid');
                var amount = $(amount).val();
                sync_car(goods_id,amount);
	});

	//直接输入
	$(".amount").blur(function(){
		if (parseInt($(this).val()) < 1){
			alert("商品数量最少为1");
			$(this).val(1);
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
                
                
                //更新购物车中的商品的数量信息
                var goods_id =  $(this).attr('gid');
                var amount = $(this).val();
                sync_car(goods_id,amount);

	});
        
        $('.remove_goods').click(function(){
                //找到当前删除商品的商品id
                var grand_ele = $(this).parent().parent();
                var goods_id = grand_ele.find('.amount').attr('gid');
                var amount = 0;
                sync_car(goods_id,amount);
                //删除dom节点
                grand_ele.remove();
            
            
                //总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
                
                
                
        });
        
        console.debug(sync_car_url);
        
        /**
         * 同步商品数量
         * @returns {undefined}
         */
        function sync_car(goods_id,amount){
            var data = {goods_id:goods_id,amount:amount};
            $.getJSON(sync_car_url,data);
        }
});