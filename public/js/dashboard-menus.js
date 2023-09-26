var dataTable;
var start = null;
var end = null;
var totalColumns = null;
$(document).ready(function() {
    var dateColumnCount = 2;
    var sortedColumn = 1;
    if (typeof(userFullSession) != "undefined" && userFullSession !== null) {
        dateColumnCount = 1;
        sortedColumn = 0;
    }
    dataTable = $('#usersFullSession, #submitTicketsWereOpened, #selfResolvedMade').DataTable({
        order: [
            [sortedColumn, 'desc']
        ],
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excel',
            text: 'Export Data'
        }],
        //dom: "<'row'<'col-sm-2'l><'col-sm-5 text-center'i><'col-sm-5'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'B><'col-sm-7'p>>",
        //"pageLength": 30,
        //"lengthMenu": [10, 25, 30, 50, 75, 100]
    });
    totalColumns = dataTable.columns().nodes().length;
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var date = new moment(data[totalColumns - dateColumnCount]).format('YYYY-MM-DD');
        if (
            (start == null || end == null) || (moment(date).isSameOrAfter(start.format('YYYY-MM-DD')) && moment(date).isSameOrBefore(end.format('YYYY-MM-DD')))) {
            return true;
        }
        return false;
    });

    function cb(min, max) {
        start = min;
        end = max;
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' <img class="calender_arrow" src="images/calender_arrow.svg"> ' + end.format('MMMM D, YYYY'));
        $("div.daterangepicker").addClass("show-calendar");
        dataTable.draw();
    }
    start = moment().subtract(1, 'month').utcOffset(0);
    end = moment().utcOffset(0);
    $('#reportrange').daterangepicker({
        // timePicker: true,
        startDate: start,
        endDate: end,
        opens: 'right',
        autoApply: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    cb(start, end);
});