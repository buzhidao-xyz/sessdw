/**
 * Created by Administrator on 2015/12/17.
 */


var ECharts = {
    ChartConfig: function (container, option) { //container:为页面要渲染图表的容器，option为已经初始化好的图表类型的option配置
        var chart_path = "/assets/js/echarts/dist"; //配置图表请求路径
        require.config({//引入常用的图表类型的配置
            paths: {
                echarts: chart_path
            }
        });
        this.option = { chart: {}, option: option, container: container };
        return this.option;
    },
    Charts: {
        RenderChart: function  (op) {
            require([
                    'echarts',
                    'echarts/chart/line',
                    'echarts/chart/bar'
                ],
                function (ec) {
                    var echarts = ec;
                    if (op.chart && op.chart.dispose)
                        op.chart.dispose();
                    op.chart = echarts.init(op.container);
                    window.onresize = op.chart.resize;
                    op.chart.setOption(op.option, true);
                });
        }
    }
};
var EChartsItems = new Array(); //监控图表集合
var IntervalTime = 1*1000; //定时间隔
var intervalId; //定时器ID
var isMonitorHis = false;

////点击主机
//function onClickHost(t) {
//    onSelectHost($(t).parent().attr("id"))
//}



var lastData = 11;
var axisData;
function getHostUpdateDataTest() {
    lastData += Math.random() * ((Math.round(Math.random() * 10) % 2) == 0 ? 1 : -1);
    lastData = lastData.toFixed(1) - 0;
    axisData = (new Date()).toLocaleTimeString().replace(/^\D*/,'');
    var temp = [
        [
            0,        // 系列索引
            Math.round(Math.random() * 1000), // 新增数据
            true,     // 新增数据是否从队列头部插入
            false     // 是否增加队列长度，false则自定删除原有数据，队头插入删队尾，队尾插入删队头
        ],
        [
            1,        // 系列索引
            lastData, // 新增数据
            false,    // 新增数据是否从队列头部插入
            false,    // 是否增加队列长度，false则自定删除原有数据，队头插入删队尾，队尾插入删队头
            axisData  // 坐标轴标签
        ]
    ];
    return temp;
}

//初始化监控图表
function initHostMonitor () {
    //var hostId = $('ul.monitor-host-item li.active').attr("id");
    //if (hostId == undefined) {
    //    return
    //}
    //updateHostMonitorHtml(hostId);
    addMonitorEChartsMore();
    showMonitorEChartsInterval();
}
$('#mh-widget').scrollToFixed({marginTop: 143});


//var strjson = $.toJSON(option);
//$('.json').html(strjson);
function getMonitorIdBy(e) {
    var monitorId = $(e).attr('id');
    if (monitorId == undefined) {
        return undefined
    }
    var index = monitorId.indexOf('m_');
    if (index != -1) {
        monitorId = monitorId.substring(index+2);
    }
    return monitorId;
}
//初始化主机监控历史图表
function initHostMonitorHis() {
    var monitorId = getMonitorIdBy('ul.monitor-his-item li.active');
    if (monitorId == undefined) {
        return
    }
    isMonitorHis = true;
    //alert(monitorId);
    EChartsItems = new Array();
    addMonitorEChartsSole(monitorId);
}
//添加监控项图表
function addMonitorECharts(monitorId) {
    //alert(monitorId)

    var cid = $('select.mh_sel_customer').val(); //客户ID
    var gid = $('select.mh_sel_host').val(); //主机组ID
    var hostId = $('ul.monitor-host-item li.active').attr("id"); //主机ID

    $.ajax({
        type:"GET",
        url:"/assets/api/cpu_rate.json",
        dataType: "json",
        success: function(data){
            if (data == undefined || data == null) {
                return
            }
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
                    window.location.href = "monitorhosthis.html?id="+monitorId//TODO::可加上上述ID
                }
            };
            if (EChartsItems[monitorId] == undefined) {
                EChartsItems[monitorId] = {
                    ECharts: ECharts,
                    Op : ECharts.ChartConfig($('#'+monitorId)[0], data)
                };
            } else {
                EChartsItems[monitorId].Op = EChartsItems[monitorId].ECharts.ChartConfig($('#'+monitorId)[0], data);
            }
            EChartsItems[monitorId].ECharts.Charts.RenderChart(EChartsItems[monitorId].Op);
        }
    });
}
function stopMonitorECharts() {
    clearInterval(intervalId);
    EChartsItems = new Array();
}
function addMonitorEChartsMore() {
    $('.monitor-item-data').each(function(){
        addMonitorECharts($(this).attr('id'));
    });
}
//新增并清除原有
function addMonitorEChartsSole(monitorId) {
    $.ajax({
        type:"GET",
        url:"/assets/api/cpu_rate.json",
        dataType: "json",
        success: function(data){
            if (data == undefined || data == null) {
                return
            }
            EChartsItems[monitorId] = {
                ECharts: ECharts,
                Op : ECharts.ChartConfig($('#monitor-his-item-data')[0], data)
            };
            EChartsItems[monitorId].ECharts.Charts.RenderChart(EChartsItems[monitorId].Op);
        }
    });
}

