// Get the global vars

var siteUrl = siteUrl || '',
	_people = _people || '',
	_client = _client || '',
	_board = _board || '',
	_limit = _limit || '';


// Resize throttling

$.resize.throttleWindow = false;
$.resize.delay = 1;


// Function str_replace

function str_replace(subject, search, replace, count) {
	var i = 0,
		j = 0,
		temp = '',
		repl = '',
		sl = 0,
		fl = 0,
		f = [].concat(search),
		r = [].concat(replace),
		s = subject,
		ra = Object.prototype.toString.call(r) === '[object Array]',
		sa = Object.prototype.toString.call(s) === '[object Array]';
	s = [].concat(s);
	if (count) {
		this.window[count] = 0;
	}
	for (i = 0, sl = s.length; i < sl; i++) {
		if (s[i] === '') {
			continue;
		}
		for (j = 0, fl = f.length; j < fl; j++) {
			temp = s[i] + '';
			repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
			s[i] = (temp).split(f[j]).join(repl);
			if (count && s[i] !== temp) {
				this.window[count] += (temp.length - s[i].length) / f[j].length;
			}
		}
	}
	return sa ? s : s[0];
}


$(document).ready(function() {
	//*-----BOARD JQUERY-----*//



	/*-----GLOBAL VIEW FUNCTIONS-----*/

	function refreshView() {
		preloadIn();
	}

	function resizeView() {
		var nav = $('#Nav'),
			list = $('#List'),
			footer = $('#Footer'),
			column = $('.column'),
			pad = 20,
			margin = 22;

		list.height('auto');
		column.height('100%');

		var lHeight = list.height(),
			wHeight = $(window).height(),
			hHeight = $('#Header').outerHeight(),
			fHeight = $('#Footer').outerHeight(),
			pHeight = hHeight + pad,
			tHeight = wHeight - (hHeight + fHeight + pad + 2 * margin),
			dWidth = document.documentElement.clientWidth,
			lWidth = list.width(),
			size = column.size();

		list.css({
			paddingTop: pHeight
		});
		nav.css({
			top: pHeight
		});

		if (lHeight <= tHeight) {
			list.height(tHeight);
			footer.css('position', 'fixed');
		} else {
			footer.css('position', 'static');
		}

		if (dWidth >= 960) {
			column.height($('#List').height() - margin);
			column.width(Math.floor(lWidth / size)-2 * margin);
			column.css("margin","0 "+margin/2+"px "+margin+"px");
		} else {
			column.height('auto');
		}
	}

	$(window).resize(function() {
		resizeView();
	}).trigger('resize');

	$('#Header').resize(function() {
		resizeView();
	});


	/*-----TITLE FUNCTIONS-----*/

	function titleRefresh() {
		var title = $('#Header h1').html();
		$('title').html('Project Flow | ' + title);
	}


	/*-----PRELOADER FUNCTIONS-----*/

	function preloadIn() {
		$('#Preloader, #Loading').fadeIn('slow', function() {
			window.location.reload();
		});
	}

	function preloadOut() {
		$('#Preloader, #Loading').delay(300).fadeOut('slow');
	}


	/*-----MESSAGE FUNCTIONS-----*/

	function showMessage(message, update, type) {
		hideLoader(); // Done

		if (type == 'error') {
			$('.message').addClass('error');
		}

		if (type == 'success') {
			$('.message').addClass('success');
		}

		$('.message').html(message);
		$('.message').fadeIn('fast');

		setTimeout(function() {
			$('.message').fadeOut('fast', function() {
				$('.message')
					.removeClass('error')
					.removeClass('success');
			});
		}, 1000);

		if (update === true) {
			//refreshView();
		}
	}
	
	var showLoaderCount = 0;

	function showLoader() {
		//$('.working').fadeIn('fast');
		$('.limit_any_move').fadeIn('fast');
		showLoaderCount++;
	}
	function hideLoader() {
		
		showLoaderCount--;
		
		if(showLoaderCount==0)
		$('.limit_any_move').fadeOut('fast');
	}


	/*-----NAV FUNCTIONS-----*/

	function navRefresh() {
		$('#Nav li').remove();
		$('.column').each(function() {
			var id = $(this).find('.head').attr('id'),
				title = $(this).find('.head h2').html(),
				color = $(this).attr('class').split(' '),
				size = $(this).find('.project').size(),
				label = (size == 1) ? 'Item' : 'Items';

			$('#Nav ul')
				.append('<li id="' + id + '" class="tab ' + color[1] +
					'"><div class="icon"></div><header><h2>' + title + '</h2><p>' + size +
					' ' + label + '</p></header></li>');
		});
	}

	function navScroll(id) {
		var offset = $('#status_id_' + id).offset();
		$('html, body').animate({
			scrollTop: offset.top - 60
		}, 'slow');
	}


	/*-----MENU FUNCTIONS-----*/

	function openMenu(menu, method, update, edit) {
		showLoader(); // Loading

		$.post(method, update, function(theResponse) {
			if (theResponse && theResponse != 'FALSE') {
				hideLoader(); // Done

				var instance = menu;
				instance.find('.ajax').html(theResponse);
				instance
					.modal('show')
					.find('.save').focus();
				formRefresh(edit);
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	function openDropdown() {
		showLoader(); // Loading

		$('#Header .multi').html('');

		$.post(siteUrl + 'board/get_menu_recent', function(theResponse) {
			if (theResponse && theResponse != 'FALSE') {
				hideLoader(); // Done

				$('#Header .multi').html(theResponse);
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	function closeDropdown() {
		$('#Header .multi').hide();
		$('.nav_boards').closest('li').removeClass('active');

		$('#Header .sub').hide();
		$('.nav_menu').closest('li').removeClass('active');
	}

	function closeMenu() {
		$('#modal').modal('hide');
	}


	/*-----FORM FUNCTIONS-----*/

	function formRefresh(edit) {
		// Add datepicker

		$('.date input').datepicker().on('changeDate', function() {
			$(this).datepicker('hide');
		});

		// Sort select options

		$('select[name="client"] option').tsort();
		$('select[name="assignments[]"] option').tsort();

		// Add mixitup to modals

		if (!edit) {
			if ($('#modal').size()) {
				if ($('#modal').mixitup('mixLoaded')) {
					if ($('.tabbed').size()) {
						$('#modal')
							.mixitup('remix')
							.mixitup('sort', ['data-modified', 'asc']);
					} else {
						$('#modal')
							.mixitup('remix')
							.mixitup('sort', ['data-name', 'desc']);
					}
				} else {
					$('#modal').mixitup({
						effects: ['fade'],
						transitionSpeed: 200,
						pagersWrapper: '#pagers',
						generatePagers: true,
						liveControls: true,
						limit: 12,
						onMixEnd: function() {
							mixHold = false;
						},
						onMixLoad: function() {
							mixHold = false;
						}
					});
				}
			}
		}

		if (!$('.list_item .item').size()) {
			mixHold = false;
		}

		// Add Chosen to selects

		$('select').not('.js-multi_picker').chosen({
			create_option: function(name) {
				showLoader(); // Loading

				var chosen = this;
				update = {
					name: name
				}; // POST
				$.post(siteUrl + 'board/new_client', update, function(theResponse) {
					if (theResponse && theResponse != 'FALSE') {
						hideLoader(); // Done

						showMessage('Successfully Added!');
						chosen.append_option({
							value: theResponse,
							text: name
						});
						formRefresh();
					} else {
						showMessage('Application Error!', true, 'error');
					}
				});
			},
			persistent_create_option: false,
			allow_single_deselect: true
		}).trigger('liszt:updated');

		$('.js-multi_picker').chosen({
			create_option: function(name) {
				showLoader(); // Loading

				var chosen = this;
				update = {
					name: name
				}; // POST
				$.post(siteUrl + 'board/new_person', update, function(theResponse) {
					if (theResponse && theResponse != 'FALSE') {
						hideLoader(); // Done

						showMessage('Successfully Added!');
						chosen.append_option({
							value: theResponse,
							text: name
						});
						formRefresh();
					} else {
						showMessage('Application Error!', true, 'error');
					}
				});
			},
			persistent_create_option: false
		}).trigger('liszt:updated');

		// Add tooltips to modal links

		$('.modal a').tooltip({
			placement: 'bottom'
		});

		// Remove meta from the last board

		if ($('.list_boards .item').not('inactive').size() <= 1) {
			$('.list_boards .item .meta').remove();
		}

		// Set modal titles

		var mSize = $('#modal .item').not('.inactive').size(),
			mTitle = $('#modal .menu').attr('rel'),
			mHead;

		if ($('#modal').find('.list_boards').size()) {
			mHead = mTitle + ' (<span class="count">' + mSize + '/' + _limit +
				'</span>)';
		} else {
			mHead = mTitle + ' (<span class="count">' + mSize + '</span>)';
		}

		$('#modal .modal-header h3').html(mHead);

		// Update inactive boards

		if ($('.list_boards .item').not('.inactive').size() < _limit) {
			var instance = $('.list_boards .inactive').eq(0),
				title = instance.find('h3').html(),
				id = instance.attr('rel');

			instance
				.removeClass('inactive')
				.find('h3').html('<a href="' + id +
					'" class="js-switch_board" data-dismiss="modal">' + title + '</a>');
		}

		$('.accordion').on('show', function(event) {
			$(this).find('.accordion-toggle').not($(event.target)).addClass(
				'collapsed');
			$(event.target).prev('.accordion-heading').find('.accordion-toggle').removeClass(
				'collapsed');
		});

		$('.slide')
			.bootstrapSwitch()
			.on('switch-change', function() {
				blockStyle();
			});

		blockStyle();
	}

	function formClear(form) {
		form.find(':input').not(':button, :submit, :reset, :hidden :disabled').val(
			'');
		form.find('select').val('').trigger('liszt:updated');
		form.find('.js-multi_picker').val('').trigger('liszt:updated');
	}

	if (navigator.platform != 'iPhone' && navigator.platform != 'iPod' &&
		navigator.platform != 'iPad') {
		$('a').tooltip({
			placement: 'bottom'
		});
	}


	/*-----BLOCK STYLE FUNCTIONS-----*/

	function blockStyle() {
		if ($('.block').size()) {
			var client = $('.block input[name="client"]').val(),
				project = $('.block input[name="project"]').val(),
				people = $('.block input[name="people"]').val(),
				notes = $('.block input[name="notes"]').val(),
				duedate = $('.block input[name="duedate"]').val();

			$('.example h2').html(project);
			$('.example h3').html(client);
			$('.example p:eq(0)').html('<span>' + people + ' Option 1</span><span>' +
				people + ' Option 2</span>');
			$('.example p:eq(1)').html(notes);
			$('.example p:eq(2)').html(duedate);

			if ($('#client_lvl').is(':checked') && $('#client_dis').is(':checked')) {
				$('.example h3').show();
			} else {
				$('.example h3').hide();
			}

			if (!$('#client_lvl').is(':checked')) {
				$('#client_lvl').closest('label').next('.level').hide();
			} else {
				$('#client_lvl').closest('label').next('.level').show();
			}

			if ($('#people_lvl').is(':checked') && $('#people_dis').is(':checked')) {
				$('.example p:eq(0)').removeClass('hide');
			} else {
				$('.example p:eq(0)').addClass('hide');
			}

			if (!$('#people_lvl').is(':checked')) {
				$('#people_lvl').closest('label').next('.level').hide();
			} else {
				$('#people_lvl').closest('label').next('.level').show();
			}

			if ($('#notes_lvl').is(':checked') && $('#notes_dis').is(':checked')) {
				$('.example p:eq(1)').removeClass('hide');
			} else {
				$('.example p:eq(1)').addClass('hide');
			}

			if (!$('#notes_lvl').is(':checked')) {
				$('#notes_lvl').closest('label').next('.level').hide();
			} else {
				$('#notes_lvl').closest('label').next('.level').show();
			}

			if ($('#duedate_lvl').is(':checked') && $('#duedate_dis').is(':checked')) {
				$('.example p:eq(2)').removeClass('hide');
			} else {
				$('.example p:eq(2)').addClass('hide');
			}

			if (!$('#duedate_lvl').is(':checked')) {
				$('#duedate_lvl').closest('label').next('.level').hide();
			} else {
				$('#duedate_lvl').closest('label').next('.level').show();
			}
		}
	}

	$('.block input').live('click', function() {
		blockStyle();
	});


	/*-----CONFRIMATION FUNCTIONS-----*/

	function openConfirm(message, title, target, fn) {
		$('#confirm .modal-header').html('<h3>' + title + '</h3>');
		$('#confirm .modal-body').html('<p>' + message + '</p>');

		$('.js-confirm').click(function() {
			fn(target);
		});

		$('#confirm').modal('show');
	}


	/*-----NAV ACTIONS------*/

	$('.tab').live('click', function() {
		var id = $(this).attr('id');
		navScroll(id);
	});


	/*-----ENTER BUTTON ACTIONS-----*/

	$(document).keydown(function(event) {
		if (!$(document.activeElement).closest('.chzn-container').size() && !$(
			document.activeElement).closest('.ft-search').size() && !$('#confirm').is(
			':visible') && !$('textarea').is(':focus')) {
			if (event.keyCode == 13) {
				event.preventDefault();
				if ($('#Header input').is(':focus')) {
					$('#Header input').blur();
				} else if ($('.share input').is(':focus')) {
					$('.js-new_share').click();
				} else {
					$(document.activeElement).closest('form').find('.save').click();
				}
			}
		}
	});


	/*-----MENU ACTIONS-----*/

	$('.nav_filter').click(function() {
		var closest = $(this).closest('li');

		closeDropdown();

		$('#Filter').slideToggle();
		closest.toggleClass('active');
	});

	$('.nav_menu').click(function() {
		var closest = $(this).closest('li');

		$('#Header .sub').toggle();
		$('#Header .multi').hide();
		$('#Filter').slideUp();
		$('#Header .nav li').not(closest).removeClass('active');
		closest.toggleClass('active');
	});

	$('.nav_boards, .count').click(function() {
		var closest = $(this).closest('li');

		if (!closest.hasClass('active')) {
			openDropdown();
		}

		$('#Header .multi').toggle();
		$('#Header .sub').hide();
		$('#Filter').slideUp();
		$('#Header .nav li').not(closest).removeClass('active');
		closest.toggleClass('active');
	});


	/*-----POPUP ACTIONS-----*/

	$('.nav_add').live('click', function(event) {
		openMenu($('#popup'), siteUrl + 'board/get_menu_add', true);
		$('#popup .modal-header h3').html('Add');
	});

	$('.nav_settings').live('click', function(event) {
		openMenu($('#popup'), siteUrl + 'board/get_menu_settings', true);
		$('#popup .modal-header h3').html('Settings');
	});


	/*-----MODAL ACTIONS-----*/

	var mixHold = false;

	$('.nav_success').live('click', function() {
		openMenu($('#modal'), siteUrl + 'board/get_menu_success', true);
		closeDropdown();
	});

	$('.nav_trash').live('click', function() {
		openMenu($('#modal'), siteUrl + 'board/get_menu_trash', true);
		closeDropdown();
	});

	$('.view_clients').live('click', function() {
		openMenu($('#modal'), siteUrl + 'board/get_menu_clients', true);
	});

	$('.view_people').live('click', function() {
		openMenu($('#modal'), siteUrl + 'board/get_menu_people', true);
	});

	$('.view_boards').live('click', function() {
		if (mixHold === false) {
			openMenu($('#modal'), siteUrl + 'board/get_menu_boards', true);
			closeDropdown();
			mixHold = true;
		}
	});

	$('.view_shared').live('click', function() {
		if (mixHold === false) {
			openMenu($('#modal'), siteUrl + 'board/get_menu_shared', true);
			mixHold = true;
		}
	});

	$('.view_archive').live('click', function() {
		if (mixHold === false) {
			openMenu($('#modal'), siteUrl + 'board/get_menu_archive', true);
			mixHold = true;
		}
	});


	/*-----COLUMN DIALOG ACTIONS-----*/

	$('.head a.edit').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#edit'), siteUrl + 'board/get_status_edit', update);
		closeDropdown();
	});

	$('.head').live('dblclick', function(event) {
		event.preventDefault();
		var id = $(this).attr('id');
		update = {
			id: id
		}; // POST
		openMenu($('#edit'), siteUrl + 'board/get_status_edit', update);
		closeDropdown();
	});

	$('.head a.add').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		};
		openMenu($('#add'), siteUrl + 'board/get_status_add', update);
		closeDropdown();
	});


	/*-----PROJECT DIALOG ACTIONS-----*/

	

	$('.project a.move').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#move'), siteUrl + 'board/get_project_move', update);
		closeDropdown();
	});


	/*-----ITEM DIALOG ACTIONS-----*/

	$('.clients .item a.edit').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#item'), siteUrl + 'board/get_client_edit', update, true);
	});

	$('.people .item a.edit').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#item'), siteUrl + 'board/get_person_edit', update, true);
	});

	$('.success .item a.move').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#move'), siteUrl + 'board/get_success_move', update, true);
	});

	$('.trash .item a.move').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#move'), siteUrl + 'board/get_trash_move', update, true);
	});


	/*-----BOARD DIALOG ACTIONS-----*/

	$('.boards .item a.move').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#move'), siteUrl + 'board/get_board_move', update, true);
	});

	$('.archived .item a.move').live('click', function(event) {
		event.preventDefault();
		var id = $(this).attr('href');
		update = {
			id: id
		}; // POST
		openMenu($('#move'), siteUrl + 'board/get_archive_move', update, true);
	});


	//*-----AJAX JQUERY-----*//

	/*-----CRITICAL VARIABLES-----*/

	var method,
		update,
		data = {
			width: 0,
			height: 0,
			click: {
				left: 0,
				top: 0
			}
		};


	/*-----DROP FUNCTIONS-----*/

	$('.nav_trash, .nav_success').droppable({
		hoverClass: 'hover',
		tolerance: 'pointer',
		over: function(event, ui) {
			var info = ui.draggable.closest('.list_project').data('uiSortable'),
				changer = $(this).hasClass('nav_trash') ? 'over_trash' :
				'over_success';

			if (info.helperProportions.width != 32) {
				data.width = info.helperProportions.width;
				data.height = info.helperProportions.height;
				data.click.left = info.offset.click.left;
				data.click.top = info.offset.click.top;
			}

			ui.draggable.addClass(changer).css({
				left: ui.draggable.position().left + (data.click.left - 24),
				top: ui.draggable.position().top + (data.click.top - 16)
			});

			info.helperProportions.width = 32;
			info.helperProportions.height = 32;
			info.offset.click.left = 24;
			info.offset.click.top = 16;
		},
		out: function(event, ui) {
			var info = ui.draggable.closest('.list_project').data('uiSortable'),
				changer = $(this).hasClass('nav_trash') ? 'over_trash' :
				'over_success';

			ui.draggable.removeClass(changer).css({
				left: ui.draggable.position().left - (data.click.left - 24),
				top: ui.draggable.position().top - (data.click.top - 16)
			});

			info.helperProportions.width = data.width;
			info.helperProportions.height = data.height;
			info.offset.click.left = data.click.left;
			info.offset.click.top = data.click.top;
		},
		drop: function(event, ui) {
			showLoader(); // Loading

			var changer = $(this).hasClass('nav_trash') ? 'trash' : 'success',
				id = ui.draggable.find('.edit').attr('href'),
				lives = changer;
			ui.draggable.remove();

			update = {
				id: id,
				lives: lives
			}; // POST
			$.post(siteUrl + 'board/update_home', update, function(theResponse) {
				if (theResponse == 'TRUE') {
					showMessage('Successfully Moved!', false, changer); // RESPONSE

					ft.refresh();
					navRefresh();
					formRefresh();
					resizeView();
				} else {
					showMessage('Application Error!', true, 'error');
				}
			});
		}
	});


	/*-----SORTABLE FUNCTIONS-----*/

	function sortConfirm() {
	
		/*$.post(method, update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Moved!'); // RESPONSE

				navRefresh();
			} else {
				showMessage('Application Error!', true, 'error');
			}*/
			
		$.ajax({
			  type: 'POST',
			  url: method,
			  data: update,
			  success: function(theResponse) {
				if (theResponse == 'TRUE') {
					showMessage('Successfully Moved!'); // RESPONSE

					navRefresh();
				} else {
					showMessage('Application Error!', true, 'error');
				}
			},
			  async:true
			});	
			
			
		//});
	}

	function sortRefresh() {
		navRefresh();

		/*$('.list_column').sortable({
			items: '.column:not(:first-child)',
			opacity: 0.8,
			revert: 200,
			delay: 200,
			cursor: 'move',
			containment: '.list_column',
			handle: '.head',
			start: function(event, ui) {
				ui.item.height('auto');
			},
			update: function(event, ui) {
				//showLoader(); // Loading

				//method = siteUrl + 'board/update_column';
				//update = $(this).sortable('serialize'); // POST
				//sortConfirm();
			}
		});*/

		$('.list_project').sortable({
			items: '.project',
			opacity: 0.8,
			revert: 200,
			delay: 200,
			cursor: 'move',
			placeholder: 'placeholder',
			connectWith: '.list_project',
			handle: '.info',
			over: function(event, ui) {
				resizeView();
			},
			start: function(event, ui) {
				$('.nav_trash, .nav_success').addClass('drop');
			},
			stop: function() {
				$('.project').css('z-index', 'auto');
				$('.nav_trash, .nav_success').removeClass('drop');
				resizeView();
			},
			update: function(event, ui) {
				showLoader(); // Loading

				var status = $(this).attr('id');
				method = siteUrl + 'tasks/ajax/update_order';
				update = $(this).sortable('serialize') + '&status=' + status; // POST
				sortConfirm();
			}
		});
	}


	/*-----SWITCH BOARD ACTIONS-----*/

	$('.js-switch_board').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href');

		update = {
			id: id
		}; // POST
		$.post(siteUrl + 'board/switch_board', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Loading...', true); // RESPONSE
			} else {
				showMessage('Application Error!', true, 'error');
			}

			closeDropdown();
		});
	});


	/*-----NEW BOARD ACTIONS-----*/

	$('.js-new_board').live('click', function() {
		showLoader(); // Loading

		$.post(siteUrl + 'board/new_board', function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Loading...', true); // RESPONSE
			} else if (theResponse == 'LIMIT') {
				showMessage('Board Limit Reached!', false, 'error'); // RESPONSE
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UPDATE PUBLIC ACTIONS-----*/

	$('.js-update_public').live('click', function() {
		showLoader(); // Loading

		var pub = $(this).is(':checked') ? 1 : 0;

		update = {
			pub: pub
		}; // POST
		$.post(siteUrl + 'board/update_public', update, function(theResponse) {
			if (theResponse == 'TRUE' || theResponse == 'SWITCH') {
				showMessage('Successfully Updated!'); // RESPONSE
				if (pub) {
					$('input[name="public_url"]').attr('disabled', false);
				} else {
					$('input[name="public_url"]').attr('disabled', 'disabled');
				}
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----ARCHIVE BOARD ACTIONS-----*/

	$('.js-archive_board').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href');

		update = {
			id: id
		}; // POST
		$.post(siteUrl + 'board/archive_board', update, function(theResponse) {
			if (theResponse == 'TRUE' || theResponse == 'SWITCH') {
				if (theResponse == 'SWITCH') {
					showMessage('Successfully Moved!', true); // RESPONSE
				} else {
					showMessage('Successfully Moved!'); // RESPONSE
				}

				if (theResponse == 'SWITCH') {
					closeMenu();
				}

				mixHold = true;

				$('#board_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					formRefresh();
				});
			} else if (theResponse == 'LIMIT') {
				showMessage('Cannot Archive Board!', false, 'error');
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UNARCHIVE BOARD ACTIONS-----*/

	$('.js-unarchive_board').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href');

		update = {
			id: id
		}; // POST
		$.post(siteUrl + 'board/unarchive_board', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Moved!'); // RESPONSE

				mixHold = true;

				$('#board_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					formRefresh();
				});
			} else if (theResponse == 'LIMIT') {
				showMessage('Board Limit Reached!', false, 'error'); // RESPONSE
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UPDATE TITLE ACTIONS-----*/

	$('#Header h1').click(function() {
		if($('#Header form').is('form')) {
			$(this).hide();
			$('#Header form').show();
			$('#Header input').focus();
		}
	});

	$('#Header input').blur(function() {
		showLoader(); // Loading

		var id = $(this).closest('form').attr('id'),
			title = $(this).val();

		update = {
			title: title
		};
		$.post(siteUrl + 'board/update_title', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Updated!'); // RESPONSE
				if (!title) {
					title = 'Untitled';
				}

				$('#Header h1').show().html(title);
				$('#Header form').hide();
				$('#recent_id_' + id).html(title);

				titleRefresh();
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UPDATE LANG ACTIONS-----*/

	$('.js-settings_blocks_save').live('click', function() {
		showLoader(); // Loading

		var serialize = $(this).closest('form').serializeArray();

		update = serialize; // POST
		$.post(siteUrl + 'board/update_lang', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Updated!', true); // RESPONSE
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UPDATE HOME ACTIONS-----*/

	$('.js-project_move_success').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href'),
			lives = 'success';

		update = {
			id: id,
			lives: lives
		}; // POST
		$.post(siteUrl + 'board/update_home', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Moved!', false, 'success'); // RESPONSE

				$('#project_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					navRefresh();
					formRefresh();
					resizeView();
				});
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});

	$('.js-project_move_trash').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href'),
			lives = 'trash';

		update = {
			id: id,
			lives: lives
		}; // POST
		$.post(siteUrl + 'board/update_home', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Moved!'); // RESPONSE

				$('#project_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					navRefresh();
					formRefresh();
					resizeView();
				});
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});

	$('.js-project_move_board').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		if ($('.column').size()) {
			var id = $(this).attr('href'),
				lives = 'project';

			update = {
				id: id,
				lives: lives
			}; // POST
			$.post(siteUrl + 'board/update_home', update, function(theResponse) {
				if (theResponse && theResponse != 'FALSE') {
					showMessage('Successfully Moved!');
					var response = $.parseJSON(theResponse);

					$('#project_id_' + id).fadeOut('fast', function() {
						$(this).remove();
						if (!$('#status_id_' + response.status).size()) {
							response.status = $('.column').eq(0).find('.head').attr('id');
						}
						$(response.project).appendTo($('#status_id_' + response.status).children(
							'.list_project'));

						method = siteUrl + 'board/update_order';
						update = $('#status_id_' + response.status).find('.list_project')
							.sortable('serialize') + '&status=' + response.status; // POST
						sortConfirm();

						ft.refresh();
						navRefresh();
						formRefresh();
						resizeView();
					});
				} else {
					showMessage('Application Error!', true, 'error');
				}
			});
		} else {
			showMessage('Add A Column!', false, 'error');
		}
	});


	/*-----UPDATE STATUS ACTIONS-----*/

	$('.js-column_edit_save').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href'),
			title = $(this).closest('form').find('input[name="title"]').val(),
			serialize = $(this).closest('form').serializeArray();
		serialize.push({
			name: "id",
			value: id
		});

		update = serialize; // POST
		$.post(siteUrl + 'board/update_status', update, function(theResponse) {
			if (theResponse && theResponse != 'FALSE') {
				showMessage('Successfully Updated!');
				if (!title) {
					title = 'Untitled';
				}

				$('#status_id_' + id + ' .head h2').html(title);
				$('#status_id_' + id).closest('.column')
					.removeClass(
						'color1 color2 color3 color4 color5 color6 color7 color8 color9 color10 color11 color12'
					)
					.addClass(theResponse);

				ft.refresh();
				navRefresh();
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UPDATE PROJECT ACTIONS-----*/

	$('.js-project_edit_save').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href'),
			title = $(this).closest('form').find('input[name="title"]').val(),
			client = $(this).closest('form').find('select[name="client"]').val(),
			new_client = $(this).closest('form').find('input[name="new_client"]').val(),
			label = $(this).closest('form').find(
				'select[name="client"] option:selected').text(),
			serialize = $(this).closest('form').serializeArray();
		serialize.push({
			name: "id",
			value: id
		});

		update = serialize; // POST
		$.post(siteUrl + 'board/update_project', update, function(theResponse) {
			if (theResponse && theResponse != 'FALSE') {
				showMessage('Successfully Updated!');

				$('#project_id_' + id).after(theResponse).remove();

				ft.refresh();
				navRefresh();
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UPDATE CLIENT ACTIONS-----*/

	$('.js-client_edit_save').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).attr('href'),
			name = $(this).closest('form').find('input[name="name"]').val(),
			serialize = $(this).closest('form').serializeArray();
		serialize.push({
			name: "id",
			value: id
		});

		update = serialize; // POST
		$.post(siteUrl + 'board/update_client', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Updated!'); // RESPONSE
				if (!name) {
					name = 'Untitled';
				}

				var before = $('.client_id_' + id).html(),
					after = name,
					oldName = str_replace(before, ',', '\\,\\'),
					newName = str_replace(after, ',', '\\,\\');

				$('.client_id_' + id).each(function() {
					var article = $(this).closest('article'),
						meta = article.attr('data-' + _client);
					article.attr('data-' + _client, str_replace(meta, oldName, newName));
				});

				$('#client_id_' + id).attr('data-name', name).find('.info h3').html(
					name);
				$('.client_id_' + id).html(name);
				$('select[name="client"] option[value="' + id + '"]').html(name).attr(
					'data-name', name);

				ft.update(str_replace(_client, '-', ' '), before, after);
				ft.refresh();
				formRefresh();
			} else if (theResponse == 'EXISTS') {
				showMessage('Option Already Exists!', false, 'error'); // RESPONSE
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----UPDATE PERSON ACTIONS-----*/

	$('.js-person_edit_save').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		var id = $(this).closest('form').attr('id'),
			name = $(this).closest('form').find('input[name="name"]').val(),
			serialize = $(this).closest('form').serializeArray();
		serialize.push({
			name: "id",
			value: id
		});

		update = serialize; // POST
		$.post(siteUrl + 'board/update_person', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Updated!'); // RESPONSE
				if (!name) {
					name = 'Untitled';
				}

				var before = $('.person_id_' + id).html(),
					after = name,
					oldName = str_replace(before, ',', '\\,\\'),
					newName = str_replace(after, ',', '\\,\\');

				$('.person_id_' + id).each(function() {
					var article = $(this).closest('article');
					var meta = article.attr('data-' + _people);
					article.attr('data-' + _people, str_replace(meta, oldName, newName));
				});

				$('#person_id_' + id).attr('data-name', name).find('.info h3').html(
					name);
				$('.person_id_' + id).html(name);
				$('select[name="assignments[]"] option[value="' + id + '"]').html(
					name).attr('data-name', name);

				ft.update(str_replace(_people, '-', ' '), before, after);
				ft.refresh();
				formRefresh();
			} else if (theResponse == 'EXISTS') {
				showMessage('Option Already Exists!', false, 'error'); // RESPONSE
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----NEW SHARE ACTIONS-----*/

	$('.js-new_share').live('click', function() {
		showLoader(); // Loading

		var input = $(this).closest('form').find('input[name="email"]'),
			email = input.val();

		update = {
			email: email
		}; // POST
		$.post(siteUrl + '/board/new_share', update, function(theResponse) {
			if (theResponse == 'EXISTS') {
				showMessage('User Already Invited!', false, 'error'); // RESPONSE
			} else if (theResponse == 'BLANK') {
				showMessage('Please Enter Email!', false, 'error'); // RESPONSE
			} else if (theResponse && theResponse != 'FALSE') {
				showMessage('Successfully Added!'); // RESPONSE

				$('.share_list').append(theResponse);
				input.val('');
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----NEW STATUS ACTIONS-----*/

	$('.js-add_column_save').live('click', function() {
		showLoader(); // Loading

		var listorder = $('.column').size() + 1,
			serialize = $(this).closest('form').serializeArray(),
			form = $(this).closest('form');
		serialize.push({
			name: "listorder",
			value: listorder
		});

		update = serialize; // POST
		$.post(siteUrl + 'board/new_status', update, function(theResponse) {
			if (theResponse && theResponse != 'FALSE') {
				showMessage('Successfully Added!');

				$(theResponse).appendTo('.list_column');

				ft.reset();
				sortRefresh();
				formRefresh();
				formClear(form);
				resizeView();
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	});


	/*-----NEW PROJECT ACTIONS-----*/

	$('.js-add_project_save').live('click', function() {
		showLoader(); // Loading

		if ($('.column').size()) {
			var listorder = $('.column').eq(0).find('.project').size() + 1,
				status = $('.column').eq(0).find('.head').attr('id'),
				serialize = $(this).closest('form').serializeArray(),
				form = $(this).closest('form');
			serialize.push({
				name: "listorder",
				value: listorder
			});
			serialize.push({
				name: "status",
				value: status
			});

			update = serialize; // POST
			$.post(siteUrl + 'board/new_project', update, function(theResponse) {
				if (theResponse && theResponse != 'FALSE') {
					showMessage('Successfully Added!');

					$(theResponse).appendTo($('.column').eq(0).children('.list_project'));

					ft.refresh();
					navRefresh();
					formClear(form);
					resizeView();
				} else {
					showMessage('Application Error!', true, 'error');
				}
			});
		} else {
			showMessage('Add A Column!', false, 'error');
		}
	});


	/*-----NEW STATUS ACTIONS-----*/

	$('.js-column_add_save').live('click', function(event) {
		showLoader(); // Loading

		event.preventDefault();
		if ($('.column').size()) {
			var id = $(this).attr('href'),
				listorder = $('#status_id_' + id).find('.project').size() + 1,
				status = $(this).attr('href'),
				serialize = $(this).closest('form').serializeArray();
			serialize.push({
				name: "listorder",
				value: listorder
			});
			serialize.push({
				name: "status",
				value: status
			});

			update = serialize; // POST
			$.post(siteUrl + 'board/new_project', update, function(theResponse) {
				if (theResponse && theResponse != 'FALSE') {
					showMessage('Successfully Added!');

					$(theResponse).appendTo($('#status_id_' + id).children(
						'.list_project'));

					ft.refresh();
					navRefresh();
					formRefresh();
					resizeView();
				} else {
					showMessage('Application Error!', true, 'error');
				}
			});
		} else {
			showMessage('Add A Column!', false, 'error');
		}
	});


	/*-----DELETE BOARD ACTIONS-----*/

	function deleteBoard(target) {
		showLoader(); // Loading

		var id = target.attr('href');

		update = {
			id: id
		}; // POST
		$.post(siteUrl + 'board/delete_board', update, function(theResponse) {
			if (theResponse == 'TRUE' || theResponse == 'SWITCH') {
				if (theResponse == 'SWITCH') {
					showMessage('Successfully Deleted!', true); // RESPONSE
				} else {
					showMessage('Successfully Deleted!'); // RESPONSE
				}

				if (theResponse == 'SWITCH') {
					closeMenu();
				}

				mixHold = true;

				$('#board_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					formRefresh();
				});
			} else if (theResponse == 'LIMIT') {
				showMessage('Cannot Delete Board!', false, 'error');
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-delete_board').live('click', function(event) {
		event.preventDefault();
		var target = $(this);
		openConfirm(
			'Are you sure you want to delete this board? Deleting boards cannot be undone.',
			'Delete board?', target, deleteBoard);
	});


	/*-----DELETE SHARE ACTIONS-----*/

	function deleteShare(target) {
		showLoader(); // Loading

		var id = target.attr('href');

		update = {
			id: id
		}; // POST
		$.post(siteUrl + 'board/delete_share', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Removed!'); // RESPONSE

				$('#share_id_' + id).fadeOut('fast', function() {
					$(this).remove();
				});
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-delete_share').live('click', function(event) {
		event.preventDefault();
		var target = $(this),
			email = target.prev('.email').html();
		openConfirm('Are your sure you want to remove <strong>' + email +
			'</strong> from the shared user list?', 'Remove user?', target,
			deleteShare);
	});


	/*-----DELETE SHARED ACTIONS-----*/

	function deleteShared(target) {
		showLoader(); // Loading

		var id = target.attr('href');

		update = {
			id: id
		}; // POST
		$.post(siteUrl + 'board/delete_shared', update, function(theResponse) {
			if (theResponse == 'TRUE' || theResponse == 'SWITCH') {
				if (theResponse == 'SWITCH') {
					showMessage('Successfully Removed!', true); // RESPONSE
				} else {
					showMessage('Successfully Removed!'); // RESPONSE
				}

				if (theResponse == 'SWITCH') {
					closeMenu();
				}

				$('#board_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					formRefresh();
				});
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-delete_shared').live('click', function(event) {
		event.preventDefault();
		var target = $(this),
			email = target.prev('.email').html();
		openConfirm(
			'Are you sure you want to remove yourself from this shared board?',
			'Remove board?', target, deleteShared);
	});


	/*-----DELETE STATUS ACTIONS-----*/

	function deleteStatus(target) {
		showLoader(); // Loading

		var id = target.attr('href');

		update = {
			id: id
		}; // POST
		$.post(siteUrl + 'board/delete_status', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Deleted!'); // RESPONSE

				$('#status_id_' + id + ' .project').each(function() {
					$(this).fadeOut('fast', function() {
						$(this).remove();
					});
				});

				$('#status_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					ft.reset();
					sortRefresh();
					formRefresh();
					resizeView();
				});
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-column_delete').live('click', function(event) {
		event.preventDefault();
		var target = $(this);
		openConfirm(
			'Are you sure you want to delete this column? All items that belong to it will be moved to the trash.',
			'Delete column?', target, deleteStatus);
	});


	/*-----DELETE PROJECT ACTIONS-----*/

	function deleteProject(target) {
		showLoader(); // Loading

		var id = target.attr('href');

		update = {
			id: id
		};
		$.post(siteUrl + 'board/delete_project', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Deleted!'); // RESPONSE

				$('#project_id_' + id).fadeOut('fast', function() {
					$(this).remove();

					ft.refresh();
					formRefresh();
				});
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-project_delete').live('click', function(event) {
		event.preventDefault();
		var target = $(this);
		openConfirm('Are you sure you want to delete this item?', 'Delete item?',
			target, deleteProject);
	});


	/*-----DELETE CLIENT ACTIONS-----*/

	function deleteClient(target) {
		showLoader(); // Loading

		var id = target.attr('href');

		update = {
			id: id
		};
		$.post(siteUrl + 'board/delete_client', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Deleted!'); // RESPONSE

				$('#client_id_' + id).fadeOut('fast', function() {
					var before = $('.client_id_' + id).html(),
						oldName = str_replace($('.client_id_' + id).html(), ',', '\\,\\');

					$('.client_id_' + id).each(function() {
						var article = $(this).closest('article'),
							meta = article.attr('data-' + _client);

						article.attr('data-' + _client, str_replace(meta, ', ' + oldName,
							''));
						article.attr('data-' + _client, str_replace(meta, oldName, ''));
					});

					$(this).remove();
					$('.client_id_' + id).closest('.item').remove();

					ft.update(str_replace(_client, '-', ' '), before);
					formRefresh();
				});
				$('.client_id_' + id).closest('.project').fadeOut('fast', function() {
					$(this).remove();

					navRefresh();
					resizeView();
				});
				$('select[name="client"] option[value="' + id + '"]').remove();
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-client_delete').live('click', function(event) {
		event.preventDefault();
		var target = $(this);
		openConfirm(
			'Are you sure you want to delete this category? All items that belong to it will be deleted as well.',
			'Delete category?', target, deleteClient);
	});


	/*-----DELETE PERSON ACTIONS-----*/

	function deletePerson(target) {
		showLoader(); // Loading

		var id = target.attr('href');

		update = {
			id: id
		};
		$.post(siteUrl + 'board/delete_person', update, function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Successfully Deleted!'); // RESPONSE

				$('#person_id_' + id).fadeOut('fast', function() {
					var before = $('.person_id_' + id).html();

					var oldName = str_replace($('.person_id_' + id).html(), ',',
						'\\,\\');
					$('.person_id_' + id).each(function() {
						var article = $(this).closest('article'),
							meta = article.attr('data-' + _people);

						article.attr('data-' + _people, str_replace(meta, ', ' + oldName,
							''));
						article.attr('data-' + _people, str_replace(meta, oldName, ''));
					});

					$(this).remove();
					$('.person_id_' + id).each(function() {
						if (!$(this).siblings('span').size()) {
							$(this).closest('p').hide().siblings('header').addClass('clean');
						}
					}).remove();

					formRefresh();
					resizeView();
				});
				$('select[name="assignments[]"] option[value="' + id + '"]').remove();
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-person_delete').live('click', function(event) {
		event.preventDefault();
		var target = $(this);
		openConfirm('Are you sure you want to delete this option?',
			'Delete option?', target, deletePerson);
	});


	/*-----EMPTY TRASH ACTIONS-----*/

	function deleteTrash(target) {
		showLoader(); // Loading

		$.post(siteUrl + 'board/delete_trash', function(theResponse) {
			if (theResponse == 'TRUE') {
				showMessage('Trash Emptied!'); // RESPONSE

				$('#Trash').find('.item').fadeOut('fast', function() {
					$(this).remove();

					ft.refresh();
					formRefresh();
				});
			} else {
				showMessage('Application Error!', true, 'error');
			}
		});
	}

	$('.js-trash_empty').live('click', function() {
		var target = $(this);
		openConfirm(
			'Are you sure you want to empty the trash? All items will be deleted permenantly.',
			'Empty trash?', target, deleteTrash);
	});



	//*-----FILTER JQUERY-----*//

	/*-----FILTER ACTIONS-----*/

	var ft = $.filtrify('List', 'FilterList', 'FilterAll', {
		callback: function(query, match, mismatch) {
			if ($('.ft-list li').size()) {
				$('.ft-clear').show();
			} else {
				$('.ft-clear').hide();
			}
		}
	});

	$('.ft-clear').click(function() {
		ft.reset();
	});



	//*-----RUNTIME JQUERY-----*//

	/*-----LAUNCH SEQUENCE-----*/

	sortRefresh();
	preloadOut();
	resizeView();

});


$(document).on('blur', 'input, textarea', function() {
	//  Fix for fixed positioning header on iPad after
	//  adding new column/project
	setTimeout(function() {
		window.scrollTo(document.body.scrollLeft, document.body.scrollTop);
	}, 0);


	/*-----SORT PEOPLE ACTIONS-----*/

	$.fn.sortList = function() {
		var listItem = $(this);
		var listItems = $('li.search-choice', listItem).get();
		listItems.sort(function(a, b) {
			var compA = $(a).text().toUpperCase();
			var compB = $(b).text().toUpperCase();
			return (compA < compB) ? -1 : 1;
		});
		$.each(listItems, function(i, item) {
			listItem.append(item);
		});
		$(this).find('.search-field').appendTo(this);
	}

	$("ul.chzn-results li").on("click", function() {
		$("ul.chzn-choices").sortList();
	});
});