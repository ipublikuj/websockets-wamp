/**
 * ipub.websockets.wamp.js
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        iPublikuj:WebSocket!
 * @subpackage     java-script
 * @since          1.0.0
 *
 * @date           26.02.17
 */

/**
 * Client-side script for iPublikuj:WebSocket!
 *
 * @author        Adam Kadlec <adam.kadlec@fastybird.com>
 * @package       iPublikuj:WebSocket!
 * @version       1.0.0
 *
 * @param {jQuery} $ (version > 1.7)
 * @param {Window} window
 */
;(function ($, window) {
    /* jshint laxbreak: true, expr: true */
    'use strict';

    var IPub = window.IPub || {};

    IPub.WebSockets = {};

    /**
     * WebSockets wamp extension definition
     *
     * @param {String} uri
     * @param {Object} options
     */
    IPub.WebSockets.WAMP = function (uri, options) {

        // WS server uri
        this.uri = uri;

        // Autobahn session
        this.session = false;

        this.options = $.extend($.fn.ipubWebSocketsWAMP.defaults, options, {});

        this.events = new IPub.WebSockets.Events();
    };

    IPub.WebSockets.WAMP.prototype =
    {
        /**
         * Connect to WS server
         */
        connect: function () {
            var that = this;

            ab.connect(this.uri,

                // Function on connect
                function (session) {
                    that.session = session;

                    that.events.fire({type: 'socket/connect', data: session});
                },

                // Function on disconnect / error
                function (code, reason) {
                    that.session = false;

                    that.events.fire({type: 'socket/disconnect', data: {code: code, reason: reason}});
                }
            );
        },

        /**
         * Adds a listener for an event type
         *
         * @param {String} type
         * @param {function} listener
         */
        on: function (type, listener) {
            this.events.on(type, listener);
        },

        /**
         * Removes a listener from an event
         *
         * @param {String} type
         * @param {function} listener
         */
        off: function (type, listener) {
            this.events.off(type, listener);
        }
    };

    IPub.WebSockets.Events = function () {

        this.listeners = {};
    };

    IPub.WebSockets.Events.prototype =
    {
        /**
         * Fires an event for all listeners
         *
         * @param {String} event
         */
        fire: function (event) {
            if (typeof event === 'string') {
                event = {
                    type: event
                };
            }

            if (!event.target) {
                event.target = this;
            }

            if (!event.type) {  // Falsy
                throw new Error('Event object missing \'type\' property.');
            }

            if (this.listeners[event.type] instanceof Array) {
                var listeners = this.listeners[event.type];

                for (var i = 0, len = listeners.length; i < len; i++) {
                    listeners[i].call(this, event.data);
                }
            }
        },

        /**
         * Adds a listener for an event type
         *
         * @param {String} type
         * @param {function} listener
         */
        on: function (type, listener) {
            if (typeof this.listeners[type] === 'undefined') {
                this.listeners[type] = [];
            }

            this.listeners[type].push(listener);
        },

        /**
         * Removes a listener from an event
         *
         * @param {String} type
         * @param {function} listener
         */
        off: function (type, listener) {
            if (this.listeners[type] instanceof Array) {
                var listeners = this.listeners[type];

                for (var i = 0, len = listeners.length; i < len; i++) {
                    if (listeners[i] === listener) {
                        listeners.splice(i, 1);

                        break;
                    }
                }
            }
        }
    };

    /**
     * Web socket client initialization
     *
     * @param {String} uri
     * @param {Object} options
     *
     * @returns {Object}
     */
    IPub.WebSockets.WAMP.initialize = function (uri, options) {
        var wamp = new IPub.WebSockets.WAMP(uri, options);

        wamp.connect();

        return wamp;
    }

    /**
     * IPub WebSockets plugin definition
     */

    var old = $.fn.ipubWebSocketsWAMP;

    $.fn.ipubWebSocketsWAMP = function (uri, options) {
        new IPub.WebSockets(uri, options).connect();
    };

    /**
     * IPub WebSockets plugin no conflict
     */

    $.fn.ipubWebSocketsWAMP.noConflict = function () {
        $.fn.ipubWebSocketsWAMP = old;

        return this;
    };

    /**
     * Complete plugin
     */

    // Assign plugin data to DOM
    window.IPub = IPub;

    return IPub;

})(jQuery, window);
