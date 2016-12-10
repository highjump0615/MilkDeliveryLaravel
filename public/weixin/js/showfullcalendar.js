/**
 * Created by Administrator on 11/2/2016.
 */
function calendar_get_numbers()
{
    var ret="";
    var i = 0;
    var start_index = this.start_index;
    $("#"+this.id+" td").each(
        function(){
            if( i >= start_index ) {
                if ($(this).children().length == 2) {
                    ret = ret + $(this).data('dates')+":"+$(this).children().html() + ",";
                }
            }
            i++;
        }
    );
    return ret;
}

function calendar_set_numbers()
{
    var data = this.custom_dates;
    data = data.replace(/,\s*$/, "");
    var data_array = data.split(',');
    for(var i =0; i< data_array.length; i++)
    {
        var day_val = data_array[i];
        var day= day_val.split(":")[0];
        var val = day_val.split(":")[1];
        var td =$("#"+this.id+" tr").find('td[data-dates="'+day+'"]').eq(0);
        $(td).html("<div class='psgzb_number'>"+val+"</div><div class='psgzb_remove'><img src='images/sb.png' width='20'></div>");

        $(td).children().click(function(){
            if($(this).is(":first-child"))
            {
                $(this).html(parseInt($(this).html())+1);
            }
            else
            {
                $(this).parent().html("");
            }
            return false;
        });
    }
}


//show only 1~31
function showfullcalendar(id)
{
    this.id = id;
    this.custom_dates = "";

    this.get_submit_value = calendar_get_numbers;
    this.set_custom_date = calendar_set_numbers;

    this.start_index = 0;

    var lastdate = 31;

    this.start_index = 0;
    var day = 0;

    var header = "<tr>";
    var data = "<tr height='62px'>";

    var i = 1;
    while( i <= lastdate)
    {
        if( i != 1 && day % 7 == 0 )
        {
            header = header + "</tr>";
            data = data + "</tr>";
            $("#"+id).append(header);
            $("#"+id).append(data);
            header = "<tr>";
            data = "<tr height='62px'>";
            header = header + "<th scope='col'>" + i + "</th>";
            data = data + "<td data-dates='"+i+"'></td>";
        }
        else
        {
            header = header + "<th scope='col'>" + i + "</th>";
            data = data + "<td data-dates='"+i+"'></td>";
        }
        i++;
        day++;
    }

    if( day % 7 != 0 )
    {
        while(day%7 != 0)
        {
            header = header + "<th scope='col'></th>";
            data = data + "<td class='day_disabled'></td>";
            day++;
        }
        header = header + "</tr>";
        data = data + "</tr>";
        $("#"+id).append(header);
        $("#"+id).append(data);
    }

    // $("table.psgzb td > div").click(function(){
    //     if($(this).is(":first-child"))
    //     {
    //         $(this).html(parseInt($(this).html())+1);
    //     }
    //     else
    //     {
    //         $(this).parent().html("");
    //     }
    //     return false;
    // });
    //
    $("#" + id + " td:not(.day_disabled)").click(function(){

        if($(this).children().length != 2)
        {
            $(this).html("<div class='psgzb_number'>1</div><div class='psgzb_remove'><img src='images/sb.png' width='20'></div>");
            $(this).children().click(function(){
                if($(this).is(":first-child"))
                {
                    $(this).html(parseInt($(this).html())+1);
                }
                else
                {
                    $(this).parent().html("");
                }
                return false;
            });
        }
    });
}


//show only 1~31
function showfullcalendar2(id, change_func)
{
    this.id = id;
    this.custom_dates = "";

    this.get_submit_value = calendar_get_numbers;
    this.set_custom_date = calendar_set_numbers;
    this.change_func = change_func;

    this.start_index = 0;

    var lastdate = 31;

    this.start_index = 0;
    var day = 0;

    var header = "<tr>";
    var data = "<tr height='62px'>";

    var i = 1;
    while( i <= lastdate)
    {
        if( i != 1 && day % 7 == 0 )
        {
            header = header + "</tr>";
            data = data + "</tr>";
            $("#"+id).append(header);
            $("#"+id).append(data);
            header = "<tr>";
            data = "<tr height='62px'>";
            header = header + "<th scope='col'>" + i + "</th>";
            data = data + "<td data-dates='"+i+"'></td>";
        }
        else
        {
            header = header + "<th scope='col'>" + i + "</th>";
            data = data + "<td data-dates='"+i+"'></td>";
        }
        i++;
        day++;
    }

    if( day % 7 != 0 )
    {
        while(day%7 != 0)
        {
            header = header + "<th scope='col'></th>";
            data = data + "<td class='day_disabled'></td>";
            day++;
        }
        header = header + "</tr>";
        data = data + "</tr>";
        $("#"+id).append(header);
        $("#"+id).append(data);
    }

    // $("table.psgzb td > div").click(function(){
    //     if($(this).is(":first-child"))
    //     {
    //         $(this).html(parseInt($(this).html())+1);
    //     }
    //     else
    //     {
    //         $(this).parent().html("");
    //     }
    //     return false;
    // });
    //
    $("#" + id + " td:not(.day_disabled)").click(function(){

        if($(this).children().length != 2)
        {
            $(this).html("<div class='psgzb_number'>1</div><div class='psgzb_remove'><img src='images/sb.png' width='20'></div>");
            $(this).children().click(function(){
                if($(this).is(":first-child"))
                {
                    $(this).html(parseInt($(this).html())+1);
                    change_func();
                }
                else
                {
                    $(this).parent().html("");
                    change_func();
                }
                return false;
            });
        }
    });
}
