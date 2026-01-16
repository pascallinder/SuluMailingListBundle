import {action,observable ,computed} from 'mobx';
import jexl from 'jexl';
import React from 'react';
import {translate} from 'sulu-admin-bundle/utils';
import {AbstractListToolbarAction} from 'sulu-admin-bundle/views';
import ResourceRequester, {RequestPromise} from 'sulu-admin-bundle/services/ResourceRequester';
export default class SubscribeToolbarAction extends AbstractListToolbarAction {
    getToolbarItemConfig() {

        return {
            disabled: this.listStore.selections.some(item => !item.isUnsubscribed)
                || this.listStore.selectionIds.length === 0,
            icon: 'fa-user-plus',
            label: translate('mailingListSubscription.action.subscribe'),
            onClick: this.handleClick,
            type: 'button',
        };
    }

    @action handleClick = () => {
        const updatePromises = [];
        const store = this.listStore;
        store.selectionIds.forEach((id) => {
            updatePromises.push(
                ResourceRequester.post(this.listStore.resourceKey, null,{id: id,action: 'subscribe'})
                    .catch((error) => {
                        if (error.status !== 404) {
                            return Promise.reject(error);
                        }
                    })
            );
        });
        Promise.all(updatePromises)
            .then(action(() => {
                store.clearSelection();
                store.reload();
            }))
            .catch(action((error) => {
                return Promise.reject(error);
            }));
    };
}
