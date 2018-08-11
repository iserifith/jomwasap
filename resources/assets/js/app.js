
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


import $ from 'jquery';
window.$ = window.jQuery = $;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

import Chart from 'chart.js';

const ClipboardJS = require('clipboard');
var clipboard = new ClipboardJS('.btn');

clipboard.on('success', function(e){
    var button = $(e.trigger).tooltip({
	  trigger: 'click',
	  placement: 'left'
	});;
    showTooltip(button, 'Link Copied!');
  	hideTooltip(button);
})

function showTooltip(button, message) {
  button.attr('data-original-title', message).tooltip('show');
}

function hideTooltip(button) {
  setTimeout(function() {
    button.tooltip('hide');
  }, 1000);
}

