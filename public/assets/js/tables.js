/* ============================================================
 * Tables
 * Generate advanced tables with sorting, export options using
 * jQuery DataTables plugin
 * For DEMO purposes only. Extract what you need.
 * ============================================================ */
(function($) {

    'use strict';

    // Initialize a basic dataTable with row selection option
    var initBasicTable = function() {

        var table = $('#basicTable');

        var settings = {
            "sDom": "t",
            "destroy": true,
            "paging": false,
            "scrollCollapse": true,
            "aoColumnDefs": [{
                'bSortable': false,
                'aTargets': [0]
            }],
            "order": [
                [1, "desc"]
            ]

        };

        table.dataTable(settings);

        $('#basicTable input[type=checkbox]').click(function() {
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('selected');
            } else {
                $(this).closest('tr').removeClass('selected');
            }

        });

    }

    // Initialize a dataTable having bootstrap's stripes style
    var initStripedTable = function() {

        var table = $('#stripedTable');

        var settings = {
            "sDom": "t",
            "destroy": true,
            "paging": false,
            "scrollCollapse": true

        };
        table.dataTable(settings);

    }

    // Initialize a dataTable with collapsible rows to show more details
    var initDetailedViewTable = function() {

        var _format = function(d) {
            var tel = d[2];
            var price = d[7];
            // `d` is the original data object for the row
            return '<table class="table table-inline" style="margin:0px">' +
                    '<tr>' +
                    '<td><a href="tel:'+tel+'"><i class="fa fa-phone fa-2x"></i></a></td>' +
                    '<td>'+price+' €</td>' +
                    '<td><i class="fa fa-users"></i></td>' +
                    '<td><i class="fa fa-users"></i></td>' +
                    '<td><i class="fa fa-users"></i></td>' +
                    '</tr>' +
                    '</table>';
            
        }


        var table = $('#detailedTable');

        table.DataTable({
            "sDom": "t",
            "scrollCollapse": true,
            "paging": true,
            "bSort": false
        });

        // Add event listener for opening and closing details
        $('#detailedTable tbody').on('click', 'tr', function() {
            //var row = $(this).parent()
            if ($(this).hasClass('shown') && $(this).next().hasClass('row-details')) {
                $(this).removeClass('shown');
                $(this).next().remove();
                return;
            }
            var tr = $(this).closest('tr');
            var row = table.DataTable().row(tr);

            $(this).parents('tbody').find('.shown').removeClass('shown');
            $(this).parents('tbody').find('.row-details').remove();

            row.child(_format(row.data())).show();
            tr.addClass('shown');
            tr.next().addClass('row-details');
        });

    }

    // Initialize a condensed table which will truncate the content 
    // if they exceed the cell width
    var initCondensedTable = function() {
        var table = $('#condensedTable');

        var settings = {
            "sDom": "t",
            "destroy": true,
            "paging": false,
            "scrollCollapse": true
        };

        table.dataTable(settings);
    }

    initBasicTable();
    initStripedTable();
    initDetailedViewTable();
    initCondensedTable();

})(window.jQuery);