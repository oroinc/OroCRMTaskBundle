define(function(require) {
    'use strict';

    const __ = require('orotranslation/js/translator');
    const BaseWidgetSetupView = require('orosidebar/js/app/views/base-widget/base-widget-setup-view');

    const AssignedTasksSetupView = BaseWidgetSetupView.extend({
        template: require('tpl-loader!orotask/templates/sidebar-widget/assigned-tasks/assigned-tasks-setup-view.html'),

        widgetTitle: function() {
            return __('oro.task.assigned_tasks_widget.settings');
        },

        /**
         * @inheritdoc
         */
        constructor: function AssignedTasksSetupView(options) {
            AssignedTasksSetupView.__super__.constructor.call(this, options);
        },

        validation: {
            perPage: {
                NotBlank: {},
                Regex: {pattern: '/^\\d+$/'},
                Number: {min: 1, max: 20}
            }
        },

        fetchFromData: function() {
            const data = AssignedTasksSetupView.__super__.fetchFromData.call(this);
            data.perPage = Number(data.perPage);
            return data;
        }
    });

    return AssignedTasksSetupView;
});
