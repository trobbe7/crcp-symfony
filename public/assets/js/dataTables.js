$(document).ready(function() {
    var t = $('#dataTable').DataTable( {
        "columnDefs": [ {
            "targets": 0
        } ],
        "order": [[ 0, 'desc' ]]
    } );
} );
