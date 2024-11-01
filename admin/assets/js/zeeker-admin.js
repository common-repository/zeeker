/** @format */

(function ($) {
	var Zeeker = {
		init: function () {
			this.inputValue = '';
			this.cacheDOM();
			this.eventListener();
			this.initSelect2($('.zeeker-select2'));
		},
		cacheDOM: function () {
			this.$zeekerOptionTabs = $('.zeeker-tabs .nav-tab');
			this.$displayOptionButton = $('button#zeeker-new-display-option');
			this.$toggleAll = $('#select-all');
		},
		eventListener: function () {
			this.$zeekerOptionTabs.on('click', this.zeekerOptionTabContents.bind(this));

			this.$displayOptionButton.on('click', this.addNewDispalyOptionSettings.bind(this));

			$('body').on('click', '.zeeker-accordion-wrapper > button', this.expandAccordion.bind(this));

			$('body').on('change', '.zeeker-widget-amp', this.ampChanged.bind(this));

			$('body').on('input change', '.zeeker-widget-tagname', this.tagNameChanged.bind(this));

			$('body').on('change', 'input.zeeker-type', this.checkBoxChanged.bind(this));

			$('body').on('click', '.zeeker-display-remove', this.displayRemove.bind(this));

			$('body').on('click', '[data-a-control]', this.customAccordion.bind(this));

			$('form#zeeker-configure-form checkbox').on('submit', this.updateConfigureOptions());

			$('body').on('focus', "#zeeker-configure-form input[type='text']", this.triggerFocusOptions.bind(this));

			$('body').on('blur', "#zeeker-configure-form input[type='text']", this.triggerUpdateConfigureOptions.bind(this));

			$('body').on(
				'change',
				"#zeeker-configure-form input[type='checkbox'], #zeeker-configure-form select",
				this.triggerUpdateConfigureOptions.bind(this)
			);

			$(this.$toggleAll).on('click', this.toggleDisplayOptions.bind(this));
		},
		initSelect2: function ($element) {
			$element.each(function () {
				var placeholder = $(this).data('placeholder');
				$(this).select2({
					ajax: {
						delay: 250,
						url: zeeker.ajax,
						data: function (params) {
							var $this = $(this),
								post_type = '',
								taxonomy = '',
								nonce = $('#zeeker_select2_nonce').val();

							if ('undefined' !== typeof $this.data('post_type')) {
								post_type = $this.data('post_type');
							}

							if ('undefined' !== typeof $this.data('taxonomy')) {
								taxonomy = $this.data('taxonomy');
							}

							var query = {
								action: 'select2_search',
								search: params.term,
								page: params.page || 1,
								post_type: post_type,
								tax: taxonomy,
								_zeeker_select2_nonce: nonce,
							};

							return query;
						},
						processResults: function (data, params) {
							params.page = params.page || 1;

							return {
								results: data.results,
								pagination: {
									more: params.page * 20 < data.count_filtered,
								},
							};
						},
					},
					placeholder: placeholder,
				});
			});
		},
		zeekerOptionTabContents: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				target = $this.data('zeeker_target');

			if ($this.hasClass('nav-tab-active') || !target) {
				return;
			}

			this.$zeekerOptionTabs.removeClass('nav-tab-active');
			$this.addClass('nav-tab-active');

			$('.zeeker-tab-contents .zeeker-tab-content.zeeker-active-content').removeClass('zeeker-active-content').hide();
			$('.zeeker-tab-contents .' + target)
				.addClass('zeeker-active-content')
				.show();

			var url = window.location.origin + window.location.pathname;

			if ('zeeker-widget-instruction' !== target) {
				url = url + '?page=zeeker-settings&tab=' + target;
			} else {
				url = url + '?page=zeeker-settings';
			}

			window.history.pushState({}, '', url);
		},
		expandAccordion: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				$panel = $this.next();

			$('button.zeeker-accordion.active').not($this).removeClass('active').next().css({
				'max-height': '',
				overflow: '',
			});

			if ($this.hasClass('active')) {
				$this.removeClass('active');
				$panel.css({
					'max-height': '',
					overflow: '',
				});
			} else {
				$this.addClass('active');
				$panel.css({
					'max-height': 'initial',
					overflow: 'visible',
				});
			}
		},
		ampChanged: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				isChecked = $this.is(':checked'),
				shortcodeid = $this.attr('id').replace('amp', 'shortcode'),
				$shortcode = $('#' + shortcodeid),
				structure = $shortcode.data('structure'),
				tagnamefieldid = $this.attr('id').replace('amp', 'tagname'),
				$tagname = $('#' + tagnamefieldid),
				$amp = $this.closest('.zeeker-accordion-wrapper').find('.zeeker-snackbar.zeeker-amp'),
				value = $tagname.val();

			value = value.replace(/\s+/g, '_').toLowerCase();

			var _structure = structure.replace('TAGNAME', 'tagname="' + value + '"');

			if (isChecked) {
				_structure = _structure.replace('AMP', 'amp="yes"');
				$amp.addClass('shown');
				$this.closest('table').find('tr.zeeker-widget-display-options').hide();
			} else {
				_structure = _structure.replace('AMP', '');
				$amp.removeClass('shown');
				$this.closest('table').find('tr.zeeker-widget-display-options').show();
			}

			$shortcode.val(_structure);
		},
		tagNameChanged: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				value = $this.val(),
				shortcodeid = $this.attr('id').replace('tagname', 'shortcode'),
				$shortcode = $('#' + shortcodeid),
				structure = $shortcode.data('structure'),
				ampid = $this.attr('id').replace('tagname', 'amp'),
				$amp = $('#' + ampid),
				isChecked = $amp.is(':checked');

			value = value.replace(/\s+/g, '_').toLowerCase();

			var _structure = structure.replace('TAGNAME', 'tagname="' + value + '"');

			if (isChecked) {
				_structure = _structure.replace('AMP', 'amp="yes"');
			} else {
				_structure = _structure.replace('AMP', '');
			}

			$shortcode.val(_structure);

			$this
				.closest('.zeeker-accordion-wrapper')
				.find('button.zeeker-accordion .zeeker-accordion-button-text')
				.html(value + ' <i>setting</i>');
		},
		addNewDispalyOptionSettings: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				key = $('.zeeker-accordion').length,
				template = $('#zeeker-display-widget-options-skeleton').html().toString().replace(/{key}/g, key.toString());

			$this.before(template);
			var $select2 = $this.prev().find('.zeeker-select2');
			this.initSelect2($select2);
		},
		checkBoxChanged: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				isChecked = $this.is(':checked');

			if (isChecked) {
				$this.closest('.zeeker-options').find('.zeeker-extra-options').removeClass('hidden');
			} else {
				$this.closest('.zeeker-options').find('.zeeker-extra-options').addClass('hidden');
			}
		},
		displayRemove: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				$form = $this.closest('form');

			if (!confirm(zeeker.removeAccordionContent)) {
				return false;
			}

			$this.closest('.zeeker-accordion-wrapper').remove();

			var b = $form.serialize();
			$.post('options.php', b)
				.error(function () {
					$('#zeeker-snackbar.zeeker-error').addClass('show');
					setTimeout(function () {
						$('#zeeker-snackbar.zeeker-error').removeClass('show');
					}, 3000);
				})
				.success(function () {
					$('#zeeker-snackbar.zeeker-success').addClass('show');
					setTimeout(function () {
						$('#zeeker-snackbar.zeeker-success').removeClass('show ');
					}, 3000);
				});
		},
		customAccordion: function (e) {
			// prevent default click event
			e.preventDefault();

			// set variables
			var $this = $(e.currentTarget),
				$content = $this.next(),
				$opened = $this.closest('.zeeker-accordion').find('> .zeeker-accordion-main > .zeeker-accordion-open');

			// check if any accordion is opened already, if opened, close it.
			$opened.not($this).removeClass('zeeker-accordion-open').next().animate({ height: '0' });

			// remove class from parent element
			$opened.not($this).parent().removeClass('content-visible');

			// check current click accordion is closed
			if (!$this.hasClass('zeeker-accordion-open')) {
				// if height is '0', add necessary class for the necessary elements and open the accordion
				$this.addClass('zeeker-accordion-open').parent().addClass('content-visible');

				$content.animate({ height: `${$content[0].scrollHeight}px` }, () => {
					setTimeout(() => {
						if ($this.hasClass('zeeker-accordion-open')) {
							$content.css('height', 'auto');
							console.log('height auto set');
						}
					}, 300);
				});
			} else {
				// current accordion is opened already, remove added class from necessary elements
				$this.removeClass('zeeker-accordion-open').parent().removeClass('content-visible');

				$content.animate({ height: '0' });
			}
		},
		updateConfigureOptions: function () {
			return false;
		},
		triggerFocusOptions: function (e) {
			e.preventDefault();
			this.inputValue = e.currentTarget.value;
		},
		triggerUpdateConfigureOptions: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				$error = $this.closest('.zeeker-accordion-wrapper').find('.zeeker-snackbar.zeeker-error'),
				$success = $this.closest('.zeeker-accordion-wrapper').find('.zeeker-snackbar.zeeker-success'),
				$form = $this.parents('form').submit();

			if ('text' === e.currentTarget.type) {
				if (this.inputValue === e.currentTarget.value) {
					return;
				}

				this.inputValue = '';
			}

			var b = $form.serialize();
			$.post('options.php', b)
				.error(function () {
					$error.addClass('show');
					setTimeout(function () {
						$error.removeClass('show');
					}, 3000);
				})
				.success(function () {
					$success.addClass('show');
					setTimeout(function () {
						$success.removeClass('show ');
					}, 3000);
				});
		},
		toggleDisplayOptions: function (e) {
			// e.preventDefault();
			// var current = e.currentTarget.checked;
			// var checked = current.checked;
			// console.log(checked);
		},
	};

	Zeeker.init();

	var ZeekerAdmin = (function () {
		/**
		 * Add loading class to show the loading effect.
		 *
		 * @param {[object]} $element jQuery DOM element.
		 */
		var addLoading = function addLoading($element) {
			$element.addClass('zeeker-loading');
		};

		/**
		 * Remove loading class to remove the loading effect.
		 *
		 * @param {[object]} $element jQuery DOM element.
		 */
		var removeLoading = function removeLoading($element) {
			$element.removeClass('zeeker-loading');
		};

		var showAction = function showAction(e) {
			e.preventDefault();
			var $this = $(e.currentTarget);
			$('.zeeker-widget-action-container')
				.removeClass('zeeker-active-content')
				.filter('#' + $this.data('action'))
				.addClass('zeeker-active-content');
		};

		/**
		 * Save form values on form Submit with AJAX to prevent page reload.
		 *
		 * @param {[object]} event JS form submit event.
		 */
		var saveWidgetDisplayOptions = function saveWidgetDisplayOptions(event) {
			event.preventDefault();
			var $form = $(event.currentTarget);
			$form.parent().find('.zeeker-error, .zeeker-success').remove();
			addLoading($form.closest('.widget-card'));
			$.ajax({
				url: zeeker.ajax,
				type: 'POST',
				data: {
					action: '_zeeker_widget_display_options',
					data: $form.serialize(),
				},
				success: function success(response) {
					if (true === response.success) {
						var _response$data;

						if ((_response$data = response.data) !== null && _response$data !== void 0 && _response$data['redirect']) {
							window.location.href = response.data.redirect;
						} else {
							$('.zeeker-tab-content .zeeker-toast-bar').html(response.data).addClass('show');
							setTimeout(function () {
								$('.zeeker-tab-content .zeeker-toast-bar').removeClass('show');
							}, 2990);
						}
					} else {
						var _response$data2;

						if ((_response$data2 = response.data) !== null && _response$data2 !== void 0 && _response$data2['zeeker_widget_id']) {
							$form.find('#zeeker_widget_id').after('<p class="zeeker-error">' + response.data.zeeker_widget_id + '</p>');
						} else {
							$form.before('<p class="zeeker-error">' + response.data + '</p>');
						}
					}

					removeLoading($form.closest('.widget-card'));
				},
				error: function error() {
					$form.before('<p class="zeeker-error">Display options save failed. Please try again.</p>');
					removeLoading($form.closest('.widget-card'));
				},
			});
		};

		/**
		 * Create new widget from.
		 *
		 * @param {[object]} event JS form submit event.
		 */
		var createWidget = function createWidget(event) {
			event.preventDefault();
			var $form = $(event.currentTarget);
			$form.parent().find('.zeeker-error').remove();
			addLoading($form.parent());
			$.ajax({
				url: zeeker.ajax,
				type: 'POST',
				data: {
					action: '_zeeker_create_widget',
					data: $form.serialize(),
				},
				success: function success(response) {
					if (false === response.success) {
						if ('string' === typeof response.data) {
							$form.before('<div class="zeeker-toast-bar toast-error">' + response.data + '</div>');
							$('.zeeker-tab-content .zeeker-toast-bar').addClass('show');
							setTimeout(function () {
								$form.prev('.zeeker-toast-bar').removeClass('show').html('').remove();
							}, 2990);
						} else {
							for (index in response.data) {
								if ($form.find('[name="' + index + '"]').length) {
									$form.find('[name="' + index + '"]').after('<p class="zeeker-error">' + response.data[index] + '</p>');
								}
							}
						}
					} else {
						var _response$data3;

						if ((_response$data3 = response.data) !== null && _response$data3 !== void 0 && _response$data3['redirect']) {
							window.location.href = response.data.redirect;
						}
					}

					removeLoading($form.parent());
				},
				error: function error(_error) {
					console.log(_error);
					removeLoading($form.parent());
				},
			});
		};

		/**
		 * Delete widget data with AJAX.
		 *
		 * @param {[object]} event JS click event.
		 * @returns null|void
		 */
		var deleteWidget = function deleteWidget(event) {
			event.preventDefault();
			var $parent = $(event.currentTarget).closest('.zeeker-widget-action-container');
			addLoading($parent);

			if (!confirm('Are you sure? This will remove the Widget from all your pages.')) {
				removeLoading($parent);
				return;
			}

			$.ajax({
				url: zeeker.ajax,
				type: 'POST',
				data: {
					action: '_zeeker_delete_widget',
					nonce: $(event.currentTarget).data('nonce'),
				},
				success: function success(response) {
					var _response$data4, _response$data5;

					if ((_response$data4 = response.data) !== null && _response$data4 !== void 0 && _response$data4['message']) {
						alert(response.data.message);
					}

					if ((_response$data5 = response.data) !== null && _response$data5 !== void 0 && _response$data5['redirect']) {
						window.location.href = response.data.redirect;
					}

					removeLoading($parent);
				},
				error: function error(_error2) {
					console.log(_error2);
					removeLoading($parent);
				},
			});
		};

		/**
		 * Refresh widget preview.
		 *
		 * @param {{object}} event JS click event.
		 */
		var refreshWidget = function refreshWidget(event) {
			event.preventDefault();
			addLoading($('.zeeker-widget-preview-bg'));
			$.get(window.location.href, function (response) {
				if ($(response).find('#widget-preview-container').length) {
					$('#widget-preview-container').html($(response).find('#widget-preview-container').html());
				}

				removeLoading($('.zeeker-widget-preview-bg'));
			});
		};

		/**
		 * Update widget active status.
		 *
		 * @param {[object]} event JS change event.
		 */
		var updateWidgetStatus = function updateWidgetStatus(event) {
			event.preventDefault();
			$.ajax({
				url: zeeker.ajax,
				type: 'POST',
				data: {
					action: '_zeeker_update_widget_status',
					nonce: $(event.currentTarget).data('nonce'),
					value: event.currentTarget.checked,
				},
				success: function success(response) {},
				error: function error(_error3) {},
			});
		};

		/**
		 * If selectAll checkbox is checked, enable/check all checkbox options,
		 * else disable/uncheck all checkbox options.
		 *
		 * @param {[object]} event JS change event.
		 */
		var selectAll = function selectAll(event) {
			var isChecked = event.currentTarget.checked;

			if (isChecked) {
				$('input[type="checkbox"].field-display-option').prop('checked', true).trigger('change');
			} else {
				$('input[type="checkbox"].field-display-option').prop('checked', false).trigger('change');
			}
		};

		/**
		 * If all checkbox options are checked, check the selectAll checkbox too.
		 *
		 * @param {[object]} event JS change event.
		 */
		var selectAllCheck = function selectAllCheck(event) {
			var allChecked = true;
			$('input[type="checkbox"].field-display-option').each(function () {
				if (!this.checked) {
					allChecked = false;
					return true;
				}
			});

			if (true === allChecked) {
				$('input#select-all').prop('checked', true);
			} else {
				$('input#select-all').prop('checked', false);
			}
		};

		/**
		 * Module related events.
		 */
		var events = function events() {
			// $('.zeeker-widget-action').on('click', '.button', showAction);
			$('.widget-display-options form').on('submit', saveWidgetDisplayOptions);
			$('form#create-widget').on('submit', createWidget);
			$('#zeeker-delete-widget').on('click', deleteWidget);
			$('.zeeker-refresh-widget').on('click', refreshWidget);
			$('.zeeker-widget-status').on('change', updateWidgetStatus);
			$('#select-all').on('change', selectAll);
			$('input[type="checkbox"].field-display-option').on('change', selectAllCheck);
		};

		/**
		 * Initialize module.
		 */
		var init = function init() {
			events();
		};

		return {
			init: init,
		};
	})();

	if ($('.zeeker-tab-contents').length) {
		ZeekerAdmin.init();
	}
})(jQuery);
