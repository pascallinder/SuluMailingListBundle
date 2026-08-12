import React from 'react';
import {action, observable} from 'mobx';
import {Dialog} from 'sulu-admin-bundle/components';
import {translate} from 'sulu-admin-bundle/utils';
import {AbstractFormToolbarAction} from 'sulu-admin-bundle/views';
import ResourceRequester from 'sulu-admin-bundle/services/ResourceRequester';

export default class SendToolbarAction extends AbstractFormToolbarAction {
    @observable showDialog = false;
    @observable loading = false;
    @observable error = undefined;

    getToolbarItemConfig() {
        return {
            disabled: this.resourceFormStore.dirty
                || this.resourceFormStore.data.sent
                || !this.resourceFormStore.data.readyForSend,
            icon: 'fa-paper-plane',
            label: translate('mailingListSubscription.action.send'),
            loading: this.loading,
            onClick: this.handleClick,
            type: 'button',
        };
    }

    getNode() {
        return (
            <Dialog
                cancelText={translate('sulu_admin.cancel')}
                confirmLoading={this.loading}
                confirmText={translate('mailingListSubscription.action.send')}
                key="newsletter-mail-send-dialog"
                onCancel={this.handleCancel}
                onConfirm={this.handleConfirm}
                open={this.showDialog}
                snackbarMessage={this.error}
                title={translate('mailingListMail.send.title')}
            >
                <p>{translate('mailingListMail.send.description')}</p>
            </Dialog>
        );
    }

    @action handleClick = () => {
        this.error = undefined;
        this.showDialog = true;
    };

    @action handleCancel = () => {
        this.error = undefined;
        this.showDialog = false;
    };

    @action handleConfirm = () => {
        const {
            resourceKey,
            locale,
            data: {
                id,
            },
        } = this.resourceFormStore;

        this.loading = true;
        this.error = undefined;

        ResourceRequester.post(resourceKey, null, {id, locale: locale?.get(), action: 'send'})
            .then(action(() => {
                this.loading = false;
                this.showDialog = false;
                this.form.showSuccessSnackbar();
                this.resourceFormStore.change('sent', true, {isServerValue: true});
            }))
            .catch((error) => {
                error.json().then(action((errorObject) => {
                    this.error = errorObject.error
                        || errorObject.detail
                        || errorObject.title
                        || translate('mailingListMail.send.error');
                    this.loading = false;
                })).catch(action(() => {
                    this.error = translate('mailingListMail.send.error');
                    this.loading = false;
                }));
            });
    };
}
