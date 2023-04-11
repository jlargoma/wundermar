(function($, undefined){

    $.fn.extend({

        /**
         * Function to render HTML from template and data
         * 
         * @param {Object} data - Collection of data ( array or object )
         * @param {Boolean} render - Return jQuery object or not
         * @return {jQueryObject|String}
         *
         * @example:
         *   $("script#template-example")
         *   .render(data)
         *   .appendTo("#container");
         */
        render: function(data, render){
           if (typeof data == 'undefined') return;
            var tmpl, html, create;

            tmpl = this.html();
            html = "";
            create = function(t, d, i){
              if (typeof t != 'undefined'){
                var m = t.match(/\:\:(.+?)\:\:/g) || [], r = t;
                r = r.replace('::ID::',i);
                $.each(m, function(i, k){
                    var v = d[ k.replace(/\:|\:/g, "") ];
                    r = r.replace(k, ( v === undefined ) ? "" : v);
                });
                
                return r;
              }
            };

            data = (data === undefined) ? {} : data;
            render = (render === undefined) ? true : false;

            if(! (data instanceof Array)){
                data = [data];
            }

            $.each(data, function(i, o){
                html += create(tmpl, o, i);
            });

            return render ? $("<div>").html(html).children() : html;
        }
    });

}(jQuery));

Date.prototype.yyyymmdd = function() {
  var mm = this.getMonth() + 1; // getMonth() is zero-based
  var dd = this.getDate();

  return [this.getFullYear(),
          (mm>9 ? '' : '0') + mm,
          (dd>9 ? '' : '0') + dd
         ].join('-');
};


