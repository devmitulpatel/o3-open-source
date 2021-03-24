/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue'
import Nova from './Nova'
import './plugins'
import Localization from '@/mixins/Localization'
import ThemingClasses from '@/mixins/ThemingClasses'
/**
 * Next, we'll setup some of Nova's Vue components that need to be global
 * so that they are always available. Then, we will be ready to create
 * the actual Vue instance and start up this JavaScript application.
 */
import './fields'
import './components';

Vue.config.productionTip = false

Vue.mixin(Localization)

/**
 * If configured, register a global mixin to add theming-friendly CSS
 * classnames to Nova's built-in Vue components. This allows the user
 * to fully customize Nova's theme to their project's branding.
 */
if (window.config.themingClasses) {
  Vue.mixin(ThemingClasses)
}

(function () {
  this.CreateNova = function (config) {
    return new Nova(config)
  }
}.call(window))
