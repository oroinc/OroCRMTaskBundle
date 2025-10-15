import {macros} from 'underscore';
import taskTemplate from 'tpl-loader!orotask/templates/macros/task-reminder-template.html';

macros('reminderTemplates', {
    /**
     * Renders contend for a task reminder massage;
     *
     * @param {Object} data
     * @param {string} data.subject
     * @param {string} data.expireAt
     * @param {string?} data.url
     */
    task_template: taskTemplate
});
