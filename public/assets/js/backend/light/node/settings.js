define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'light/node/settings/index' + location.search,
                    add_url: 'light/node/settings/add',
                    edit_url: 'light/node/settings/edit',
                    del_url: 'light/node/settings/del',
                    multi_url: 'light/node/settings/multi',
                    import_url: 'light/node/settings/import',
                    table: 'light_node_settings',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'deviceId', title: __('Deviceid'), operate: 'LIKE'},
                        {field: 'nodeId', title: __('Nodeid'), operate: 'LIKE'},
                        {field: 'highLeakCurrent', title: __('Highleakcurrent')},
                        {field: 'highInputVoltage', title: __('Highinputvoltage')},
                        {field: 'lowInputVoltage', title: __('Lowinputvoltage')},
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