//定时展示监控项
function showMonitorEChartsInterval() {
    intervalId = setInterval(function (){
        for (var id in EChartsItems) {
            var temp = getHostUpdateDataTest();
            EChartsItems[id].Op.chart.addData(temp);
            //var data = getHostUpdateData(id);
            //if (data != undefined) {
            //    EChartsItems[id].Op.chart.addData(data);
            //}
        }
    }, IntervalTime);
}

//通过主机更新图列表HTML
function updateHostMonitorHtml(hostId) {
    $.ajax({
        type:"GET",
        url:"/assets/api/monitor.json",
        dataType: "json",
        success: function(data){
            //TODO::更新文本信息Table

            //更新图表信息
            var obj = $('.monitor-items');
            var ulHtml = '';
            $.each(data,function(index){
                ulHtml += '<div id=m_'+data[index].id + ' class="col-xs-6 ec-h-300 monitor-item-data"></div>';
            });
            obj.html(ulHtml);
        }
    });
}
//点击主机事件
function onClickHost(e) {
    var hostId = $(e).parents('li').attr('id');
    var index = hostId.indexOf('h_');
    if (index != -1) {
        hostId = hostId.substring(index+2);
    }
    //alert(hostId);
    $('ul.monitor-host-item').find('li').removeClass('active');
    $('ul.monitor-host-item #'+hostId).addClass('active');

    stopMonitorECharts();
    updateHostMonitorHtml(hostId);

    //var cid = $('select.mh_sel_customer').val(); //客户ID
    //var gid = $('select.mh_sel_host').val(); //主机组ID

    setTimeout(function(){
        addMonitorEChartsMore();
        showMonitorEChartsInterval();
    }, 50);
}
//点击监控图表
function onClickMonitor(e) {
    //var monitorId = $(e).attr('id');
    //
    //var index = monitorId.indexOf('m_');
    //if (index != -1) {
    //    monitorId = monitorId.substring(index+2);
    //}
    var monitorId = getMonitorIdBy(e);
    if (monitorId == undefined) {
        return
    }
    //alert(monitorId)
    EChartsItems = new Array();
    addMonitorEChartsSole(monitorId);
}
function updateHostGroupHtml(data) {
    var obj = $('.mh_sel_host');
    obj.empty();
    obj.append("<option value=''>--请选择主机组--</option>");
    if (data == undefined || data == null) {
        return
    }
    $.each(data,function(index){
        obj.append("<option value='" + data[index].id +"'>"+data[index].name+"</option>");
    });
}
function updateHostUlHtml(data) {
    if (isMonitorHis) {
        return
    }
    var obj = $('ul.monitor-host-item');
    var ulHtml = '';
    $.each(data,function(index){
        var li = '<li id=h_'+data[index].id;
        if (index == 0) {
            li += ' class="active"'
        }
        li += '><a href="javascript:;"  onclick="onClickHost(this)"';
        if (data[index].status == 1) {//Off
            li += ' class="mh-a-off"><i class="fa mh-i-off">Off</i>'
        } else {
            li += ' ><i class="fa">On</i>'
        }
        li += ' <span>'+data[index].name+'</span></a></li>';
        ulHtml += li;
    });
    obj.html(ulHtml);
}
function updateHostSelectHtml(data) {
    if (!isMonitorHis) {
        return
    }
    var obj = $('.mh_sel_h');
    obj.empty();
    obj.append("<option value=''>--请选择主机--</option>");
    if (data == undefined || data == null) {
        return
    }
    $.each(data,function(index){
        obj.append("<option value='" + data[index].id +"'>"+data[index].name+"</option>");
    });
}

