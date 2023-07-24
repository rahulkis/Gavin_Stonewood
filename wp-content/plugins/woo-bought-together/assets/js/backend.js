'use strict';

(function($) {
  var woobt_timeout = null;

  $(function() {
    woobt_settings();

    // options page
    woobt_options();

    // arrange
    woobt_arrange();

    // button
    woobt_button();

    // default products
    woobt_default_products();

    // count
    woobt_init_count();
  });

  $(document).on('click touch', '.woobt_displaying', function(e) {
    e.preventDefault();

    if ($(this).hasClass('woobt_displaying_open')) {
      $('.woobt_show_if_displaying').hide();
    } else {
      $('.woobt_show_if_displaying').show();
    }

    $(this).toggleClass('woobt_displaying_open');
  });

  $(document).on('click touch', '.woobt-import-export', function(e) {
    // open import/export popup
    e.preventDefault();

    var ids = $('#woobt_selected').find('input, select').serializeArrayAll();

    if (!$('#woobt_import_export').length) {
      $('body').append('<div id=\'woobt_import_export\'></div>');
    }

    $('#woobt_import_export').html('Loading...');

    $('#woobt_import_export').dialog({
      minWidth: 460,
      title: 'Import/Export',
      modal: true,
      dialogClass: 'wpc-dialog',
      open: function() {
        $('.ui-widget-overlay').bind('click', function() {
          $('#woobt_import_export').dialog('close');
        });
      },
    });

    var data = {
      action: 'woobt_import_export', ids: ids,
    };

    $.post(ajaxurl, data, function(response) {
      $('#woobt_import_export').html(response);
    });
  });

  $(document).on('click touch', '.woobt-import-export-save', function(e) {
    if (confirm('Are you sure?')) {
      $(this).addClass('disabled');

      var ids = $('.woobt_import_export_data').val();
      var data = {
        action: 'woobt_import_export_save',
        ids: ids,
      };

      $.post(ajaxurl, data, function(response) {
        $('#woobt_import_export').dialog('close');
        $('#woobt_selected ul').html(response);
        woobt_arrange();
      });
    }
  });

  $(document).on('click touch', '#woobt_search_settings_btn', function(e) {
    // open search settings popup
    e.preventDefault();

    var title = $('#woobt_search_settings').attr('data-title');

    $('#woobt_search_settings').
        dialog({
          minWidth: 540,
          title: title,
          modal: true,
          dialogClass: 'wpc-dialog',
          open: function() {
            $('.ui-widget-overlay').bind('click', function() {
              $('#woobt_search_settings').dialog('close');
            });
          },
        });
  });

  $(document).on('click touch', '#woobt_search_settings_update', function(e) {
    // save search settings
    e.preventDefault();

    $('#woobt_search_settings').addClass('woobt_search_settings_updating');

    var data = {
      action: 'woobt_update_search_settings',
      limit: $('.woobt_search_limit').val(),
      sku: $('.woobt_search_sku').val(),
      id: $('.woobt_search_id').val(),
      exact: $('.woobt_search_exact').val(),
      sentence: $('.woobt_search_sentence').val(),
      same: $('.woobt_search_same').val(),
      types: $('.woobt_search_types').val(),
    };

    $.post(ajaxurl, data, function(response) {
      $('#woobt_search_settings').removeClass('woobt_search_settings_updating');
    });
  });

  $(document).on('change', 'select.woobt_change_price', function() {
    woobt_options();
  });

  $(document).on('change', 'select.woobt_atc_button', function() {
    woobt_button();
  });

  $(document).on('change', 'select.woobt_default', function() {
    woobt_default_products();
  });

  // set optional
  $(document).on('click touch', '#woobt_custom_qty', function() {
    if ($(this).is(':checked')) {
      $('.woobt_tr_show_if_custom_qty').show();
      $('.woobt_tr_hide_if_custom_qty').hide();
      $('#woobt_sync_qty').prop('checked', false);
    } else {
      $('.woobt_tr_show_if_custom_qty').hide();
      $('.woobt_tr_hide_if_custom_qty').show();
    }
  });

  // add text
  $(document).on('click touch', '.woobt_add_text', function(e) {
    e.preventDefault();

    var data = {
      action: 'woobt_add_text',
    };

    $.post(ajaxurl, data, function(response) {
      $('#woobt_selected ul').append(response);
    });
  });

  // search input
  $(document).on('keyup', '#woobt_keyword', function() {
    if ($('#woobt_keyword').val() != '') {
      $('#woobt_loading').show();

      if (woobt_timeout != null) {
        clearTimeout(woobt_timeout);
      }

      woobt_timeout = setTimeout(woobt_ajax_get_data, 300);

      return false;
    }
  });

  // actions on search result items
  $(document).on('click touch', '#woobt_results li', function() {
    $(this).children('.woobt-remove').html('×');
    $('#woobt_selected ul').append($(this));
    $('#woobt_results').html('').hide();
    $('#woobt_keyword').val('');
    woobt_arrange();
    woobt_init_count();

    return false;
  });

  // actions on selected items
  $(document).on('click touch', '#woobt_selected .woobt-remove', function() {
    $(this).parent().remove();
    woobt_init_count();

    return false;
  });

  // hide search result box if click outside
  $(document).on('click touch', function(e) {
    if ($(e.target).closest($('#woobt_results')).length == 0) {
      $('#woobt_results').html('').hide();
    }
  });

  function woobt_settings() {
    // hide search result box by default
    $('#woobt_results').html('').hide();
    $('#woobt_loading').hide();

    // show or hide limit
    if ($('#woobt_custom_qty').is(':checked')) {
      $('.woobt_tr_show_if_custom_qty').show();
      $('.woobt_tr_hide_if_custom_qty').hide();
      $('#woobt_sync_qty').prop('checked', false);
    } else {
      $('.woobt_tr_show_if_custom_qty').hide();
      $('.woobt_tr_hide_if_custom_qty').show();
    }
  }

  function woobt_options() {
    if ($('select.woobt_change_price').val() == 'yes_custom') {
      $('.woobt_change_price_custom').show();
    } else {
      $('.woobt_change_price_custom').hide();
    }
  }

  function woobt_button() {
    if ($('select.woobt_atc_button').val() == 'separate') {
      $('select.woobt_show_this_item').
          val('yes').
          trigger('change').
          prop('disabled', 'disabled');
    } else {
      $('select.woobt_show_this_item').prop('disabled', false);
    }
  }

  function woobt_default_products() {
    if ($('select.woobt_default').val() != 'none') {
      $('.woobt_show_if_default_products').show();
    } else {
      $('.woobt_show_if_default_products').hide();
    }
  }

  function woobt_init_count() {
    if ($('li.woobt_options').length && $('#woobt_selected').length) {
      var count = $('#woobt_selected .woobt-li-product').length;

      if ($('li.woobt_options a span.count').length) {
        $('li.woobt_options a span.count').html('(' + count + ')');
      } else {
        $('<span class="count">(' + count + ')</span>').
            appendTo($('li.woobt_options a'));
      }
    }
  }

  function woobt_arrange() {
    $('#woobt_selected ul').sortable({
      handle: '.woobt-move',
    });
  }

  function woobt_ajax_get_data() {
    // ajax search product
    woobt_timeout = null;

    var ids = [];

    $('#woobt_selected').find('.woobt-li-product').each(function() {
      ids.push($(this).attr('data-id'));
    });

    var data = {
      action: 'woobt_get_search_results',
      woobt_keyword: $('#woobt_keyword').val(),
      woobt_id: $('#post_ID').val(),
      woobt_ids: ids.join(),
    };

    $.post(ajaxurl, data, function(response) {
      $('#woobt_results').show();
      $('#woobt_results').html(response);
      $('#woobt_loading').hide();
    });
  }

  $.fn.serializeArrayAll = function() {
    var rCRLF = /\r?\n/g;

    return this.map(function() {
      return this.elements ? $.makeArray(this.elements) : this;
    }).map(function(i, elem) {
      var val = $(this).val();

      if (val == null) {
        return val == null;
      } else if (this.type === 'checkbox') {
        if (this.checked) {
          return {name: this.name, value: this.checked ? this.value : ''};
        }
      } else if (this.type === 'radio') {
        if (this.checked) {
          return {name: this.name, value: this.checked ? this.value : ''};
        }
      } else {
        return $.isArray(val) ? $.map(val, function(val, i) {
          return {name: elem.name, value: val.replace(rCRLF, '\r\n')};
        }) : {name: elem.name, value: val.replace(rCRLF, '\r\n')};
      }
    }).get();
  };
})(jQuery);