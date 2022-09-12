import {macros} from 'underscore';

macros('reminderTemplates', {
    /**
     * Renders contend for a task reminder massage;
     *
     * @param {Object} data
     * @param {string} data.subject
     * @param {string} data.expireAt
     * @param {string?} data.url
     */
    task_template: require('tpl-loader!orotask/templates/macros/task-reminder-template.html')
});
