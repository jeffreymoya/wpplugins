(function($){

	$('#update_details_btn').data('action', 
	{
		link: 'update_profile'
	});

	$('#update_pwd_btn').data('action', 
	{
		link: 'update_pwd',
		handler: function() {
			$('#current_pwd, #new_pwd, #confirm_new_pwd').val('');
		}
	});

	$('#update_settings_btn').data('action',
	{
		link: 'update_settings'
	});

	$('#update_cc_btn').data('action', {
		link: 'update_cc',
		handler: function(resp) {
			$('#p_cc_type').text(resp.data.card_type);
			$('#p_cc_no').text(resp.data.card_number);
			$('#p_exp_date').text(resp.data.exp_date);
			$('#cc-update input:checked').removeAttr('checked');
			$('#cc-update #card_number, #cc-update #card_name, #cc-update #expiry_month, #cc-update #expiry_year, #cc-update #ccv').val('');
		}
	});

	$(document).ready(function() {

		$(document).foundation();

		var flash = function(type, message) 
		{
			var $ = jQuery, className = type;
			$('.mp_flash')
				.stop(true)
				.hide()
				.addClass(className)
				.html(message)
				.slideDown(300)
				.delay(3000)
				.slideUp(300, function() {
					$('.mp_flash').removeClass(className).html('');
				});
		},
		responseHandler = function(data, fn)
		{
			if(data.success) {
				flash('success', data.msg);
				if(fn) fn(data);
			}
			else
			{
				flash('alert', data.error);
			}
		}

		$('#mp_profile input[type=button]').on('click', function(e){
			e.preventDefault();
			var form = $(this).parents('form:first'), data = $(this).data('action');

			this.origText = $(this).val();
			$(this).prop('disabled', true);
			$(this).val('Please wait..');

			$.ajax({
				url: '/amac/members/' + data.link,
				dataType: 'json',
				type: 'POST',
				data: form.serialize(),
				success: function(resp) {
					$(this).prop('disabled', false);
					$(this).val(this.origText);
					responseHandler(resp, data.handler);
				}.bind(this),
				error: function(xhr, status, err) { 
					flash('alert', err); 
				}
			});
		});

		$('#auto_renew').on('change', function(){
			$.ajax({
				url: '/amac/members/update_auto_renew',
				dataType: 'json',
				type: 'POST',
				data: { 'auto_renew': (this.checked ? 1 : 0) },
				success: function(resp) {
					responseHandler(resp);
				}.bind(this),
				error: function(xhr, status, err) { 
					flash('alert', err); 
				}
			});
		});
	});
})(jQuery);
