import React from 'react';
import {action, observable} from 'mobx';
import {Dialog, Input} from 'sulu-admin-bundle/components';
import {translate} from 'sulu-admin-bundle/utils';
import {AbstractFormToolbarAction} from 'sulu-admin-bundle/views';

export default class SendTestToolbarAction extends AbstractFormToolbarAction {
    @observable showDialog = false;
    @observable recipient = '';
    @observable loading = false;
    @observable error = undefined;

    getToolbarItemConfig() {
        return {
            disabled: this.resourceFormStore.dirty
                || !this.resourceFormStore.id
                || this.resourceFormStore.saving,
            icon: 'fa-flask',
            label: translate('mailingListMail.action.sendTest'),
            loading: this.loading,
            onClick: this.handleClick,
            type: 'button',
        };
    }

    getNode() {
        return (
            <Dialog
                cancelText={translate('sulu_admin.cancel')}
                confirmDisabled={!this.recipient}
                confirmLoading={this.loading}
                confirmText={translate('mailingListMail.action.sendTest')}
                key="newsletter-mail-test-dialog"
                onCancel={this.handleCancel}
                onConfirm={this.handleConfirm}
                open={this.showDialog}
                snackbarMessage={this.error}
                title={translate('mailingListMail.testMail.title')}
            >
                <p>{translate('mailingListMail.testMail.description')}</p>
                <Input
                    icon="su-mail"
                    onChange={this.handleRecipientChange}
                    type="email"
                    value={this.recipient}
                />
            </Dialog>
        );
    }

    @action handleClick = () => {
        if (!this.resourceFormStore.validate()) {
            return;
        }

        this.error = undefined;
        this.showDialog = true;
    };

    @action handleCancel = () => {
        this.showDialog = false;
        this.error = undefined;
    };

    @action handleRecipientChange = (recipient) => {
        this.recipient = recipient;
        this.error = undefined;
    };

    @action handleConfirm = async() => {
        this.loading = true;
        this.error = undefined;

        try {
            const response = await fetch('/admin/api/newsletters-mails/test', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: this.resourceFormStore.id,
                    locale: this.resourceFormStore.locale?.get(),
                    recipient: this.recipient,
                }),
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.detail || error.title || translate('mailingListMail.testMail.error'));
            }

            action(() => {
                this.loading = false;
                this.showDialog = false;
                this.form.showSuccessSnackbar();
            })();
        } catch (error) {
            action(() => {
                this.error = error.message || translate('mailingListMail.testMail.error');
                this.loading = false;
            })();
        }
    };
}
