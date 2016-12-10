/**
 * Created by Administrator on 11/2/2016.
 */
function week_get_numbers()
{
    var ret="";
    $("#"+this.id+" td").each(
        function(){
            if( $(this).children().length == 2 )
            {
                ret = ret + $(this).data('dates')+":"+$(this).children().html() + ",";
            }
        }
    );
    return ret;
}

function week_set_numbers()
{
    var data = this.custom_dates;
    data = data.replace(/,\s*$/, "");
    var data_array = data.split(',');
    for(var i =0; i< data_array.length; i++)
    {
        var day_val = data_array[i];
        var day= day_val.split(":")[0];
        var val = day_val.split(":")[1];

        if(day == 7)
        {
            day = 0;
        }

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

function showmyweek(id)
{
    this.id = id;
    this.obj = $("#"+id);
    this.custom_dates = "";
    this.get_submit_value = week_get_numbers;
    this.set_custom_date = week_set_numbers;

    var arr = new Array("周日","周一","周二","周三","周四","周五","周六");

    var header = "<tr>";
    var data = "<tr height='62px'>";
    for( var i=0; i<7; i++ )
    {
        header = header + "<th scope='col'>" + arr[i] + "</th>";
        data = data + "<td data-dates='"+(i)+"'></td>";
    }
    $("#"+id).append(header);
    $("#"+id).append(data);

    $("table#week td").click(function(){
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


function showmyweek2(id, change_func)
{
    this.id = id;
    this.obj = $("#"+id);
    this.custom_dates = "";
    this.get_submit_value = week_get_numbers;
    this.set_custom_date = week_set_numbers;
    this.change_func = change_func;

    var arr = new Array("周日", "周一","周二","周三","周四","周五","周六");

    var header = "<tr>";
    var data = "<tr height='62px'>";
    for( var i=0; i<7; i++ )
    {
        header = header + "<th scope='col'>" + arr[i] + "</th>";
        data = data + "<td data-dates='"+(i)+"'></td>";
    }
    $("#"+id).append(header);
    $("#"+id).append(data);

    $("table#week td").click(function(){
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