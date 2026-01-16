import {action} from 'mobx';
import React from 'react';
import {translate} from 'sulu-admin-bundle/utils';
import {AbstractFormToolbarAction} from 'sulu-admin-bundle/views';
import ResourceRequester from 'sulu-admin-bundle/services/ResourceRequester';
export default class SendToolbarAction extends AbstractFormToolbarAction {

    getToolbarItemConfig() {
        return {
            disabled: this.resourceFormStore.dirty ||this.resourceFormStore.data.sent || !this.resourceFormStore.data.readyForSend,
            icon: 'fa-paper-plane',
            label: translate('mailingListSubscription.action.send'),
            onClick: this.handleClick,
            type: 'button',
        };
    }

    handleClick = () => {
        const {
            resourceKey,
            locale,
            data: {
                id,
            },
        } = this.resourceFormStore;
        this.loading = true;
       ResourceRequester.post(resourceKey, null,{id: id, locale:locale,action: 'send'})
           .then(action((response) => {
               this.loading = false;
               this.form.showSuccessSnackbar();
               this.resourceFormStore.change('sent', true, {isServerValue: true});
           }))
           .catch(action((error) => {
                error.json().then(action((errorObject) => {
                    this.form.errors.push(errorObject.error);
                }));
            }));
    };
}
