
$('button[data-action = "print"]').click(function () {

    var od = $('#customerTable').css('display');
    var fd = $('#filteredTable').css('display');

    if (od != "none") {
        printContent('customerTable', gnUserTypeStation, '客户管理');
    }
    else if (fd != "none") {
        printContent('filteredTable', gnUserTypeStation, '客户管理');
    }
});

$('button[data-action = "export_csv"]').click(function () {

    var od = $('#customerTable').css('display');
    var fd = $('#filteredTable').css('display');

    if (od != "none") {
        data_export('customerTable', gnUserTypeStation, '客户管理', 0, 0);
    }
    else if (fd != "none") {
        data_export('filteredTable', gnUserTypeStation, '客户管理', 0, 0);
    }
});