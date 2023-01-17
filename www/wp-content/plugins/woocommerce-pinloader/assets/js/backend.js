jQuery(document).ready(function($){

	$('.shortcode-input').on('click', function(){
		$(this).focus().select();
	});

	$(".multiple_action_toggle").on('click', function(){
		var checkBoxes = $(".multiple_action");
		checkBoxes.trigger("click");
	});

	//$('.color-field').wpColorPicker();

	if($('.autocomplete').length){
		$('.autocomplete').autocomplete({
			source: globals.ajax_url + "?action=" + globals.autocomplete_get_products_action + "&nonce=" + globals.nonce,
			minLength: 2,
			delay: 500,
			position: {my: "right top", at: "right bottom"},
			response: function(event, ui){
				//console.log(ui);
			},
			select: function(event, ui){
				//console.log(ui);
				//console.log("Selected: " + ui.item.value + " aka " + ui.item.id);
				event.preventDefault();
				$(this).val(ui.item.id);
				$(this).parents('tr').find('.group-check').removeClass('hide');
			}
		}).on('keyup blur', function(){
			if(!$(this).val()){
				$(this).parents('tr').find('.group-check').addClass('hide');
			}else{
				$(this).parents('tr').find('.group-check').removeClass('hide');
			}
		});
	}

	$('[data-action="js_ajax"]').on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		var source = $(this).data('source');
		switch(source){
			case "supplier":
				supplierSaveRequest($this);
				break;
			case "add_product":
				addProductToShopRequest($this);
				break;
			case "add_selected_products":
				addSelectedProductsToShopRequest($this);
				break;
			case "ignore_product":
				ignoreProductRequest($this);
				break;
			case "ignore_selected_products":
				ignoreSelectedProductsRequest($this);
				break;
			case "change_nacenka":
				changeProductNacenkaRequest($this);
				break;
			case "change_all_manual":
				changeAllProductPriceManualAction($this);
				break;
			case "change_all_auto":
				changeAllProductPriceAutoAction($this);
				break;
			case "switchon_one":
				switchOnOneProductRequest($this);
				break;
			case "switchon_all_manual":
				switchOnAllProductsManualAction($this);
				break;
			case "switchon_all_auto":
				switchOnAllProductsAutoAction($this);
				break;
			case "switchoff_one":
				switchOffOneProductRequest($this);
				break;
			case "switchoff_all":
				switchOffAllProductsRequest($this);
				break;
			case "restore_one":
				restoreOneProductRequest($this);
				break;
			case "restore_all":
				restoreAllProductsRequest($this);
				break;
			case "change_price":
				changePriceRequest($this);
				break;
			case "monitor_change_color":
				monitorChangeColorRequest($this);
				break;
			case "group_selected":
				groupSelectedProductsRequest($this);
				break;
			case "ungroup_products":
				ungroupProductsRequest($this);
				break;
			case "get_ym_products":
				getYandexMarketProducts($this);
				break;
			case "update_ymf_prices":
				updateYandexMarketFeedPrices($this);
				break;
			case "update_images_alt":
				updateImagesAlts($this);
				break;
		}
	});

	$('[data-action="js_change"]').on('change keyup', function(e){
		e.preventDefault();
		var $this = $(this);
		var source = $(this).data('source');
		switch(source){
			case "change_price":
				changeProductPriceAction($this);
				break;
			case "change_price2":
				changeProductPrice2Action($this);
				break;
			case "change_minprice":
				changeProductMinPriceAction($this);
				break;
		}
	});

	var changeProductPriceAction = function($input){
		var $parent = $input.parents('tr');
		var products_id = $input.data('pid');
		var products_price = $input.val();
		var $new_price = $parent.find('.new_price');
		var $nacenka = $parent.find('.nacenka');
		var formattedNumberTmp = products_price - parseInt($new_price.text().replace(/[^\d.]/g, ''));
		var formattedNumber = formattedNumberTmp.toString().slice(0, -3) + ' ' + formattedNumberTmp.toString().slice(-3);
		$nacenka.text(formattedNumber);
		if(formattedNumberTmp > 0){
			$nacenka.css('color', 'inherit');
		}else{
			$nacenka.css('color', 'red');
		}
	};

	var changeProductPrice2Action = function($input){
		var $parent = $input.parents('tr');
		var products_id = $input.data('pid');
		var products_price = $input.val();
		var $new_price = $parent.find('.new_price');
		var $nacenka = $parent.find('.nacenka');
		var $coefficient = $parent.find('.coefficient');
		var $minprice = $parent.find('.minprice');
		var $raznica = $parent.find('.raznica');

		var formattedNumberTmp = products_price - parseInt($new_price.text().replace(/[^\d.]/g, ''));
		var formattedNumber = formattedNumberTmp.toString().slice(0, -3) + ' ' + formattedNumberTmp.toString().slice(-3);
		$nacenka.text(formattedNumber);

		var coefficient_val = Math.round((products_price / parseInt($new_price.text().replace(/[^\d.]/g, ''))) * 1000) / 1000;
		$coefficient.text(coefficient_val);

		if(parseInt($minprice.val()) > 0){
			var formattedNumberTmp = $minprice.val() - products_price;
			var formattedNumber = formattedNumberTmp.toString().slice(0, -3) + ' ' + formattedNumberTmp.toString().slice(-3);
			$raznica.text(formattedNumber).parent('td').css('color', 'inherit');
		}else{
			$raznica.parent('td').css('color', 'red');
		}

		if(parseInt($nacenka.text()) > 0){
			$nacenka.parent('td').css('color', 'inherit');
		}else{
			$nacenka.parent('td').css('color', 'red');
		}

	};

	var changeProductMinPriceAction = function($input){
		var $parent = $input.parents('tr');
		var products_id = $input.data('pid');
		var minprice = $input.val();
		var $product_price = $parent.find('.product_price');
		var $raznica = $parent.find('.raznica');


		if(parseInt(minprice) > 0){
			var formattedNumberTmp = minprice - $product_price.val();
			var formattedNumber = formattedNumberTmp.toString().slice(0, -3) + ' ' + formattedNumberTmp.toString().slice(-3);
			$raznica.text(formattedNumber);
		}else{
			$raznica.text('');
		}
		if(parseInt($raznica.text()) > 0){
			$raznica.css('color', 'inherit');
		} else {
			$raznica.css('color', 'red');
		}
	};

	var supplierSaveRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {};
		$parent.find('input').each(function(){
			form_data[$(this).attr('name')] = $(this).val();
		});
		console.log(form_data);

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.supplier_save_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$parent
					.find('input[name="id_supplier"]').val(responce.result)
					.end()
					.find('.id_supplier').text(responce.result);
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR");
			$btn.removeClass('loader');
		});
	};

	var addProductToShopRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid'),
			'new_price': $parent.find('input[name="product_price"]').val(),
			'clone_id': $parent.find('input[name="clone_product_id"]').val(),
			'group_id': $parent.find('input[name="group_product_id"]').is(':checked') ? 1 : 0,
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.product_add_to_shop_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$parent
					.find('.js_hide_after_action').addClass('invisible')
					.end()
					.find('.js_show_after_action').removeClass('invisible');
				$btn.next().attr('href', responce.result.edit_link);
			}else if(responce.error == 1){
				$parent.remove();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var addSelectedProductsToShopRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var form_data = {'products': []};
		if($parent.find('input.multiple_action:checked').length == 0){
			return false;
		}
		$parent.find('input.multiple_action:checked').each(function(){
			var id = $(this).val();
			var new_price = $(this).parents('tr').find('input[name="product_price"]').val();
			var clone_id = $(this).parents('tr').find('input[name="clone_product_id"]').val();
			var group_id = $(this).parents('tr').find('input[name="group_product_id"]').is(':checked') ? 1 : 0;
			form_data.products.push({
				'id': id,
				'new_price': new_price,
				'clone_id': clone_id,
				'group_id': group_id
			});
		});
		//console.log(form_data); return false;

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.products_add_to_shop_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var checkBoxes = $(".multiple_action");
				checkBoxes.prop("checked", false);
				$(".multiple_action_toggle").prop("checked", false);
				window.location.reload();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var ignoreProductRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid')
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.product_ignore_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$parent.remove();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var ignoreSelectedProductsRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var form_data = {'product_ids': []};
		if($parent.find('input.multiple_action:checked').length == 0){
			return false;
		}
		$parent.find('input.multiple_action:checked').each(function(){
			var id = $(this).val();
			form_data.product_ids.push(id);
		});
		//console.log(form_data); return false;

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.products_ignore_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var checkBoxes = $(".multiple_action");
				checkBoxes.prop("checked", false);
				$(".multiple_action_toggle").prop("checked", false);
				window.location.reload();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var changeProductNacenkaRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid'),
			'products_price': $parent.find('input[name="product_price"]').val(),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.change_product_nacenka_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$parent.fadeTo("slow", 0.2);
			}else if(responce.error == 1){
				$parent.remove();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var changeAllProductPriceManualAction = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('table');
		var row_prefix = $btn.data('rowprefix');
		var form_data = {
			'ids': $btn.data('ids')
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.change_product_nacenka_all_manual_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var ids = form_data.ids.split(',');
				$.each(ids, function(i, n){
					$('#'+row_prefix+n).fadeTo("slow", 0.2);
				});
			}else if(responce.error == 1){

			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var changeAllProductPriceAutoAction = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('table');
		var row_prefix = $btn.data('rowprefix');
		var form_data = {
			'ids': $btn.data('ids'),
			'prices': $btn.data('prices'),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.change_product_nacenka_all_auto_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var ids = form_data.ids.split(',');
				$.each(ids, function(i, n){
					$('#'+row_prefix+n).fadeTo("slow", 0.2);
				});
			}else if(responce.error == 1){

			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var switchOnOneProductRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid'),
			'products_price': $parent.find('input[name="product_price"]').val(),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.switchon_one_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$parent.fadeTo("slow", 0.2);
			}else if(responce.error == 1){
				$parent.remove();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var switchOnAllProductsManualAction = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('table');
		var row_prefix = $btn.data('rowprefix');
		var form_data = {
			'ids': $btn.data('ids')
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.switchon_all_manual_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var ids = form_data.ids.split(',');
				$.each(ids, function(i, n){
					$('#'+row_prefix+n).fadeTo("slow", 0.2);
				});
			}else if(responce.error == 1){

			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var switchOnAllProductsAutoAction = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('table');
		var row_prefix = $btn.data('rowprefix');
		var form_data = {
			'ids': $btn.data('ids'),
			'prices': $btn.data('prices'),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.switchon_all_auto_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var ids = form_data.ids.split(',');
				$.each(ids, function(i, n){
					$('#'+row_prefix+n).fadeTo("slow", 0.2);
				});
			}else if(responce.error == 1){

			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var switchOffOneProductRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid'),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.switchoff_one_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$parent.fadeTo("slow", 0.2);
			}else if(responce.error == 1){
				$parent.remove();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var switchOffAllProductsRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'id_supplier': $('#form_ctrl').find('select[name="id_supplier"]').val()
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.switchoff_all_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				window.location.reload();
			}else if(responce.error == 1){

			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var restoreOneProductRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid'),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.restore_one_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$parent.fadeTo("slow", 0.2);
			}else if(responce.error == 1){
				$parent.remove();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var restoreAllProductsRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var form_data = {'product_ids': []};
		if($parent.find('input.multiple_action:checked').length == 0){
			return false;
		}
		$parent.find('input.multiple_action:checked').each(function(){
			var id = $(this).val();
			form_data.product_ids.push(id);
		});
		//console.log(form_data); return false;

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.restore_all_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var checkBoxes = $(".multiple_action");
				checkBoxes.prop("checked", false);
				$(".multiple_action_toggle").prop("checked", false);
				window.location.reload();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var changePriceRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid'),
			'product_price': $parent.find('input[name="product_price"]').val(),
			'minprice': $parent.find('input[name="minprice"]').val(),
			'firm': $parent.find('input[name="firm"]').val()
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.change_price_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var results = responce.result.split(':');
				$parent.find('.nacenka').html(results[0] );
				$parent.find('.coefficient').html(results[1]);
				$parent.find('.date').html(results[2]);
				if(results[3] != 0){
					$parent.find('.raznica').html(results[3]);
				}else{
					$parent.find('.raznica').html('');
				}
				$parent.find('input[name="product_price"]').css('color', 'blue');
				$parent.find('input[name="minprice"]').css('color', 'blue');
				$parent.find('input[name="firm"]').css('color', 'blue');
				$parent.css('color', 'blue').fadeTo('slow', 0.5);
			}else if(responce.error == 1){

			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var monitorChangeColorRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $btn.parents('tr');
		var form_data = {
			'pid': $btn.data('pid'),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.monitor_change_color_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				if(responce.result == 1){
					$btn.css('backgroundColor', 'yellow');
				}else{
					$btn.css('backgroundColor', 'inherit');
				}
			}else if(responce.error == 1){

			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var groupSelectedProductsRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var form_data = {'product_ids': [], "groups": []};
		$parent.find('input.multiple_action:checked').each(function(){
			var id = $(this).val();
			var group_id = $(this).data('group-id');
			form_data.product_ids.push(id);
			form_data.groups.push(group_id);
		});
		//console.log(form_data); return false;
		if(form_data.product_ids.length <= 1){
			return false;
		}

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.group_products_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				var checkBoxes = $(".multiple_action");
				checkBoxes.prop("checked", false);
				$(".multiple_action_toggle").prop("checked", false);
				window.location.reload();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var ungroupProductsRequest = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var form_data = {
			'gid': $btn.data('gid'),
		};

		$btn.addClass('loader');
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.ungroup_products_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				window.location.reload();
			}
		}).fail(function(){
			$result_tag.text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var getYandexMarketProducts = function($btn){
		var $result_tag = $btn.parents('.form-table').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var $ymModal = $($btn.data('target'));
		var form_data = {
			'pid': $btn.data('pid'),
		};

		$parent.parents('table').find('tr').removeClass('selected');
		$parent.addClass('selected');
		$btn.addClass('loader');
		//$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.get_ym_products_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			//$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$ymModal.find('.modal-body').html(responce.result);
				//$ymModal.find('#ym_site').html(responce.result);
				//$ymModal.find('#ym_site').attr('src', responce.result);
			}
		}).fail(function(){
			$result_tag.fadeIn(100).text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$btn.removeClass('loader');
		});
	};

	var updateYandexMarketFeedPrices = function($btn){
		var $result_tag = $btn.parents('.inside').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var $target = $($btn.data('target'));
		var form_data = {};

		$btn.addClass('loader');
		$target.text('').fadeIn(100);
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.update_ymf_prices_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$target.html(responce.result).delay(20000).fadeOut(500);
			}
		}).fail(function(){
			$result_tag.fadeIn(100).text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$target.text("SYSTEM TECHNICAL ERROR");
			$btn.removeClass('loader');
		});
	};

	var updateImagesAlts = function($btn){
		var $result_tag = $btn.parents('.inside').find('.js_ajax_message');
		var $parent = $($btn.data('parent'));
		var $target = $($btn.data('target'));
		var form_data = {};

		$btn.addClass('loader');
		$target.text('').fadeIn(100);
		$result_tag.text(globals.lang.sending_request).fadeIn(100);

		$.ajax({
			type: "POST",
			url: globals.ajax_url,
			data: {'action': globals.update_images_alt_action, 'nonce': globals.nonce, 'form_data': form_data},
			dataType: "json"
		}).done(function(responce){
			$result_tag.text(responce.message).delay(2000).fadeOut(500);
			$btn.removeClass('loader');
			if(responce.error == 0){
				$target.html(responce.result).delay(20000).fadeOut(500);
			}
		}).fail(function(){
			$result_tag.fadeIn(100).text("SYSTEM TECHNICAL ERROR").delay(5000).fadeOut(500);
			$target.text("SYSTEM TECHNICAL ERROR");
			$btn.removeClass('loader');
		});
	};

});

