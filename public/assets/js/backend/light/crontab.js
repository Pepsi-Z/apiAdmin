define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'light/crontab/index' + location.search,
                    add_url: 'light/crontab/add',
                    edit_url: 'light/crontab/edit',
                    del_url: 'light/crontab/del',
                    multi_url: 'light/crontab/multi',
                    import_url: 'light/crontab/import',
                    table: 'light_crontab',
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
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'beginDate', title: __('Begindate'), operate: 'LIKE'},
                        {field: 'endDate', title: __('Enddate'), operate: 'LIKE'},
                        {field: 'actionSchema', title: __('Actionschema')},
                        {field: 'actionWeekDay', title: __('Actionweekday')},
                        {field: 'computerMode', title: __('Computermode')},
                        {field: 'executeTime', title: __('Executetime'), operate: 'LIKE'},
                        {field: 'offsetTime', title: __('Offsettime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'crontab_id', title: __('Crontab_id')},
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
