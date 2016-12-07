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

function myweek(id)
{
    this.id = id;
    this.obj = $("#"+id);
    this.get_submit_value = week_get_numbers;

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