$(document).ready(function () {

  var aMonths= new Array('','Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic');

  
var bkg = {
  data:{
    _token:null,
    pax:4,
    start:'',
    end:'',
    galleries:null,
    date_start:{d:0,m:0},
    date_end:{d:0,m:0},
    items:[],
    selected:{
      i:null,
      p:null,
      t:null,
      n:null,
      ext:[
        {t:'Desayuno',p:'1000',s:false},
        {t:'Parking',p:'1000',s:false},
      ]
    },
    loading: false,
  },
  _loading: function(){
    this.data.loading = !this.data.loading;
    if (this.data.loading) document.getElementById("bkg_loading").style.display = "block";
    else document.getElementById("bkg_loading").style.display = "none";
  },
  _getToken : function(){
    var that = this;
    if (!that.data._token){
      $.get("/api/booking",function(result){
        that.data._token = result._token;
      });
    }
  },

  _getItems : function(){
    var that = this;
    that._loading();
    $('#bkg_summary').hide();
    $.post("/api/get-items-suggest", that.data, function(result){
      $('.bkg_roomsLst').show();
      if (result == 'empty'){
        $("#roomsLst").html('<tr><td class="alert alert-warning">No hay habitaciones disponibles</td></tr>');
      } else {
        that.data.items = result;
        var html = $("#roomsTemplate").render(result);
        $("#roomsLst").html(html);
        
        
        $.each(that.data.items, function(i, o){
          TobiiAttr.selector = '.bkg_gl_'+i;
          new Tobii(TobiiAttr);
        });
    
        

      }
      that._loading();
    });
  },
  _selectItem : function(i){
    
    var selected = this.data.items[i];
    $('#bkg_summary').show();
    this.data.selected.i  = i;
    this.data.selected.n  = selected.name;
    this.data.selected.t  = selected.tit;
    this.data.selected.p  = selected.price;
    this.data.selected.pvp  = selected.pvp;
    
    this.data.selected.ext[0].s = false;
    this.data.selected.ext[1].s = false;
    
    this._summaryRender();
    
    /*****/
    if(this.data.date_start.m == this.data.date_end.m){
      $('.bkg_summary_date').text(
          parseInt(this.data.date_start.d)
          +'-'+parseInt(this.data.date_end.d)
          +' '+aMonths[parseInt(this.data.date_end.m)]
          );
    } else {
      $('.bkg_summary_date').text(
          parseInt(this.data.date_start.d)
          +' '+aMonths[parseInt(this.data.date_start.m)]
          +' / '+parseInt(this.data.date_end.d)
          +' '+aMonths[parseInt(this.data.date_end.m)]
          );
    }
  },
  _summaryRender: function(){
    var that = this;
    var total = parseInt(that.data.selected.pvp);
    var summary = [
      {n:that.data.selected.t,p:that._formatEu(that.data.selected.pvp)}
    ];
    
    $.each(that.data.selected.ext, function(i, o){
      if (o.s){
        total += parseInt(o.p);
        summary.push({n:o.t,p:that._formatEu(o.p)});
      }
    });
    summary.push({n:'Total',p:that._formatEu(total)});
    
    var html = $("#bkg_summaryTemplate").render(summary);
    $("#bkg_summaryLst").html(html);
  },
  _showDateRange: function(){
    var aux = this.data.start.split('-');
    this.data.date_start.d = aux[2];
    this.data.date_start.m = aux[1];
    $('.bkg_start').find('.day').text(this.data.date_start.d);
    $('.bkg_start').find('.month').text(aMonths[parseInt(aux[1])]);

    var aux = this.data.end.split('-');
    this.data.date_end.d = aux[2];
    this.data.date_end.m = aux[1];
    $('.bkg_end').find('.day').text(aux[2]);
    $('.bkg_end').find('.month').text(aMonths[parseInt(aux[1])]);
  },
  _formatEu: function(val){
    return new Intl.NumberFormat('de-DE', {
              style: 'currency',
              currency: 'EUR',
              minimumFractionDigits: 0
           }).format(parseInt(val));
     
  },
  _finish: function(){
    var that = this;
    that._loading();
    $.post("/api/finish_booking", that.data, function(result){
      that._loading();
      $('#paymentBooking').show('slow').html(result);
    });
  }
  
};

  /** Start Plugin */
  var today = new Date();
  bkg.data.start = today.yyyymmdd();
  today.setDate(today.getDate() + 2);
  bkg.data.end = today.yyyymmdd();
  bkg._showDateRange();
  bkg._getToken();
  $('.bkg_pax_num').val(bkg.data.pax);
  
  /** Start Plugin */

  /** jQuery events **/
  $('._bkg_openDateRange').on('click', function () {
    $('#daterangepickerdates').trigger("click");
  });
  
  $('#daterangepickerdates').on('apply.daterangepicker', function (ev, picker) {
    if (typeof picker != 'undefined') {
      bkg._getToken();
      bkg.data.start = picker.startDate.format('YYYY-MM-DD');
      bkg.data.end = picker.endDate.format('YYYY-MM-DD');
      bkg._showDateRange();
      bkg._getItems();
    }
  });
  
  $('.bkg_pax').on('change','.bkg_pax_num', function () {
    bkg.data.pax = $(this).val();
    bkg._getItems();
  });
  $('.bkg_pax').on('click','.bkg_pax_sum', function () {
    bkg.data.pax++;
    $('.bkg_pax_num').val(bkg.data.pax);
    bkg._getItems();
  });
  $('.bkg_pax').on('click','.bkg_pax_rest', function () {
    bkg.data.pax--;
    $('.bkg_pax_num').val(bkg.data.pax);
    bkg._getItems();
  });

  $('#roomsLst').on('click','.bkg_s_detail', function () {
    var k = $(this).data('k');
    var tit = $(this).data('t');
    $.get("/api/booking/detail/"+k,function(result){
      var obj = $('#bkg_roomDetailModal');
      obj.find('.bkg_modal_tit').text(tit);
      obj.find('.bkg_modal_detail').html(result);
      obj.modal('show');
    });
  });
  $('#roomsLst').on('click','button', function () {
    bkg._selectItem($(this).data('k'));
  });
  $('#bkg_summary').on('click','button', function () {
    bkg._finish();
  });
  
  $('#bkg_summary').on('change','.bkg_summary_extr', function () {
    if ($(this).data('k') == 'Desayuno'){
      bkg.data.selected.ext[0].s = $(this).is(':checked');
    }
    if ($(this).data('k') == 'Parking'){
      bkg.data.selected.ext[1].s = $(this).is(':checked');
    }
    bkg._summaryRender();
  });
  
  
});



/****************************************************************/
var TobiiAttr = {
  selector: '.bkg_gl_1',
  navLabel: ['Anterior', 'Siguiente'],
  close: true,
  closeLabel: 'Close lightbox',
  loadingIndicatorLabel: 'Image loading',
  counter: true,
  keyboard: true,
  docClose: true,
  swipeClose: true,
  hideScrollbar: true,
  draggable: true,
  threshold: 100,
};
