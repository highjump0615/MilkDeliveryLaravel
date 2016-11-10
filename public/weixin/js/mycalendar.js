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
                    ret = ret + $(this).children().html() + ",";
                }
                else
                    ret = ret + "0,";
            }
            i++;
        }
    );
    return ret;
}

function mycalendar(id)
{
    this.id = id;
    this.get_submint_value = calendar_get_numbers;
    this.start_index = 0;

    var dt = new Date();
    var lastdate = new Date(dt.getYear(), dt.getMonth() + 1, 0);
    var day = dt.getDay();
    day = (day - (dt.getDate()-1)%7 + 7 ) % 7;

    this.start_index = day;

    var header = "<tr>";
    var data = "<tr height='62px'>";
    for( var i=0; i<day; i++ )
    {
        header = header + "<th scope='col'></th>";
        data = data + "<td></td>";
    }
    var i = 1;
    while( i <= lastdate.getDate() )
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
            data = data + "<td></td>";
        }
        else
        {
            header = header + "<th scope='col'>" + i + "</th>";
            data = data + "<td></td>";
        }
        i++;
        day++;
    }
    if( day % 7 != 0 )
    {
        while(day%7 != 0)
        {
            header = header + "<th scope='col'></th>";
            data = data + "<td></td>";
            day++;
        }
        header = header + "</tr>";
        data = data + "</tr>";
        $("#"+id).append(header);
        $("#"+id).append(data);
    }

/*    $("table.psgzb td > div").click(function(){
        if($(this).is(":first-child"))
        {
            $(this).html(parseInt($(this).html())+1);
        }
        else
        {
            $(this).parent().html("");
        }
        return false;
    });*/
    $("#" + id + " td").click(function(){
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
