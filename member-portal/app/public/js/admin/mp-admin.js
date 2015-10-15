(function($){
	$(document).ready(function()
	{
		var ids = {
			source: '#Membership_MembershipAddon_select',
			target: '#m_assigned_addons',
			deletes: '#m_assigned_addons a'
		},

		MemberAddon = 
		{
			init: function(srcId, targetId, linkIds)
			{
				this.addons   = $(srcId);
				this.links = $(linkIds);
				this.assigned = $(targetId);
				this.renderAvailable();
				this.attachHandlerToAvailable();
				if(this.assigned.children().length === 0) this.assigned.hide();
			},

			renderAvailable: function()
			{
				var opts = this.addons.children();
				var assigned = this.links.map(function(){ return this.id }).toArray();

				this.addons.html(
					Array.prototype.filter.call(opts, function(e) {
						return this.indexOf(e.value)  === -1;
					}.bind(assigned))
				);
			},

			addAssigned: function(s)
			{
				var opt = $(s.selectedOptions[0]);
				var a = $('<a>').attr({'id': opt.val(), 'href': '#'})
							.append($('<img>').attr('src',$('#del_img_url').val()));

				$('<p>').append(a)
						.append($('<span>').text(opt.text()))
						.append($('<input>').attr({'type':'hidden', 'name':'addons[]','value':opt.val()}))
						.appendTo(this.assigned);

				opt.remove();
				this.attachHandlerToAssigned(a);

				if(this.assigned.is(':hidden'))
				{
					this.assigned.show();
				}
			},

			removeAssigned: function(a)
			{
				this.addons.append($('<option>').attr('value',a.id).text($(a).next().text()));

				$(a).parent().remove();
				if(this.assigned.children().length === 0)
				{
					this.assigned.hide();
				}
			},

			attachHandlerToAvailable: function()
			{
				$(ids.source).on('change', function(e){
					e.preventDefault();
					this.addAssigned(e.target);
				}.bind(this));
			},

			attachHandlerToAssigned: function(a)
			{
				target = a || ids.deletes;
				$(target).on('click', function(e){
					e.preventDefault();
					this.removeAssigned(e.target.parentElement);
				}.bind(this));
			}
		}

		MemberAddon.init(ids.source, ids.target, ids.deletes);
		MemberAddon.attachHandlerToAssigned();
	});
})(jQuery);