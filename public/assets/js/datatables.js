/* ============================================================
 * DataTables
 * Generate advanced tables with sorting, export options using
 * jQuery DataTables plugin
 * For DEMO purposes only. Extract what you need.
 * ============================================================ */
(function($) {

    'use strict';

    var responsiveHelper = undefined;
    var breakpointDefinition = {
        tablet: 1024,
        phone: 480
    };

    // Initialize datatable showing a search box at the top right corner
    var initTableWithSearch = function() {
        var table = $('#tableWithSearch');

        var settings = {
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 20
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function() {
            table.fnFilter($(this).val());
        });
    }

    var initTableWithSearch2 = function() {
        var table = $('#tableWithSearch2');

        var settings = {
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 20
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table2').keyup(function() {
            table.fnFilter($(this).val());
        });
    }

    var initTableWithSearch3 = function() {
        var table = $('#tableWithSearch3');

        var settings = {
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": false,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 20
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table3').keyup(function() {
            table.fnFilter($(this).val());
        });
    }

    var initTableWithSearchRoom = function() {
        var table = $('#tableWithSearchRoom');

        var settings = {
            "order": [7, 'asc'],
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": false,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 30
        };

        table.dataTable(settings);

        // search box for table
        $('#search-tableRoom').keyup(function() {
            table.fnFilter($(this).val());
        });
    }


    var initTableWithSearchLiquidacion = function() {
        var table = $('#tableWithSearchLiquidacion');

        var settings = {
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": false,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 50
        };

        table.dataTable(settings);

        // search box for table
        $('#search-tableLiquidacion').keyup(function() {
            table.fnFilter($(this).val());
        });
    }

    var initTablePendientes = function() {
        var table = $('#tablePendientes');

        var settings = {
            "order": [0, 'desc'],
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": false,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 50,
            
        };

        table.dataTable(settings);

        // search box for table
        $('#searchPendientes').keyup(function() {
            table.fnFilter($(this).val());
        });
    }

    var initTableUser = function() {
        var table = $('#tableUser');

        var settings = {
            "order": [0, 'desc'],
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": false,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 50,
            
        };

        table.dataTable(settings);

        // search box for table
        $('#searchUser').keyup(function() {
            table.fnFilter($(this).val());
        });
    }

    // Initialize datatable with ability to add rows dynamically
    var initTableWithDynamicRows = function() {
        var table = $('#tableWithDynamicRows');


        var settings = {
            "sDom": "<t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 5
        };


        table.dataTable(settings);

        $('#show-modal').click(function() {
            $('#addNewAppModal').modal('show');
        });

        $('#add-app').click(function() {
            table.dataTable().fnAddData([
                $("#appName").val(),
                $("#appDescription").val(),
                $("#appPrice").val(),
                $("#appNotes").val()
            ]);
            $('#addNewAppModal').modal('hide');

        });
    }

    // Initialize datatable showing export options
    var initTableWithExportOptions = function() {
        var table = $('#tableWithExportOptions');


        var settings = {
            "sDom": "<'exportOptions'T><'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
            },
            "iDisplayLength": 10000,
            "oTableTools": {
                "sSwfPath": "assets/plugins/jquery-datatable/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [{
                    "sExtends": "csv",
                    "sButtonText": "<i class='pg-grid'></i>",
                }, {
                    "sExtends": "xls",
                    "sButtonText": "<i class='fa fa-file-excel-o'></i>",
                }, {
                    "sExtends": "pdf",
                    "sButtonText": "<i class='fa fa-file-pdf-o'></i>",
                }, {
                    "sExtends": "copy",
                    "sButtonText": "<i class='fa fa-copy'></i>",
                }]
            },
            fnDrawCallback: function(oSettings) {
                $('.export-options-container').append($('.exportOptions'));

                $('#ToolTables_tableWithExportOptions_0').tooltip({
                    title: 'Export as CSV',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_1').tooltip({
                    title: 'Export as Excel',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_2').tooltip({
                    title: 'Export as PDF',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_3').tooltip({
                    title: 'Copy data',
                    container: 'body'
                });
            }
        };


        table.dataTable(settings);

    }

  // Initialize datatable showing export options
  var initTableWithOrderingOptions = function() {
    var table = $('#tableWithOrderingOptions');
    var settings = {
      "columnDefs": [ {
        "targets": 0,
        "orderable": false
      } ],
      "destroy": true,
      "scrollCollapse": true,
      "oLanguage": {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Mostrando <b>_START_ de _END_</b> de _TOTAL_"
      },
      "iDisplayLength": 1000
    };


    table.dataTable(settings);

  }

    initTableWithSearch();
    initTableWithSearch2();
    initTableWithSearch3();
    initTableWithSearchRoom();
    initTablePendientes();
    initTableUser();
    initTableWithSearchLiquidacion();
    initTableWithDynamicRows();
    initTableWithExportOptions();
    initTableWithOrderingOptions();

})(window.jQuery);