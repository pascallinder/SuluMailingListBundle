import {listToolbarActionRegistry,     formToolbarActionRegistry} from 'sulu-admin-bundle/views';
import UnsubscribeToolbarAction from "./components/list/toolbar/button/UnsubscribeToolbarAction";
import SubscribeToolbarAction from "./components/list/toolbar/button/SubscribeToolbarAction";
import SendToolbarAction from "./components/form/toolbar/button/SendToolbarAction";

listToolbarActionRegistry.add('app.newsletter-subscription.unsubscribe', UnsubscribeToolbarAction);
listToolbarActionRegistry.add('app.newsletter-subscription.subscribe', SubscribeToolbarAction);
formToolbarActionRegistry.add('app.newsletter-subscription.send', SendToolbarAction);