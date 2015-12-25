/**
 * Created by Administrator on 2015/12/17.
 */
// 路径配置
var path = "/assets/js/echarts/dist";
require.config({
    paths: {
        echarts: path
    }
});

var monitorCount = $('.monitor-item').length;
var monitorChart = {};

//// 使用
var cpuRateChart;//cpu利用率
var memoryChart; //内存使用量
var memoryRateChart; //内存使用率
var portInRateChart; //端口入速率
var portOutRateChart; //端口出速率
var portOnStatusChart; //端口上行状态
var portDownStatusChart; //端口下行状态
var networkChart; //设备网络连通性
require(
    [
        'echarts',
        'echarts/chart/bar', // 使用柱状图就加载bar模块，按需加载
        'echarts/chart/line'
    ],
    function (ec) {
        // 基于准备好的dom，初始化echarts图表
        var index = 0;
        $('.monitor-item').each(function(){
            //alert($(this).attr('id'))
            var id = $(this).attr('id');
            //cpuRateChart = ec.init(this);
            monitorChart[id] = ec.init($('#'+id)[0]);
            //monitorChart.push(ec.init(document.getElementById(id)));
            //alert(monitorChart.length)
        });
        //alert(monitorChart.length)

        //monitorChart["mhes-cup-rate"] = ec.init(document.getElementById('mhes-cup-rate'));
        //monitorChart["mhes-memory"] = ec.init(document.getElementById('mhes-memory'));
        //monitorChart["mhes-memory_rate"] = ec.init(document.getElementById('mhes-memory_rate'));
        //console.log(monitorChart)
        //cpuRateChart = ec.init(document.getElementById('mhes-cup-rate'));
        //memoryChart = ec.init(document.getElementById('mhes-memory'));
        //memoryRateChart = ec.init(document.getElementById('mhes-memory_rate'));
        //portInRateChart = ec.init(document.getElementById('mhes-port-in-rate'));
        //portOutRateChart = ec.init(document.getElementById('mhes-port-out-rate'));
        //portOnStatusChart = ec.init(document.getElementById('mhes-port-on-status'));
        //portDownStatusChart = ec.init(document.getElementById('mhes-port-down-status'));
        //networkChart = ec.init(document.getElementById('mhes-network'));
        //monitorChart["mhes-cup-rate"] = cpuRateChart;
        //monitorChart["mhes-memory"] = memoryChart;
        //monitorChart["mhes-memory_rate"] = memoryRateChart;
    }
);


$(function(){
    $('.mh_sel_customer').bind('change', function(){
        var cid = this.value; //客户ID
        $.ajax({
            type:"GET",
            url:"/assets/api/host_group.json",
            dataType: "json",
            success: function(data){
                var obj = $('.mh_sel_host');
                obj.empty();
                obj.append("<option value=''>--请选择主机组--</option>");
                $.each(data,function(index){
                    obj.append("<option value='" + data[index].id +"'>"+data[index].name+"</option>");
                });
            }
        });
    });

    $('.mh_sel_host').bind('change', function(){
//                alert($('select.mh_sel_customer').val())
        var cid = $('select.mh_sel_customer').val(); //客户ID
//                alert(cid)
        var gid = this.value;//主机组ID
        $.ajax({
            type:"GET",
            url:"/assets/api/host.json",
            dataType: "json",
            success: function(data){
                var obj = $('.mh-box ul.list-unstyled');
                var ulHtml = '';
                $.each(data,function(index){
                    var li = '<li id='+data[index].id;
                    if (index == 0) {
                        li += ' class="active"'
                    }
                    //"onSelectHost(\''+data[index].id+'\')"
                    li += '><a href="javascript:;" onclick="onClickHost(this)"';
                    if (data[index].status == 1) {//Off
                        li += 'class="mh-a-off"><i class="fa mh-i-off">Off</i>'
                    } else {
                        li += '><i class="fa">On</i>'
                    }
                    li += '<span>'+data[index].name+'</span></a></li>';
                    ulHtml += li;
                });
                obj.html(ulHtml);
                onDefaultHost();
            }
        });
    });
});

//点击主机
function onClickHost(t) {
    onSelectHost($(t).parent().attr("id"))
};
//选中主机
function onSelectHost(id) {
    $('ul.list-unstyled').find('li').removeClass('active');
    $('ul.list-unstyled #'+id).addClass('active');

    var cid = $('select.mh_sel_customer').val(); //客户ID
    var gid = $('select.mh_sel_host').val(); //主机组ID

    for (var i in monitorChart) {
        var url = "/assets/api/cpu_rate.json";
        if (i == 'mhes-cup-rate') {
            url = "/assets/api/memory.json"
        }

        alert(i)
        var chart = monitorChart[i]
        $.ajax({
            type:"GET",
            url:url,
            dataType: "json",
            success: function(data){
                if (data != undefined) {
                    if (data.toolbox == undefined) {
                        data.toolbox = {}
                    }
                    if (data.toolbox.feature == undefined) {
                        data.toolbox.feature = {}
                    }
                    data.toolbox.feature.myTool = {
                        show : true,
                        title : '查看历史',
                        icon : '/assets/img/logo.png',
                        onclick : function (){
                            window.location.href = "monitorhosthis.html?hid="+id+'&cid='+cid+'&gid='+gid
                        }
                    }
                }

                //console.log(data);
                chart.setOption(data);
            }
        });
    }


    //$.ajax({type:"GET", url:"/assets/api/memory.json", dataType: "json",
    //    success: function(data){memoryChart.setOption(data);}
    //});
    //$.ajax({type:"GET", url:"/assets/api/memory.json", dataType: "json",
    //    success: function(data){memoryRateChart.setOption(data);}
    //});
    //$.ajax({type:"GET", url:"/assets/api/cpu_rate.json", dataType: "json",
    //    success: function(data){portInRateChart.setOption(data);}
    //});
    //$.ajax({type:"GET", url:"/assets/api/memory.json", dataType: "json",
    //    success: function(data){portOutRateChart.setOption(data);}
    //});
    //$.ajax({type:"GET", url:"/assets/api/cpu_rate.json", dataType: "json",
    //    success: function(data){portOnStatusChart.setOption(data);}
    //});
    //$.ajax({type:"GET", url:"/assets/api/memory.json", dataType: "json",
    //    success: function(data){portDownStatusChart.setOption(data);}
    //});
    //$.ajax({type:"GET", url:"/assets/api/cpu_rate.json", dataType: "json",
    //    success: function(data){networkChart.setOption(data);}
    //});
}
//默认选中
function onDefaultHost () {
    var id = $('ul.list-unstyled li.active').attr("id");
    if (id == undefined) {
        return
    }
    onSelectHost(id);
};
window.onload=onDefaultHost;
$(".select2el").select2();
$('#mh-widget').scrollToFixed({marginTop: 143});