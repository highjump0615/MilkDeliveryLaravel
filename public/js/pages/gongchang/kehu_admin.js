
$('button[data-action = "print"]').click(function () {

    var od = $('#customerTable').css('display');
    var fd = $('#filteredTable').css('display');

    if (od != "none") {
        printContent('customerTable', gnUserTypeFactory, '客户列表');
    }
    else if (fd != "none") {
        printContent('filteredTable', gnUserTypeFactory, '客户列表');
    }
});

$('button[data-action = "export_csv"]').click(function () {

    var od = $('#customerTable').css('display');
    var fd = $('#filteredTable').css('display');

    if (od != "none") {
        data_export('customerTable', gnUserTypeFactory, '客户列表', 0, 0);
    }
    else if (fd != "none") {
        data_export('filteredTable', gnUserTypeFactory, '客户列表', 0, 0);
    }
});