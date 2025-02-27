define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'light/device/settings/index' + location.search,
                    add_url: 'light/device/settings/add',
                    edit_url: 'light/device/settings/edit',
                    del_url: 'light/device/settings/del',
                    multi_url: 'light/device/settings/multi',
                    import_url: 'light/device/settings/import',
                    table: 'light_device_settings',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'deviceId', title: __('Deviceid'), operate: 'LIKE'},
                        {field: 'phaseBroken', title: __('Phasebroken')},
                        {field: 'highVoltageA', title: __('Highvoltagea'), operate:'BETWEEN'},
                        {field: 'lowVoltageA', title: __('Lowvoltagea'), operate:'BETWEEN'},
                        {field: 'highVoltageB', title: __('Highvoltageb'), operate:'BETWEEN'},
                        {field: 'lowVoltageB', title: __('Lowvoltageb'), operate:'BETWEEN'},
                        {field: 'highVoltageC', title: __('Highvoltagec'), operate:'BETWEEN'},
                        {field: 'lowVoltageC', title: __('Lowvoltagec'), operate:'BETWEEN'},
                        {field: 'highCurrentA', title: __('Highcurrenta')},
                        {field: 'highCurrentB', title: __('Highcurrentb')},
                        {field: 'highCurrentC', title: __('Highcurrentc')},
                        {field: 'highAd1Voltage', title: __('Highad1voltage')},
                        {field: 'lowAd1Voltage', title: __('Lowad1voltage')},
                        {field: 'highAd2Voltage', title: __('Highad2voltage')},
                        {field: 'lowAd2Voltage', title: __('Lowad2voltage')},
                        {field: 'oc1', title: __('Oc1')},
                        {field: 'oc2', title: __('Oc2')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
