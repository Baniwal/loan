$(document).ready(function($){

	$("#newcar_proposal").hide();
	$("#usedcar_proposal").hide();
	$("#serviceloan").hide();
	$("#serviceloan_result").hide();
	//2016.08.26 add
	$("#newcar_estimate").hide();

	//新車個別提案書（PDF）開く
	$("#newcar_proposal_open").on("click",function(){
		$("#newcar_result").hide(0,function(){
			$("#newcar_proposal").show();
		});
		return false;
	});

	//新車個別提案書（PDF）戻る
	$("#newcar_proposal_close").on("click",function(){
		$("#newcar_proposal").hide(0,function(){
			$("#newcar_result").show();
		});
		return false;
	});

	//2016.08.26 add
	//見積書（PDF）開く
	$("#newcar_estimate_open").on("click",function(){
		$("#newcar_result").hide(0,function(){
			$("#newcar_estimate").show();
		});
		return false;
	});

	//見積書（PDF）戻る
	$("#newcar_estimate_close").on("click",function(){
		$("#newcar_estimate").hide(0,function(){
			$("#newcar_result").show();
		});
		return false;
	});
	
	//比較見積り閉じる
	$(".window-close").on("click",function(){
		window.close();
		return false;
	});

	/* 2014.02.04 del by morita
	//サービスローンタブをクリック
	$("a.serviceloan-tab").on("click",function(){
		//サービスローンタブがクリックされたときの処理
		$("#newcar, #usedcar").hide(0,function(){
			$("#serviceloan").show();
		});
		return false;
	});

	//サービスローンの計算ボタンクリック
	$("#serviceloan_calc").on("click",function(){
		$("#serviceloan_input").hide(0,function(){
			$("#serviceloan_result").show();
		});
		return false;
	});
	*/

	//サービスローン戻る
	$("#serviceloan_result_close").on("click",function(){
		$("#serviceloan_result").hide(0,function(){
			$("#serviceloan_input").show();
		});
		return false;
	});
	
	//radioボタン
	$("label").click(function(){}); //古いiOS用
	$("ul.list-radio01 label input:disabled").closest('label').addClass('disabled');
	$("ul.list-radio01 label input:checked").closest('label').addClass('current');
	$("ul.list-radio01 label input").on('change', function(){
		$(this).radio_update();
	});

	//select box
	$(function(){
		$('div.select-custom01 select').custom_selectbox();
		$('div.select-custom02 select').custom_selectbox();
		$('div.select-custom03 select').custom_selectbox();
	});

});

//セレクトボックスにデザイン適用
$.fn.custom_selectbox = function(){
	return this.each(function(){
		var _self = $(this);
		var set_selectbox = function(){
			var _value = _self.find('option:selected').html();
			_self.siblings('div.inner').find('span').html(_value);
		}
		_self.siblings('div.inner').find('span').html('');
		_self.each(set_selectbox).on('change', set_selectbox);
	});
}

//セレクトボックスをdisabledに変更する時
$.fn.select_disabled_on = function(){
	return this.each(function(){
		$(this).prev('div.inner').addClass('disabled');
	});
}

//セレクトボックスをdisabledから戻す時
$.fn.select_disabled_off = function(){
	return this.each(function(){
		$(this).prev('div.inner').removeClass('disabled');
	});
}
//ラジオボタンをdisabledに変更する時
$.fn.radio_disabled_on = function(){
	return this.each(function(){
		$(this).closest('label').addClass('disabled');
	});
}

//ラジオボタンをdisabledから戻す時
$.fn.radio_disabled_off = function(){
	return this.each(function(){
		$(this).closest('label').removeClass('disabled');
	});
}

//特定のラジオボタンをチェック
$.fn.radio_check_on = function(){
	return this.each(function(){
		var _self = $(this);
		_self.closest('ul.list-radio01').find('label').removeClass('current');
		_self.closest('label').addClass('current');
	});
}

//同じグループのラジオボタンのチェックを外す
$.fn.radio_check_off = function(){
	return this.each(function(){
		$(this).closest('ul.list-radio01').find('label').removeClass('current');
	});
}

//ラジオボタンの更新
$.fn.radio_update = function(){
	return this.each(function(){
		$(this).closest('ul.list-radio01').find('label').removeClass('current');
		$(this).closest('label').addClass('current');
	});
}

