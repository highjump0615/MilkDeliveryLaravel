
var ue = UE.getEditor('editor');

$(document).ready(function () {
    ue = UE.getEditor('editor');
});

function show_ue_content(ue_data) {
    if (ue_data == "")
        return;
    var data1 = ue_data[0].data.toString();
    ue.ready(function () {
        ue.setContent(data1, false);
    });
}

function ue_getContent() {
    var arr = [];
    arr.push(UE.getEditor('editor').getContent());
    return (arr.join("\n"));
}
