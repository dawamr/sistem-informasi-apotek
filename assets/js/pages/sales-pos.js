(function(){
  var typing;
  function fmt(idr){ return 'Rp ' + (idr||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
  function debounce(fn, d){ return function(){ var ctx=this, args=arguments; clearTimeout(typing); typing=setTimeout(function(){ fn.apply(ctx,args); }, d||200); }; }

  function renderResults(items){
    var $list = $('#searchResults').empty();
    if (!items || !items.length) { return; }
    items.forEach(function(it){
      var disabled = it.stock<=0 ? ' disabled' : '';
      var badge = it.stock<=0 ? '<span class="badge bg-danger ms-2">Habis</span>' : '';
      var $a = $('<a href="#" class="list-group-item list-group-item-action"></a>')
        .attr('data-id', it.id)
        .attr('data-name', it.name)
        .attr('data-code', it.code)
        .attr('data-price', it.price)
        .attr('data-unit', it.unit)
        .toggleClass('disabled', it.stock<=0)
        .html('<div class="d-flex w-100 justify-content-between">'
          + '<h6 class="mb-1">'+it.name+' <small class="text-muted">('+it.code+')</small>'+badge+'</h6>'
          + '<small>'+fmt(it.price)+'</small>'
          + '</div>'
          + '<small>Stok: '+it.stock+' '+it.unit+'</small>');
      $list.append($a);
    });
  }

  function recalcTotals(){
    var total=0, items=0;
    $('#cartTable tbody tr').each(function(){
      var qty = parseInt($(this).find('.input-qty').val(),10)||0;
      var price = parseInt($(this).data('price'),10)||0;
      var sub = qty*price; items+=qty; total+=sub;
      $(this).find('.cell-subtotal').text(fmt(sub));
    });
    $('#totalItems').text(items);
    $('#totalAmount').text(fmt(total));
  }

  function addToCart(id, qty){
    $.post(baseUrl('sales/add'), { medicine_id:id, qty:qty }, function(resp){
      if (!resp || !resp.ok) return alert(resp && resp.error || 'Gagal menambahkan item');
      // refresh POS page to simplify sync
      location.reload();
    }, 'json');
  }

  function updateQty(id, qty){
    $.post(baseUrl('sales/update'), { medicine_id:id, qty:qty }, function(resp){
      if (!resp || !resp.ok) return alert(resp && resp.error || 'Gagal mengubah jumlah');
      recalcTotals();
    }, 'json');
  }

  function removeItem(id){
    $.post(baseUrl('sales/remove'), { medicine_id:id }, function(resp){
      if (!resp || !resp.ok) return alert(resp && resp.error || 'Gagal menghapus');
      $('tr[data-id="'+id+'"]').remove();
      recalcTotals();
    }, 'json');
  }

  function checkout(){
    $.post(baseUrl('sales/checkout'), {}, function(resp){
      if (!resp || !resp.ok) return alert(resp && resp.error || 'Checkout gagal');
      if (resp.redirect) location.href = resp.redirect;
    }, 'json');
  }

  function baseUrl(p){ return (window.BASE_URL||$('base').attr('href')||'/') + (p||''); }

  $(function(){
    // search typing
    $('#searchInput').on('input', debounce(function(){
      var q = this.value.trim();
      if (!q) return $('#searchResults').empty();
      $.getJSON(baseUrl('sales/search'), { q:q }, function(res){ renderResults(res && res.data || []); });
    }, 250));

    // click result to add
    $('#searchResults').on('click', 'a', function(e){ e.preventDefault();
      if ($(this).hasClass('disabled')) return;
      var id = $(this).data('id');
      var qty = parseInt($('#qtyInput').val(),10)||1;
      addToCart(id, qty);
    });

    // manual add by code in input if needed (fallback no-op)
    $('#btnAddManual').on('click', function(e){ e.preventDefault();
      var $first = $('#searchResults a').first();
      if ($first.length){ $first.trigger('click'); }
    });

    // change qty
    $('#cartTable').on('change', '.input-qty', function(){
      var $tr = $(this).closest('tr');
      var id = parseInt($tr.data('id'),10);
      var qty = parseInt($(this).val(),10)||1;
      if (qty<1) qty=1; $(this).val(qty);
      updateQty(id, qty);
    });

    // remove
    $('#cartTable').on('click', '.btn-remove', function(){
      var id = parseInt($(this).closest('tr').data('id'),10);
      if (!confirm('Hapus item ini?')) return;
      removeItem(id);
    });

    // preload price on rows for calc
    $('#cartTable tbody tr').each(function(){
      var priceText = $(this).find('td').eq(2).text().replace(/[^0-9]/g,'');
      $(this).attr('data-price', parseInt(priceText,10)||0);
    });

    // checkout
    $('#btnCheckout').on('click', function(){ checkout(); });
  });
})();