function updateHostMonitorTableHtml(data) {
    var obj = $('ul.monitor-his-item');
    var ulHtml = '';
    $.each(data,function(index){
        var li = '<li id=m_'+data[index].id;
        li += ' class="tab-red'
        if (index == 0) {
            li += '  active'
        }
        li += '" onclick="onClickMonitor(this)"><a data-toggle="tab" aria-expanded="true">';
        li += data[index].name;
        li += ' </a></li>';
        ulHtml += li;
    });
    obj.html(ulHtml);
}
$(function(){
    $('.mh_sel_customer').bind('change', function(){
        var cid = this.value; //客户ID
        if (cid == '') {
            updateHostGroupHtml(null);
            updateHostSelectHtml(null);
            return
        }
        $.ajax({
            type:"GET",
            url:"/assets/api/host_group.json",
            dataType: "json",
            success: function(data){
                updateHostGroupHtml(data);
            }
        });
    });

    $('.mh_sel_host').bind('change', function(){
        var cid = $('select.mh_sel_customer').val(); //客户ID
        var gid = this.value;//主机组ID
        if (gid == '') {
            updateHostSelectHtml(null);
            return
        }
        $.ajax({
            type:"GET",
            url:"/assets/api/host.json",
            dataType: "json",
            success: function(data){
                updateHostUlHtml(data);
                updateHostSelectHtml(data);
            }
        });
    });
    $('.mh_sel_h').bind('change', function(){
        var hid = this.value;//主机ID
        if (hid == '') {
            return
        }
        //alert(hid)
        $.ajax({
            type:"GET",
            url:"/assets/api/monitor.json",
            dataType: "json",
            success: function(data){
                updateHostMonitorTableHtml(data);

                //默认选中第一个监控图表
                EChartsItems = new Array();
                var monitorId = getMonitorIdBy('ul.monitor-his-item li.active');
                if (monitorId == undefined || monitorId == '') {
                    return
                }
                addMonitorEChartsSole(monitorId);
            }
        });
    });
    ////选择主机 若主机列表HTML更新后，则绑定无效
    //$('ul.monitor-host-item li a').bind('click', function(){
    //    var hostId = $(this).parents('li').attr('id');
    //    alert(hostId);
    //    $('ul.monitor-host-item').find('li').removeClass('active');
    //    $('ul.monitor-host-item #'+hostId).addClass('active');
    //
    //    stopMonitorECharts();
    //    updateHostMonitorHtml(hostId);
    //
    //    //var cid = $('select.mh_sel_customer').val(); //客户ID
    //    //var gid = $('select.mh_sel_host').val(); //主机组ID
    //
    //    setTimeout(function(){
    //        addMonitorEChartsMore();
    //        showMonitorEChartsInterval();
    //    }, 50);
    //});



    //历史 监控项点击
    //$('ul.monitor-his-item li').bind('click', function(){
    //    addMonitorEChartsSole($(this).attr('id'));
    //    showMonitorEChartsInterval();
    //});
});



//function onDefault() {
//    onDefaultHost();
//    onDefaultMonitor();
//}



$(".select2el").select2();


